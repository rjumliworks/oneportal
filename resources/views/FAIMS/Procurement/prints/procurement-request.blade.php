@php
    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += ($item->item_quantity * $item->item_unit_cost);
    }

    $basePageHeight = 500; // Slightly reduced to ensure no overflow
    $estimatedContentHeight = 0;
    
    foreach ($items as $item) {
        $charCount = strlen($item->item_description);
        
        // Dynamic Font Trigger
        $item->is_long = $charCount > 600; 
        
        // Estimate number of lines: count manual newlines + word-wrapped lines
        $manualLines = substr_count($item->item_description, "\n") + 1;
        $wrappedLines = ceil($charCount / ($item->is_long ? 85 : 65));
        $actualLines = max($manualLines, $wrappedLines);
        
        $lineHeight = $item->is_long ? 13 : 18;
        $estimatedContentHeight += ($actualLines * $lineHeight) + 15; // 15px for padding/borders
    }

    // Calculate filler: If content is too long, height becomes 0
    $dynamicFillerHeight = max(10, $basePageHeight - $estimatedContentHeight);
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0.3in; 
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            line-height: 1.2;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
        }
        
        thead { display: table-header-group; }
        
        th, td { 
            border: 1px solid black; 
            padding: 4px 6px; 
            vertical-align: top; 
            word-wrap: break-word;
        }

        .no-border { border: none !important; }
        .bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* FONT ADJUSTMENT */
        .small-font {
            font-size: 9px !important;
            line-height: 1.1;
        }

        /* FILLER ROW: Only occupies remaining space */
        .filler-row td {
            height: {{ $dynamicFillerHeight }}px;
            border-top: none;
            border-bottom: 1px solid black;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: -1px;
            page-break-inside: avoid; 
        }
        
        .description-cell {
            white-space: pre-wrap;
            text-align: justify;
        }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr class="no-border">
                <td colspan="6" class="no-border" style="padding-bottom: 10px;">
                    <div style="float: right; border: 1px solid black; padding: 2px 8px; font-size: 10px;">
                        <span class="bold">FASS-PUR F08</span><br>
                        <span>Rev. 1/07-01-23</span>
                    </div>
                    <div style="clear: both;"></div>
                    <div class="text-center">
                        <h2 style="margin: 0; padding-bottom: 5px;">PURCHASE REQUEST</h2>
                    </div>
                </td>
            </tr>
            <tr class="no-border">
                <td colspan="4" class="no-border">Entity Name: <u>Department of Science and Technology - IX</u></td>
                <td colspan="2" class="no-border text-right">Fund Cluster: <u>{{$procurement->fund_cluster?->name ?? 'Regular Fund'}}</u></td>
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
            <tr class="{{ $item->is_long ? 'small-font' : '' }}">
                <td class="text-center">{{ $item->item_no }}</td>
                <td class="text-center">{{ $item->item_unit_type->name_short }}</td>
                <td class="description-cell">{!! $item->item_description !!}</td>
                <td class="text-center">{{ $item->item_quantity }}</td>
                <td class="text-right">{{ number_format($item->item_unit_cost, 2) }}</td>
                <td class="text-right bold">{{ number_format($item->item_quantity * $item->item_unit_cost, 2) }}</td>
            </tr>
            @endforeach

            {{-- FILLER: This expands the table to the footer area without forcing a new page --}}
            @if($dynamicFillerHeight > 3)
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
            <td colspan="6" style="padding: 10px;">
                <strong>Purpose:</strong> {{ $procurement->purpose }}
            </td>
        </tr>
        <tr class="text-center no-border">
            <td class="no-border" width="20%"></td>
            <td colspan="2" class="bold no-border">Requested By:</td>
            <td colspan="3" class="bold no-border">Approved By:</td>
        </tr>
        <tr class="no-border">
            <td class="no-border" style="padding-top: 15px;">Signature:</td>
            <td colspan="2" class="no-border text-center" style="padding-top: 15px;">__________________________</td>
            <td colspan="3" class="no-border text-center" style="padding-top: 15px;">__________________________</td>
        </tr>
        <tr class="no-border">
            <td class="no-border">Printed Name:</td>
            <td colspan="2" class="text-center bold no-border"><u>{{ strtoupper($procurement->requested_by->profile->fullname) }}</u></td>
            <td colspan="3" class="text-center bold no-border"><u>{{ strtoupper($procurement->approved_by->profile->fullname) }}</u></td>
        </tr>
        <tr class="no-border">
            <td class="no-border">Designation:</td>
            <td colspan="2" class="text-center no-border">{{ $procurement->requested_by->designation ?? 'Division Head' }}</td>
            <td colspan="3" class="text-center no-border">{{ $procurement->approved_by->designation ?? 'Regional Director' }}</td>
        </tr>
    </table>

</body>
<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
            $size = 9;
            $pageText = "Page " . $PAGE_NUM . " of " . $PAGE_COUNT;
            $pdf->text(520, 750, $pageText, $font, $size);
        ');
    }
</script>
</html>