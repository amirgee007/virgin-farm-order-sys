<tr>
    <td class="align-middle" colspan="3">
        <a target="_blank" title="@lang('Click to see detail and edit quantity')"
           data-toggle="tooltip"
           data-placement="left"
           href="{{ route('products.index.manage', ['date_in' => $inventory->date_in, 'date_out' => $inventory->date_out]) }}">
            {{ dateFormatRecent($inventory->date_in) }} - {{ dateFormatRecent($inventory->date_out) }}
        </a>
    </td>
    <td class="align-middle" colspan="3">
        {{ $inventory->supplier_name }}
    </td>
    <td class="align-middle" colspan="3">
        {{ $inventory->updated_at }}
    </td>
</tr>
