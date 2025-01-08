<!DOCTYPE html>
<html>
<head>
    <title>Product Report</title>
</head>
<body>
<div style="text-align: center;">
    <img src="{{ asset('assets/img/virgin-farms-logo.png') }}" alt="Logo Virgin Farms" style="max-height: 100px; text-align: left;">
    <address style="text-align: right;">
        <b>Virgin Farms Direct</b><br>
        1.888.548 (7673)<br>
        www.virginfarms.com<br>
        sales@virginfarms.com<br><br>
    </address>
</div>
<table border="0.5" width="100%">
    <thead>
    <tr>
        @foreach ($columns as $column)
            <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($data as $row)
        <tr>
            @foreach ($columns as $column)
                <td>{{ $row[$column] }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
