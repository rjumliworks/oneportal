@php
    $totalAmount = 0;
    foreach ($items as $item) {
        $totalAmount += ($item->item_quantity * $item->item_unit_cost);
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { 
            margin: 0.4in; 
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
        }

        /* Essential for multi-page tables */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; 
            page-break-inside: auto; /* Allows table to split across pages */
        }
        
        th, td { 
            border: 1px solid black; 
            padding: 5px; 
            vertical-align: top; 
            word-wrap: break-word;
        }
        
        /* Forces the header/PR details to repeat on every page */
        thead { 
            display: table-header-group; 
        }

        /* CRITICAL FIX: Allows the content inside the row to break */
        tr { 
            page-break-inside: auto !important; 
            page-break-after: auto !important;
        }
        
        td { 
            page-break-inside: auto !important; 
        }

        .no-border { border: none !important; }
        .bold { font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .underline { border-bottom: 1px solid black; }

        .form-id-box {
            border: 1px solid black;
            padding: 2px 8px;
            float: right;
            font-size: 10px;
            margin-bottom: 10px;
        }

        /* Keeps the signatures together so they don't split */
        .footer-wrapper {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <table>
        <thead>
            <tr class="no-border">
                <td colspan="6" class="no-border">
                    <div class="form-id-box">
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
                <td colspan="4" class="no-border">
                    Entity Name: <span class="underline">Department of Science and Technology - IX</span>
                </td>
                <td colspan="2" class="no-border text-right">
                    Fund Cluster: ________________
                </td>
            </tr>
            <tr>
                <td colspan="2" class="bold">Office/Section:</td>
                <td colspan="2" class="bold">PR No: <span class="underline">{{ $procurement->code }}</span></td>
                <td colspan="2" rowspan="2" class="bold">Date: <span class="underline">{{ date('m-d-Y', strtotime($procurement->date)) }}</span></td>
            </tr>
            <tr>
                <td colspan="2">{{ $procurement->division->name }}</td>
                <td colspan="2">Responsibility Center Code: <br>{{ $procurement->unit->responsibility_center_code }}</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <th width="8%">Stock No.</th>
                <th width="10%">Unit</th>
                <th width="42%">Item Description</th>
                <th width="10%">Qty</th>
                <th width="15%">Unit Cost</th>
                <th width="15%">Total Cost</th>
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
            
            <tr style="height: 80px;">
                <td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>

            <tr>
                <td colspan="5" class="text-right bold">TOTAL</td>
                <td class="text-right bold">{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-wrapper">
        <table style="border-top: none;">
            <tr>
                <td colspan="6" style="padding: 10px;">
                    <strong>Purpose:</strong> {{ $procurement->purpose }}
                </td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr class="text-center no-border">
                <td width="20%" class="no-border"></td>
                <td width="40%" class="bold no-border">Requested By:</td>
                <td width="40%" class="bold no-border">Approved By:</td>
            </tr>
            <tr class="no-border" style="height: 35px;">
                <td class="no-border">Signature:</td>
                <td class="no-border">__________________________</td>
                <td class="no-border">__________________________</td>
            </tr>
            <tr class="no-border">
                <td class="no-border">Printed Name:</td>
                <td class="no-border text-center bold"><u>{{ strtoupper($procurement->requested_by->profile->full_name) }}</u></td>
                <td class="no-border text-center bold"><u>{{ strtoupper($procurement->approved_by->profile->full_name) }}</u></td>
            </tr>
            <tr class="no-border">
                <td class="no-border">Designation:</td>
                <td class="no-border text-center">{{ $procurement->requested_by->org_chart->designation->name ?? '' }}</td>
                <td class="no-border text-center">{{ $procurement->approved_by->org_chart->designation->name ?? '' }}</td>
            </tr>
        </table>
        <div class="text-right" style="font-size: 9px; margin-top: 5px;">
            Page 1 of 1
        </div>
    </div>

</body>
</html>