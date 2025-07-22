@extends('layouts.app')

@section('page-title', __('Product Groups'))
@section('page-heading', __('Products Groups'))

@section('breadcrumbs')
    <li class="breadcrumb-item text-muted">
        @lang('Manage Groups')
    </li>
@stop

@section('content')
    @include('partials.messages')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">

                    <div class="container">
                        <h2>Product Groups</h2>
                        <a href="{{ route('product-groups.create') }}" class="btn btn-primary mb-3">➕ Create New Group</a>

                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                            <tr>
                                <th>Group Name</th>
                                <th>Parent Product</th>

                                <th>Products (with Stems)</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td>
                                        {{ $group->parentProduct?->item_no ?? '—' }}
                                    </td>
                                    <td>
                                        @foreach($group->products as $product)
                                            <div>
                                                <strong>{{ $product->item_no }}</strong> ({{ $product->pivot->stems }} stems)
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('product-groups.edit', $group->id) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('product-groups.destroy', $group->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete group?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop

