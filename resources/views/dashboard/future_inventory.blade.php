<tr>
    <td class="align-middle" colspan="3">
        @if(myRoleName() == 'Admin')
            <a target="_blank" title="@lang('Click to see detail and edit quantity')"
               data-toggle="tooltip"
               data-placement="left"
               href="{{ route('products.index.manage', ['date_in' => $inventory->date_in, 'date_out' => $inventory->date_out , 'supp' => $inventory->supplier_id]) }}">
                {{ dateFormatRecent($inventory->date_in) }} - {{ dateFormatRecent($inventory->date_out) }}
            </a>
        @else
            {{ dateFormatRecent($inventory->date_in) }} - {{ dateFormatRecent($inventory->date_out) }}
        @endif
    </td>
    <td class="align-middle" colspan="3">
        {{ $inventory->supplier_name }}
    </td>
    <td class="align-middle" colspan="3">
        {{ $inventory->updated_at }}
    </td>
</tr>
