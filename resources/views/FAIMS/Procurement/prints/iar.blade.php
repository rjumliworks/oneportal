@php
    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += ($item->item_quantity * $item->item_unit_cost);
    }

    /** * IMPROVED FILLER LOGIC:
     * We estimate height based on row count and character length.
     */
    $basePageHeight = 650; 
    $estimatedContentHeight = 0;
    foreach ($items as $item) {
        // Estimate 25px per row, plus extra for long descriptions
        $descriptionLines = ceil(strlen($item->item_description) / 60); 
        $estimatedContentHeight += ($descriptionLines * 18) + 10;
    }

    $dynamicFillerHeight = $basePageHeight - $estimatedContentHeight;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0.2in; 
        }
        
        /* This handles the automatic page numbering */
        .page-number:before {
            content: counter(page);
        }
        .total-pages:before {
            content: counter(pages);
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            page-break-inside: auto;
        }
        
        th, td { 
            border: 1px solid black; 
            padding: 5px; 
            vertical-align: top; 
            word-wrap: break-word;
        }
        
        /* Repeat header on every page */
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; page-break-after: auto; }

        .no-border { border: none !important; }
        .bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* THE FILLER: Only appears if height is positive */
        .filler-row td {
            height: {{ $dynamicFillerHeight }}px;
            border-top: none;
            border-bottom: 1px solid black;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: -1px;
            page-break-inside: avoid; /* Keeps signature block together */
        }

        .page-counter-container {
            text-align: right;
            font-size: 9px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr class="no-border">
                <td colspan="6" class="no-border">
                    <div style="float: right; border: 1px solid black; padding: 2px 8px; font-size: 10px;">
                        <span class="bold">FASS-PUR F08</span><br>
                        <span>Rev. 1/07-01-23</span>
                    </div>
                    <div style="clear: both;"></div>
                    <div class="text-center">
                        <h2 style="margin: 0 0 15px 0;">PURCHASE REQUEST</h2>
                    </div>
                </td>
            </tr>
            <tr class="no-border">
                <td colspan="4" class="no-border">Entity Name: <u>Department of Science and Technology - IX</u></td>
                <td colspan="2" class="no-border text-right">Fund Cluster: <u>{{$procurement->fund_cluster?->name ?? '________________'}}</u></td>
            </tr>
            <tr>
                <td colspan="2" class="bold">Office/Section:</td>
                <td colspan="2" class="bold">PR No: <u>{{ $procurement->code }}</u></td>
                <td colspan="2" rowspan="2" class="bold">Date: <u>{{ date('m-d-Y', strtotime($procurement->date)) }}</u></td>
            </tr>
            <tr>
                <td colspan="2">{{ $procurement->division->name }}</td>
                <td colspan="2">Responsibility Center Code: <br>{{ $procurement->unit->responsibility_center_code }}</td>
            </tr>
            <tr style="background-color: #f2f2f2;" class="text-center bold">
                <td width="8%">Stock No.</td>
                <td width="10%">Unit</td>
                <td width="42%">Item Description</td>
                <td width="10%">Quantity</td>
                <td width="15%">Unit Cost</td>
                <td width="15%">Total Cost</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td class="text-center">{{ $item->item_no }}</td>
                <td class="text-center">{{ $item->item_unit_type->name_short }}</td>
                <td style="white-space: pre-wrap; text-align: justify;">{!! $item->item_description !!}</td>
                <td class="text-center">{{ $item->item_quantity }}</td>
                <td class="text-right">{{ number_format($item->item_unit_cost, 2) }}</td>
                <td class="text-right bold">{{ number_format($item->item_quantity * $item->item_unit_cost, 2) }}</td>
            </tr>
            @endforeach

            {{-- ONLY SHOW FILLER IF THERE IS REMAINING SPACE --}}
            @if($dynamicFillerHeight > 20)
            <tr class="filler-row">
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endif

            <tr>
                <td colspan="5" class="text-right bold">TOTAL</td>
                <td class="text-right bold">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td colspan="6" style="padding: 15px 10px;">
                <strong>Purpose:</strong> {{ $procurement->purpose }}
            </td>
        </tr>
        <tr class="text-center no-border">
            <td class="no-border" width="15%"></td>
            <td colspan="2" class="bold no-border">Requested By:</td>
            <td colspan="3" class="bold no-border">Approved By:</td>
        </tr>
        <tr class="no-border">
            <td class="no-border">Signature:</td>
            <td colspan="2" class="no-border text-center">__________________________</td>
            <td colspan="3" class="no-border text-center">__________________________</td>
        </tr>
        <tr class="no-border">
            <td class="no-border">Printed Name:</td>
            <td colspan="2" class="text-center bold no-border"><u>{{ strtoupper($procurement->requested_by->profile->fullname) }}</u></td>
            <td colspan="3" class="text-center bold no-border"><u>{{ strtoupper($procurement->approved_by->profile->fullname) }}</u></td>
        </tr>
        <tr class="no-border">
            <td class="no-border">Designation:</td>
            <td colspan="2" class="text-center no-border">{{ $procurement->requested_by->user_organization->org_chart->designation->name ?? '' }}</td>
            <td colspan="3" class="text-center no-border">{{ $procurement->approved_by->user_organization->org_chart->designation->name ?? '' }}</td>
        </tr>
    </table>

    <div class="page-counter-container">
        Page <span class="page-number"></span> of <span class="total-pages"></span>
    </div>

</body>
</html>