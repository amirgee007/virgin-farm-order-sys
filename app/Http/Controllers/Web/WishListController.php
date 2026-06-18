<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Product;
use Vanguard\Models\WishList;
use Vanguard\Models\WishListItem;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class WishListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function view()
    {
        try {
            if (in_array(myRoleName(), ['Admin', 'SalesRep'])) {
                return redirect()->route('wishlist.manage');
            }

            $wishList = $this->getOrCreateDraft();
            $items = $wishList->items()->with('product')->orderByDesc('id')->get();

            $pastWishLists = WishList::mine()
                ->where('id', '!=', $wishList->id)
                ->with('items')
                ->orderByDesc('id')
                ->paginate(10);

            return view('wish-list.view', compact('wishList', 'items', 'pastWishLists'));
        } catch (\Exception $ex) {
            Log::error('Error in WishList view: ' . $ex->getMessage());
        }
    }

    public function browse(Request $request)
    {
        try {
            $search = trim((string) $request->get('q'));

            $products = Product::query()
                ->leftJoin('colors_class', 'products.color_id', '=', 'colors_class.id')
                ->select([
                    'products.*',
                    'colors_class.color as color_name',
                    'colors_class.description as color_description',
                ])
                ->when($search !== '', function ($q) use ($search) {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('products.item_no', 'like', "%{$search}%")
                           ->orWhere('products.product_text', 'like', "%{$search}%");
                    });
                })
                ->orderBy('products.product_text')
                ->paginate(50)
                ->withQueryString();

            $wishList = $this->getOrCreateDraft();

            return view('wish-list.browse', compact('products', 'wishList', 'search'));
        } catch (\Exception $ex) {
            Log::error('Error in WishList browse: ' . $ex->getMessage());
        }
    }

    public function index()
    {
        try {
            $wishLists = WishList::mine()
                ->with('items')
                ->orderByDesc('id')
                ->paginate(20);

            return view('wish-list.index', compact('wishLists'));
        } catch (\Exception $ex) {
            Log::error('Error in WishList index: ' . $ex->getMessage());
        }
    }

    public function addToWishList(Request $request)
    {
        try {
            if (!auth()->user()->is_approved) {
                return redirect()->back()->with('success', 'Please ask admin to approve your account!');
            }

            $request->validate([
                'product_id' => 'required|integer',
                'quantity'   => 'required|integer|min:1',
            ]);

            $product = Product::find($request->product_id);
            if (!$product) {
                session()->flash('app_error', 'Product not found.');
                return back();
            }

            $wishList = $this->getOrCreateDraft();

            $existing = WishListItem::where('wish_list_id', $wishList->id)
                ->where('product_id', $product->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $request->quantity);
            } else {
                WishListItem::create([
                    'wish_list_id' => $wishList->id,
                    'product_id'   => $product->id,
                    'item_no'      => $product->item_no,
                    'name'         => $product->product_text,
                    'quantity'     => $request->quantity,
                    'image'        => $product->image_url,
                    'size'         => $product->size,
                    'stems'        => $product->stems,
                ]);
            }
        } catch (\Exception $ex) {
            Log::error('Error in addToWishList: ' . $ex->getMessage());
        }

        return redirect()->back()->with('success', 'Product added to wish list.');
    }

    public function updateItemQty(Request $request)
    {
        try {
            $request->validate([
                'id'       => 'required|integer',
                'quantity' => 'required|integer|min:1',
            ]);

            $wishList = $this->getOrCreateDraft();
            $item = WishListItem::where('wish_list_id', $wishList->id)
                ->where('id', $request->id)
                ->first();

            if ($item) {
                $item->update(['quantity' => $request->quantity]);
                session()->flash('app_message', 'Wish list quantity updated.');
            }
        } catch (\Exception $ex) {
            Log::error('Error in updateItemQty: ' . $ex->getMessage());
        }

        return back();
    }

    public function removeItem(Request $request)
    {
        try {
            $wishList = $this->getOrCreateDraft();
            WishListItem::where('wish_list_id', $wishList->id)
                ->where('id', $request->id)
                ->delete();
        } catch (\Exception $ex) {
            Log::error('Error in removeItem: ' . $ex->getMessage());
        }

        session()->flash('success', 'Item removed from wish list.');
        return back();
    }

    public function emptyWishList()
    {
        try {
            $wishList = WishList::mineDraft()->first();
            if ($wishList) {
                $wishList->items()->delete();
            }
        } catch (\Exception $ex) {
            Log::error('Error in emptyWishList: ' . $ex->getMessage());
        }

        session()->flash('success', 'Wish list emptied.');
        return back();
    }

    public function saveNotes(Request $request)
    {
        try {
            $wishList = $this->getOrCreateDraft();
            $wishList->update(['notes' => $request->notes]);
        } catch (\Exception $ex) {
            Log::error('Error in saveNotes: ' . $ex->getMessage());
        }
    }

    public function submit(Request $request)
    {
        try {
            $request->validate([
                'request_date' => 'required|date|after_or_equal:today',
                'notes'        => 'nullable|string|max:2000',
            ]);

            $wishList = WishList::mineDraft()->with('items')->first();

            if (!$wishList || $wishList->items->isEmpty()) {
                session()->flash('app_error', 'Your wish list is empty.');
                return back();
            }

            $wishList->update([
                'sales_rep'    => auth()->user()->sales_rep,
                'request_date' => $request->request_date,
                'notes'        => $request->notes,
                'status'       => 'submitted',
                'submitted_at' => now(),
            ]);

            // TODO: send WishListSubmittedMail to sales (mirrors checkOutCart recipients).
            addOwnNotification('Your wish list has been submitted.', null, $wishList->user_id);
            addOwnNotification('New Wish List submitted: WL-' . $wishList->id, null);

            session()->flash('success', 'Wish list submitted. Sales will follow up with a quote.');
            return redirect()->route('wishlist.history');
        } catch (\Exception $ex) {
            Log::error('Error in submit wish list: ' . $ex->getMessage());
            session()->flash('app_error', 'Something went wrong, please check with admin.');
            return back();
        }
    }

    public function manage(Request $request)
    {
        try {
            $search    = $request->get('search');
            $userId    = $request->get('user_id');
            $salesRep  = $request->get('sales_rep');
            $status    = $request->get('status');

            $isAdmin = myRoleName() == 'Admin';
            $isSalesRep = myRoleName() == 'SalesRep';

            if (!$isAdmin && !$isSalesRep) {
                abort(403);
            }

            $salesReps    = getSalesReps();
            $salesRepIds  = getSalesReps(true);

            if ($isSalesRep) {
                $salesRep = auth()->user()->sales_rep;
                $salesReps = array_filter($salesReps, fn ($rep) => $rep === $salesRep);
            }

            $salesRepName = null;
            $salesRepId   = null;
            if ($salesRep && $salesRep !== '0') {
                if (is_numeric($salesRep)) {
                    $salesRepId   = (int) $salesRep;
                    $salesRepName = array_search($salesRepId, $salesRepIds, true)
                        ?: User::whereKey($salesRepId)->value('sales_rep');
                } else {
                    $salesRepName = $salesRep;
                    $salesRepId   = $salesRepIds[$salesRepName] ?? null;
                }
            }

            $query = WishList::with(['items', 'user'])
                ->where('status', '!=', 'draft')
                ->latest();

            if ($status) {
                $query->where('status', $status);
            }

            if ($userId) {
                $query->where('user_id', $userId);
            }

            if ($salesRepName || $salesRepId) {
                $query->where(function ($q) use ($salesRepName, $salesRepId) {
                    if ($salesRepName) {
                        $q->where('sales_rep', $salesRepName);
                    }
                    if ($salesRepId) {
                        $q->orWhere('user_id', $salesRepId);
                    }
                });
            }

            if ($search) {
                if (str_starts_with(strtoupper($search), 'WL')) {
                    $id = str_ireplace('WL-', '', $search);
                    $id = str_ireplace('WL', '', $id);
                    $query->where('id', $id);
                } else {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            }

            $users = [0 => 'Show All'] + User::where('status', UserStatus::ACTIVE)
                ->orderBy('first_name')
                ->pluck('first_name', 'id')
                ->toArray();

            $count = (clone $query)->count();
            $wishLists = $query->paginate(20)->withQueryString();

            return view('wish-list.manage', compact(
                'wishLists', 'count', 'users', 'salesReps', 'isAdmin', 'search', 'status'
            ));
        } catch (\Exception $ex) {
            Log::error('Error in WishList manage: ' . $ex->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $wishList = WishList::with(['items.product', 'user'])->findOrFail($id);

            $isAdmin = myRoleName() == 'Admin';
            $isSalesRep = myRoleName() == 'SalesRep';

            if (!$isAdmin && !$isSalesRep && $wishList->user_id !== auth()->id()) {
                abort(403);
            }

            if ($isSalesRep && $wishList->sales_rep !== auth()->user()->sales_rep) {
                abort(403);
            }

            return view('wish-list.show', compact('wishList'));
        } catch (\Exception $ex) {
            Log::error('Error in WishList show: ' . $ex->getMessage());
        }
    }

    public function customerDecisions(Request $request, $id)
    {
        try {
            $wishList = WishList::with('items')->findOrFail($id);

            if ($wishList->user_id !== auth()->id()) {
                abort(403);
            }

            if (!in_array($wishList->status, ['quoted', 'confirmed'], true)) {
                session()->flash('app_error', 'You can only respond after sales has marked items available.');
                return back();
            }

            $decisions = $request->input('customer_decisions', []);
            $changed = false;

            foreach ($wishList->items as $item) {
                if ($item->approval_status !== 'approved') {
                    continue;
                }

                $choice = $decisions[$item->id] ?? null;
                if (!in_array($choice, ['accepted', 'rejected'], true)) {
                    continue;
                }

                $item->update([
                    'customer_decision'   => $choice,
                    'customer_decided_at' => now(),
                ]);
                $changed = true;
            }

            if ($changed) {
                addOwnNotification('Customer responded on Wish List WL-' . $wishList->id, null);
                session()->flash('success', 'Thanks — your response has been sent to sales.');
            }
        } catch (\Exception $ex) {
            Log::error('Error in customerDecisions: ' . $ex->getMessage());
            session()->flash('app_error', 'Something went wrong saving your response.');
        }

        return back();
    }

    public function saveDecisions(Request $request, $id)
    {
        try {
            if (!in_array(myRoleName(), ['Admin', 'SalesRep'])) {
                abort(403);
            }

            $wishList = WishList::with('items')->findOrFail($id);

            if (myRoleName() === 'SalesRep' && $wishList->sales_rep !== auth()->user()->sales_rep) {
                abort(403);
            }

            $decisions = $request->input('decisions', []);

            foreach ($wishList->items as $item) {
                $row = $decisions[$item->id] ?? null;
                if (!$row) {
                    continue;
                }

                $status = in_array($row['approval_status'] ?? null, ['pending', 'approved', 'rejected'], true)
                    ? $row['approval_status']
                    : 'pending';

                $item->update([
                    'approval_status' => $status,
                    'quoted_price'    => is_numeric($row['quoted_price'] ?? null) ? $row['quoted_price'] : null,
                    'admin_note'      => isset($row['admin_note']) ? mb_substr((string) $row['admin_note'], 0, 500) : null,
                ]);
            }

            $this->recalculateWishListStatus($wishList);

            session()->flash('success', 'Decisions saved.');
        } catch (\Exception $ex) {
            Log::error('Error in saveDecisions: ' . $ex->getMessage());
            session()->flash('app_error', 'Something went wrong saving decisions.');
        }

        return back();
    }

    private function recalculateWishListStatus(WishList $wishList): void
    {
        if ($wishList->status === 'closed') {
            return;
        }

        $wishList->load('items');
        $items = $wishList->items;

        if ($items->isEmpty()) {
            return;
        }

        $pending = $items->where('approval_status', 'pending')->count();
        $touched = $items->whereIn('approval_status', ['approved', 'rejected'])->count();

        if ($touched > 0 && $wishList->status !== 'quoted') {
            $wishList->update(['status' => 'quoted']);
        } elseif ($touched === 0 && $pending === $items->count()) {
            $wishList->update(['status' => 'submitted']);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:submitted,quoted,confirmed,closed',
            ]);

            if (!in_array(myRoleName(), ['Admin', 'SalesRep'])) {
                abort(403);
            }

            $wishList = WishList::findOrFail($id);
            $wishList->update(['status' => $request->status]);

            session()->flash('success', 'Wish list status updated.');
        } catch (\Exception $ex) {
            Log::error('Error in updateStatus: ' . $ex->getMessage());
        }

        return back();
    }

    private function getOrCreateDraft(): WishList
    {
        $draft = WishList::mineDraft()->first();
        if ($draft) {
            return $draft;
        }

        return WishList::create([
            'user_id' => auth()->id(),
            'status'  => 'draft',
        ]);
    }
}
