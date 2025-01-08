<!DOCTYPE html>
<html>
<head>
    <title>Product Report</title>
    <style>
        /* Add styling to ensure proper alignment and spacing */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header-container img {
            max-height: 100px; /* Adjusted for better fit */
            text-align: left !important;
        }

        .header-container address {
            text-align: right;
            font-style: normal; /* Remove default italic styling of address */
            margin: 0;
            line-height: 1.6;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 0.5px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<div class="header-container">
    <!-- Image in one section -->
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('VFLogo.png'))) }}" height="150px" alt="Logo Virgin Farms">

    <!-- Address in the other section -->
    <address>
        <b>Virgin Farms Direct</b><br>
        1.888.548 (7673)<br>
        <a href="https://www.virginfarms.com" target="_blank">www.virginfarms.com</a><br>
        <a href="https://www.virginfarms.net" target="_blank">www.virginfarms.net</a><br>
        <a href="mailto:sales@virginfarms.com">sales@virginfarms.com</a><br>
        <a href="mailto:weborders@virginfarms.com">weborders@virginfarms.com</a><br>
    </address>
</div>

<h4>Availability Week of {{@dateFormatMy($dateIn)}}</h4>

<!-- Table Section -->
<table>
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
