<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Report</title>
    <style>
        @page {
            margin: 2px; /* Remove all page margins */
        }

        /* General reset */
        body {
            margin: 0;
            padding: 0;
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px; /* Reduce font size */
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 0.5px solid #000;
            padding: 2px; /* Minimized padding */
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Styling for the logo */
        .logo img {
            width: 350px; /* Adjust the logo width */
            height: auto;
            margin-top: -50px;
            margin-left: -30px;
        }

        /* Styling for the address */
        .address {
            text-align: right;
            font-style: normal;
            line-height: 1.4; /* Adjust line height */
        }

        .address a {
            color: #007BFF; /* Link color */
            text-decoration: none;
        }

        .address a:hover {
            text-decoration: underline;
        }

        /* Adjust table heading and data for compact display */
        th, td {
            font-size: 9px; /* Smaller font size for table */
            padding: 1px; /* Minimum padding for compact layout */
        }

        /* Optional: Add some spacing for better visual separation */
        h4 {
            padding-left: 75px;
            font-size: 15px; /* Reduce heading font size */
        }
    </style>
</head>
<body>

<!-- Table with logo on the left and address on the right -->
<table style="margin-left: -25px; margin-bottom: -25px;">
    <tr>
        <!-- Logo Section (Left) -->
        <td class="logo" style="width: 30%; vertical-align: top; border: 0px;">
            <img src="{{public_path('VFLogo.png')}}" alt="Virgin Farms Logo">
        </td>

        <!-- Address Section (Right) -->
        <td class="address" style="width: 70%; margin-left: -20px; border: 0px; font-size: 15px; "> <!-- Adjust the value as needed -->
            <b>Virgin Farms Direct</b><br>
            1.888.548 (7673)<br>
            <a href="https://www.virginfarms.com" target="_blank">www.virginfarms.com</a><br>
            <a href="https://www.virginfarms.net" target="_blank">www.virginfarms.net</a><br>
            <a href="mailto:sales@virginfarms.com">sales@virginfarms.com</a><br>
            <a href="mailto:weborders@virginfarms.com">weborders@virginfarms.com</a><br>
        </td>
    </tr>
</table>

<!-- Optional Heading -->
<h4>Availability Week of {{@dateFormatMy($dateIn)}}</h4>
<!-- Table Section -->
@include('products.reports.__report-table')
</body>
</html>
