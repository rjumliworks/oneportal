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
        }

        /* HEADER FIXED */
        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            background: #fff;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
            text-align: center;
        }

        /* FOOTER FIXED */
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 200px;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 10px;
            background: #fff;
        }

        /* TABLE EXPANDS TO AVAILABLE PAGE HEIGHT */
        .content-wrapper {
            min-height: calc(100% - 0px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

   

        th, td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
            font-size: 11px;
        }

        thead {
            background: #e6e6e6;

        }

        /* FORCE BORDER DOWN TO FOOTER ON LAST TABLE */
        .table2 tbody tr:last-child td {
            height: 50%;
        }

        .table3 tbody tr td{
            border:none;
        }

        /* PAGE BREAKS FOR LONG DATA */
        tr {
            page-break-inside: avoid;
        }

        .border-container {
            margin-top: 0px;
            border: solid 1px black;
            padding: 2px 8px 2px 8px;
            display: inline-block; /* Keeps the border close to the content */
            margin-right: 20px;
        }

         .bold {
            font-weight: bold;
            font-size:11px;
        }
        .small-text {
            font-size: 8px;
            padding-right: 15px;
        }

        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right ;
            line-height: 0.1;
        }
        .text-left{
            text-align: left ;
            line-height: 0.5;
        }

  
    </style>
</head>

<body>
     <div class="text-right">
        <div class="border-container">
            <p class="bold">FASS-PUR F08</p>
            <p class="small-text">Rev.1/07-01-2023</p>
        </div>
    </div>

    <div style="text-align:center; margin: -30px 0 50px 10px; ">
        <h3>Procurement Request</h3> 
    </div>


<div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; width: 100%; font-size: 12px;">
    <span style="text-align: left; flex: 1; margin-right: 250px">
       <u> Entity Name: Department of Science and Technology - IX</u>
    </span>
    <span style="text-align: right; flex: 1;">
        Fund Cluster: <u>{{ $procurement->fund_cluster->name }}</u>
    </span>
</div>
  <table>
    <tr>
        <td rowspan="2" style="padding: 5px; vertical-align: middle"">
            Office/Unit: <u> {{ $procurement->division->name }} </u>
        </td>
        <td  style="padding: 5px;">
            PR No.:<u> {{ $procurement->code }} </u>
        </td>
        <td rowspan="2" style="padding: 5px; vertical-align: middle">
            Date:<u> {{ date('m/d/Y', strtotime($procurement->date)) }} </u>
        </td>
    </tr>
    <tr>
        <td  style="padding: 5px;">
            Responsibility Center Code: <br><u> {{ $procurement->unit->responsibility_center_code }} </u>
        </td>
    </tr>
</table> 

<div class="content-wrapper">

    <table class="table2">
        <thead style="padding: 10px">
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
                    <td colspan="3" style="padding:5px;  text-align: left ; ">
                        <?php
                                $fontSize = str_word_count($item->item_description) > 100 ? '10px' : '10px';
                            ?>
                            <div style="margin-top:-8px;font-size: {{ $fontSize }};">
                                {!! $item->item_description !!}
                            </div>
                        </td>
                    </td>
                    <td style="text-align:center">{{ $item->item_quantity }}</td>
                    <td style="text-align:center">{{ number_format($item->item_unit_cost, 2) }}</td>
                    <td style="text-align:center">{{ number_format($item->item_quantity * $item->item_unit_cost, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

   <div class="footer">
    <table >
         <tr >
            <td >
                <div class="padding: 20px">
                {{  $procurement->purpose }}
            </div>
        </td>
        </tr>
    </table>
     <table class="table3" style="border: 1px solid"> 
       
      <tr >
        <td colspan="2"></td>
        <td  colspan="3" style="padding-top:2px;padding-bottom:10px">Requested By:</td>
        <td colspan="2" style="padding-top:2px;padding-bottom:0px">Approved By:</td>
      </tr>
      <tr>
         <td colspan="2" style="padding-left:10px">
            Signature
         </td>
         <td colspan="3">______________________</td>
         <td colspan="2" >______________________</td>
      </tr>
      <tr>
        <td  colspan="2" >
            Printed Name
        </td>
        <td colspan="3"><b><u>  {{ $procurement->requested_by->profile->full_name }}  


        </u></b></td>
        <td colspan="2" ><b><u>{{ $procurement->approved_by->profile->full_name }} </u></b></td>
      </tr>
      <tr >
        <td  colspan="2" >
            Designation
        </td>
        <td colspan="3">{{ $procurement->requested_by->org_chart->designation->name ?? null }}</td>
        <td colspan="2" >{{ $procurement->approved_by->org_chart->designation->name ?? null }}</td>
      </tr>
  </table>

   </div>
 

</div>

</body>
</html>
