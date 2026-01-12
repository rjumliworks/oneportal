<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Full Height Table</title>
    <style>
        @page {
            margin: 90px 40px 80px 40px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
        }

        /* MAIN CONTENT - Push down for header */
        .content-wrapper {
            min-height: calc(100vh - 300px); /* Account for header and footer */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
            font-size: 11px;
        }

        thead {
            background: #e6e6e6;
            display: table-header-group;
        }

        .table2 td tr{
            border:0;
        }


        .table3 td, .table3 th {
            border:0;
        }
        

        .table3{
            border-collapse: collapse;
            border:1px solid black;
        }

        .border-container {
            margin-top: 0px;
            border: solid 1px black;
            padding: 2px 8px 2px 8px;
            display: inline-block;
            margin-right: 20px;
        }

        .bold {
            font-weight: bold;
            font-size: 11px;
        }

        .small-text {
            font-size: 8px;
            padding-right: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
            line-height: 0.1;
        }

        .text-left {
            text-align: left;
            line-height: 0.5;
        }

        /* FILLER ROWS */
        .filler-row {
            height: 25px; /* Adjust this value to control filler row height */
        }

        .filler-cell tr{
            border: 0;
        }


        /* PAGE BREAK CONTROL */
        .page-break {
            page-break-before: always;
        }

        /* TABLE CONTAINER FOR FIXED HEIGHT */
        .table-container-fixed {
            min-height: 400px; /* Adjust this based on your needs */
            position: relative;
        }

        /* EMPTY ROW FILLER */
        .empty-row {
            height: 25px;
        }

        .empty-row td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }
    </style>
</head>

<body>
    <!-- HEADER (fixed, appears on every page) -->
    <header>
        <div class="text-right">
            <div class="border-container">
                <p class="bold">FASS-PUR F08</p>
                <p class="small-text">Rev.1/07-01-2023</p>
            </div>
        </div>

        <div style="text-align:center; margin: -30px 0 50px 10px;">
            <h3>Procurement Request</h3> 
        </div>

        <div class="table-container">
            <table>
            <tr style="font-size:12px; margin-bottom:50px; overflow:hidden;">
                    <td style="float:left;">
                        Entity Name: <u>Department of Science and Technology - IX</u>
                    </td>
                    <td colspan="2" style="float:right; text-align:center;">
                        Fund Cluster: <u>{{ $procurement->fund_cluster->name }}</u>
                    </td>
                </tr>

                <tr>
                    <td rowspan="2" style="padding: 5px; vertical-align: middle">
                        Office/Unit: <u>{{ $procurement->division->name }}</u>
                    </td>
                    <td style="padding: 5px;">
                        PR No.:<u>{{ $procurement->code }}</u>
                    </td>
                    <td rowspan="2" style="padding: 5px; vertical-align: middle">
                        Date:<u>{{ date('m/d/Y', strtotime($procurement->date)) }}</u>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;">
                        Responsibility Center Code: <br><u>{{ $procurement->unit->responsibility_center_code }}</u>
                    </td>
                </tr>
            </table>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <div class="content-wrapper">
        <table class="table2">
            <thead>
                <tr>
                    <th>Stock No.</th>
                    <th>Unit</th>
                    <th colspan="3">Description</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td style="text-align:center">{{ $item->item_no }}</td>
                        <td style="text-align:center">{{ $item->quantity > 1 ? $item->item_unit_type->name_long : $item->item_unit_type->name_short }}</td>
                        <td colspan="3" style="padding:5px; text-align: left;">
                            <?php
                                $fontSize = str_word_count($item->item_description) > 100 ? '10px' : '10px';
                            ?>
                            <div style="margin-top:-8px;font-size: {{ $fontSize }};">
                                {!! $item->item_description !!}
                            </div>
                        </td>
                        <td style="text-align:center">{{ $item->item_quantity }}</td>
                        <td style="text-align:center">{{ number_format($item->item_unit_cost, 2) }}</td>
                        <td style="text-align:center">{{ number_format($item->item_quantity * $item->item_unit_cost, 2) }}</td>
                    </tr>
                @endforeach
                
                <!-- FILLER ROWS - Adjust the number of rows as needed -->
                @php
                    $totalRows = count($items);
                    $desiredRows = 20; // Change this to the number of rows you want to display
                    $fillerRows = $desiredRows - $totalRows;
                    
                    // Only add filler rows if we have fewer items than desired
                    if ($fillerRows > 0) {
                        for ($i = 0; $i < $fillerRows; $i++) {
                @endphp
                            <tr class="filler-row">
                                <td class="filler-cell">&nbsp;</td>
                                <td class="filler-cell">&nbsp;</td>
                                <td colspan="3" class="filler-cell">&nbsp;</td>
                                <td class="filler-cell">&nbsp;</td>
                                <td class="filler-cell">&nbsp;</td>
                                <td class="filler-cell">&nbsp;</td>
                            </tr>
                @php
                        }
                    }
                @endphp

                <!-- TOTAL ROW (always at the bottom) -->
                @php
                    $totalAmount = 0;
                    foreach ($items as $item) {
                        $totalAmount += $item->item_quantity * $item->item_unit_cost;
                    }
                @endphp
                <tr>
                    <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL:</td>
                    <td style="text-align:center; font-weight: bold;">{{ number_format($totalAmount, 2) }}</td>
                </tr>

            </tbody>
        </table>


        <!-- FOOTER - NOT fixed, appears only at the end -->
        <div class="document-footer">
            <table class="table3" style="margin-top:20px;">
                <tr>
                    <td colspan="7">
                         <strong>Purpose:</strong> {{ $procurement->purpose }}  
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="3" style="padding-top:2px;padding-bottom:10px">Requested By:</td>
                    <td colspan="2" style="padding-top:2px;padding-bottom:0px">Approved By:</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-left:10px">Signature</td>
                    <td colspan="3">______________________</td>
                    <td colspan="2">______________________</td>
                </tr>
                <tr>
                    <td colspan="2">Printed Name</td>
                    <td colspan="3"><b><u>{{ $procurement->requested_by->profile->full_name }}</u></b></td>
                    <td colspan="2"><b><u>{{ $procurement->approved_by->profile->full_name }}</u></b></td>
                </tr>
                <tr>
                    <td colspan="2">Designation</td>
                    <td colspan="3">{{ $procurement->requested_by->org_chart->designation->name ?? null }}</td>
                    <td colspan="2">{{ $procurement->approved_by->org_chart->designation->name ?? null }}</td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>