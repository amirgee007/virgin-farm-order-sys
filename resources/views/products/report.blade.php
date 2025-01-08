<!DOCTYPE html>
<html>
<head>
    <title>Product Report</title>
</head>
<body>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <img src="{{ url('VFLogo.png') }}" alt="Logo Virgin Farms" style="max-height: 150px;">
    <address style="text-align: right; margin: 0;">
        <b>Virgin Farms Direct</b><br>
        1.888.548 (7673)<br>
        <a href="https://www.virginfarms.com" target="_blank">www.virginfarms.com</a><br>
        <a href="https://www.virginfarms.net" target="_blank">www.virginfarms.net</a><br>
        <a href="mailto:sales@virginfarms.com">sales@virginfarms.com</a><br>
        <a href="mailto:weborders@virginfarms.com">weborders@virginfarms.com</a><br>
    </address>
</div>

<h4>Availability Week of {{@dateFormatMy($dateIn)}}</h4>
<table border="0.5px" width="100%">
    <thead>
    <tr>
        @foreach ($columns as $column)
            <th style="text-align: {{ $column === 'product_text' ? 'left' : 'center' }};">{{ @$columnCustomNames[$column] }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($data as $row)
        <tr>
            @foreach ($columns as $column)
                <td style="text-align: {{ $column === 'product_text' ? 'left' : 'center' }};">
                    @if (str_contains($column, 'price'))
                        ${{ round2Digit($row[$column]) }}
                    @else
                        {{ $row[$column] }}
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
