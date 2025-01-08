<!DOCTYPE html>
<html>
<head>
    <title>Product Report</title>
</head>
<body>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <img src="https://virginfarms.net/assets/img/virgin-farms-logo.png" alt="Logo Virgin Farms" style="max-height: 100px;">
    <address style="text-align: right; margin: 0;">
        <b>Virgin Farms Direct</b><br>
        1.888.548 (7673)<br>
        www.virginfarms.com<br>
        www.virginfarms.net<br>
        sales@virginfarms.com<br>
        weborders@virginfarms.com<br>
    </address>
</div>

<h4>Availability Week of {{@dateFormatMy($dateIn)}}</h4>
<table border="0.5px" width="100%">
    <thead>
    <tr>
        @foreach ($columns as $column)
            <th style="text-align: left;">{{ @$columnCustomNames[$column] }}</th>
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
