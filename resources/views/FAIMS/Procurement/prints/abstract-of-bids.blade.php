<!DOCTYPE html>
<html>
<head>
    <title>Notice of Award</title>
    <style>
        html, body {
            font-family: Arial, sans-serif;
            margin: 50px 50px 50px 50px;
            font-size: 12px;
            line-height: 1.5;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .header img {
            width: 100px;
            height: 100px;
        }

        .letter-date {
            margin-bottom: 20px;
        }

        .letter-body p {
            margin: 10px 0;
            text-align: justify;
        }

        .signature {
            margin-top: 50px;
        }

        .signature span {
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;;
        }
        th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 7px;
            vertical-align: top;
        }

        .table2 th td tr{
            border:none;
            border-collapse: collapse;
        }

            /* FOOTER FIXED */
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 200px;
            text-align: center;
            padding-top: 10px;
            background: #fff;
        }

    </style>
</head>
<body>
    <!-- Optional Header -->
    
    <div class="header text-center">
        <img src="{{ public_path('/images/logo-sm.png') }}" alt="Logo Left" style="float:left; height:50px; width: 50px" >
         <img src="" alt="Logo Left" style="float:right;">
        <div style="margin-top:20px; line-height : .1">
            <p>Republic of the Philippines</p>
            <h3>Department of Science and Technology</h3>
            <p>Regional Office No. IX</p>
        </div>
    </div>
   

    <div class=" text-center">
       <h2> ABSTRACT OF BIDS</h2>
    </div>

  


    <div>
       <table>
            <tr>
                <td style="text-align: left;" colspan="2">Standard Form Number:</td>
                <td colspan="2" style="text-align: left;" colspan="4">Project Reference No.:</td>
            </tr>
            <tr>
                <td style="text-align: left;" colspan="2">Revised Date:</td>
                <td style="text-align: left;" colspan="2">Name of the Project:</td>
                <td style="text-align: left;" colspan="2">Location of the Project:</td>
            </tr>
            <thead>
                <tr>
                    <th>Item No.</th>
                    <th>Quantity/Unit</th>
                    <th>Description</th>
                    @foreach ($quotations as $quotation)
                        <th>{{ $quotation->supplier->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    // Assume all quotations have the same items in the same order
                    $items = $quotations[0]->items;
                @endphp

                @foreach ($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->item->item_quantity }} {{ $item->item->item_quantity > 1 ? $item->item->item_unit_type->name_long : $item->item->item_unit_type->name_short }}</td>
                        <td > 
                            <div style="margin-top: -10px">
                                {!! $item->item->item_description !!}
                            </div>
                        </td>

                    @foreach ($quotations as $quotation)
                
                      <td class="text-center">
                        @php
                            $price = $quotation->items[$index]->bid_price;
                        @endphp

                        @if (is_null($price))
                            No Bid
                        @elseif ($price == 0)
                            Free
                        @else
                            {{ $price }}
                        @endif
                    </td>
                    
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    </div>

    <div class="footer" style="line-height: 1; font-size: 12px;">
    <div style="margin: 20px 0; text-align: left;">Awarding Committee</div>
    <div>
        <table style="width:100%; text-align:center; margin-bottom:50px; border-collapse: collapse; border: none; line-height: 1;">
            <tr>
                <!-- Chairperson -->
                <th style="border: none; line-height: 1;">{{ $bac_chairperson['name'] }}</th>

                <!-- Vice Chairperson -->
                <th style="border: none; line-height: 1;">{{ $bac_vice_chairperson['name'] }}</th>

                <!-- BAC Members -->
                @foreach ($bac_members as $member)
                    <th style="border: none; line-height: 1;">{{ strtoupper($member['name']) }}</th>
                @endforeach

                <!-- Regional Director -->
                <th style="border: none; line-height: 1;">{{ $regional_director['name'] }}</th>
            </tr>

            <tr>
                <!-- Titles -->
                <td style="border: none; line-height: 1;">Chairperson, BAC</td>
                <td style="border: none; line-height: 1;">Vice Chairperson, BAC</td>

                @foreach ($bac_members as $member)
                    <td style="border: none; line-height: 1;">Member, BAC</td>
                @endforeach

                <td style="border: none; line-height: 1;">Regional Director</td>
            </tr>
        </table>
    </div>
</div>


</body>
</html>
