<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplu Receipt</title>
    <style>
        /* Base Styles */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            border: 1px solid #333;
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }

        /* Header Section */
        .header-table {
            width: 100%;
            border-bottom: 1px solid #333;
            border-collapse: collapse;
            line-height: 1.6;
        }
        .header-table td {
            padding: 15px;
        }
        .header-img {
            width: 25%;
            vertical-align: middle;
            text-align: left;
        }
        .header-img img {
            width: 100%;
            max-width: 120px;
            height: auto;
        }
        .contact-info {
            width: 50%;
            vertical-align: top;
        }
        .contact-info h4,
        .contact-info p {
            margin: 0;
        }
        .header-title {
            width: 25%;
            text-align: right;
            text-transform: uppercase;
            vertical-align: bottom;
        }
        .header-title h2 {
            margin: 0;
        }

        /* Invoice Details */
        .invoice-details {
            overflow: hidden;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }
        .invoice-block {
            float: left;
            width: 45%;
            padding: 15px;
        }
        .invoice-block.left {
            border-right: 1px solid #333;
        }
        .invoice-details p {
            margin: 0;
        }

        /* Section Heading */
        .heading {
            border-bottom: 1px solid #333;
            padding: 5px 15px;
            background: #f4f4f4;
        }
        .heading h5 {
            margin: 0;
            font-size: 14px;
        }

        /* Bill To Section */
        .billto {
            width: 100%;
        }
        .billto .content {
            float: left;
            width: 50%;
            padding: 15px;
        }
        .billto .content p {
            margin: 0;
        }

        /* Items Table */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-bottom: 0px;
        }
        .table th,
        .table td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }
        .table th {
            background: #f4f4f4;
        }
        .table td.center {
            text-align: center;
        }
        .table td.right {
            text-align: right;
        }

        /* Summary Section */
        .summary {
            overflow: hidden;
        }
        .summary .left {
            float: left;
            padding: 37px 18px;
            width: 50%;
            border-left: 1px solid #333;
        }
        .summary .right {
            float: right;
            width:41.1%;
            border-left: 1px solid #333;
            border-right: 1px solid #333;
            padding: 15px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary table td {
            text-align: right;
            padding: 5px;
        }

        /* Note Section */
        .note {
            font-size: 10px;
            padding: 10px;
            background: #f4f4f4;
            border-top: 1px solid #000;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td class="header-img">
                    <img src="https://applaudwebmedia.com/wp-content/uploads/2023/12/Logo1-250.png" alt="Company Logo">
                </td>
                <td class="contact-info">
                    <h4 style="font-size: 16px;">Applaud Web Media Pvt. Ltd.</h4>
                    <p>1st Floor, 4 Sim Tower, GMS Road, near Shimla Bypass Road, Chowk, Dehradun, Uttarakhand 248001</p>
                    <p>GSTIN 05AATCA0702H1ZY</p>
                    <p>info@aplu.com</p>
                </td>
                <td class="header-title">
                    <h2>Invoice</h2>
                </td>
            </tr>
        </table>

        <!-- Invoice Details Section -->
        <div class="invoice-details clearfix">
            <div class="invoice-block left">
                <table style="width: 100%;">
                    <tr>
                        <td><b>Invoice Number</b></td>
                        <td>:</td>
                        <td>{{ $invoiceNumber }}</td>
                    </tr>
                    <tr>
                        <td><b>Date</b></td>
                        <td>:</td>
                        <td>{{ $invoiceDate }}</td>
                    </tr>
                </table>
            </div>
            <div class="invoice-block">
                <table style="width: 100%;">
                    <tr>
                        <td><b>Place of Supply</b></td>
                        <td>:</td>
                        <td><b>Uttarakhand</b></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Bill To Section -->
        <div class="heading">
            <h5>Bill To</h5>
        </div>
        <div class="billto clearfix">
            <div class="content">
                <p><b>{{ $billingFrom['name'] }}</b></p>
                <p>{{ $billingFrom['email'] }}</p>
                <p>{{ "+91-" . $billingFrom['phone'] }}</p>
                <p>{{ $billingFrom['address'] }}</p>
                @if (isset($billingFrom['pan_card']) && $billingFrom['pan_card'] != '')
                    <p>Pan Card : {{ $billingFrom['pan_card'] }}</p>
                @endif
                @if (isset($billingFrom['gst_number']) && $billingFrom['gst_number'] != '')
                    <p>GST Number : {{ $billingFrom['gst_number'] }}</p>
                @endif
            </div>
            
            <table class="table">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Item & Description</th>
                     <th style="text-align:center">HSN/SAC</th>
                     <th style="text-align:end">Amount (₹)</th>
                  </tr>
               </thead>
               <tbody>
                  <!-- Core Product -->
                  <tr>
                     <td>1</td>
                     <td><b>{{ $coreProduct['description'] }}</b></td>
                     <td class="center">998315</td>
                     <td class="right">{{ number_format($coreProduct['price'], 2) }}</td>
                  </tr>
                  
                  <!-- Addons -->
                  @php 
                    $addonsTotal = 0;
                    $row = 1;
                    $support_year = json_decode($payment->metadata, true)['support_year'] ?? 1;
                  @endphp
                  
                  @foreach($addons as $addonName => $addonPrice)
                     @php 
                        $addonsTotal += $addonPrice; 
                        $row++; 
                     @endphp
                     <tr>
                        <td>{{ $row }}</td>
                        <td>{{ $addonName }}</td>
                        <td class="center">998315</td>
                        <td class="right">{{ number_format($addonPrice, 2) }}</td>
                     </tr>
                  @endforeach
                  
                  <tr>
                     <td>{{ $row + 1 }}</td>
                     <td>Personal Support ({{ $support_year }} year's)</td>
                     <td class="center">998315</td>
                     <td class="right">{{ number_format($supportPrice, 2) }}</td>
                  </tr>
               </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary clearfix">
            <div class="left">
               <p>Total Paid:</p>
               <p><b><em class="text-capitalize">Indian Rupee {{ numberToWords($paidAmount) }} Only.</em></b></p>
            </div>
            <div class="right">
               <div >
                     <table>
                        <!-- Subtotal -->
                        <tr>
                           <td style="text-align:start"><b>Subtotal</b></td>
                           <td style="text-align:center">-</td>
                           <td>{{ "₹" . number_format($subtotal, 2) }}</td>
                        </tr>

                        <!-- Discount -->
                        @if ($discount > 0)
                           <tr>
                              <td style="text-align:start"><b>Discount</b></td>
                              <td style="text-align:center">-</td>
                              <td>{{ "-₹" . number_format($discount, 2) }}</td>
                           </tr>
                           <tr>
                              <td style="text-align:start"><b>Subtotal After Discount</b></td>
                              <td style="text-align:center">-</td>
                              <td>{{ "₹" . number_format($subtotalAfterDiscount, 2) }}</td>
                           </tr>
                        @endif

                        <!-- GST -->
                        <tr>
                           <td style="text-align:start"><b>GST (18%)</b></td>
                           <td style="text-align:center">-</td>
                           <td>{{ "₹" . number_format($gstAmount, 2) }}</td>
                        </tr>
                        
                        <!-- Paid Amount -->
                        <tr>
                           <td style="text-align:start"><b>Paid Amount</b></td>
                           <td style="text-align:center">-</td>
                           <td>{{ "₹" . number_format($paidAmount, 2) }}</td>
                        </tr>
                     </table>
               </div>
            </div>
         </div>

        <!-- Note Section -->
        <div class="note mt-0">
            <p><b>Note:</b> {{ $note }}</p>
        </div>
    </div>
</body>
</html>