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
         }
         .invoice-block {
            float: left;
            width: 45%;
            padding: 15px;
         }
         /* Add a right border to the left block */
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
            margin-bottom: 0;
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
         /* Summary Section */
         .summary {
            overflow: hidden;
         }
         .summary .left {
            float: left;
            padding: 15px;
            width: 50%;
         }
         .summary .right {
            float: right;
            width: 40%;
           
            border-left: 1px solid #333;
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
            width: 100%;
            font-size: 10px;
            margin-top: 5px;
           
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
                     <td>Place of Supply</td>
                     <td>:</td>
                     <td><b>Uttarakhand</b></td>
                     {{-- {{ $billingFrom['state'] }} --}}
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
               <p>{{$billingFrom['email'] }}</p>
               <p>{{"+91-".$billingFrom['phone'] }}</p>
               <p>{{$billingFrom['address'] }}</p>
               @if (isset($billingFrom['pan_card']) && $billingFrom['pan_card'] != '')
                  <p>Pan Card : {{$billingFrom['pan_card'] }}</p>
               @endif
               @if (isset($billingFrom['gst_number']) && $billingFrom['gst_number'] != '')
                  <p>GST Number : {{$billingFrom['gst_number'] }}</p>
               @endif
            </div>
         </div>
         @php
            $subtotal = $totalAmount / 1.18;
            $finalGst = $totalAmount - $subtotal;
         @endphp
         <!-- Items Table -->
         <table class="table">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Item & Description</th>
                  <th style="text-align: center;">HSN/SAC</th>
                  <th style="text-align: center;">CGST(9%)</th>
                  <th style="text-align: center;">SGST(9%)</th>
                  <th style="text-align: center;">Amount</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>1</td>
                  <td>
                     <p><b>{{ $items['description']}} <small>({{($items['duration'])}})</small></b></p>
                     <p>Aplu Push Notification Service</p>
                  </td>
                  <td style="text-align: center;">998315</td>
                  <td style="text-align: center;">{{ "₹".number_format($finalGst/2, 2) }}</td>
                  <td style="text-align: center;">{{ "₹".number_format($finalGst/2, 2) }}</td>
                  <td style="text-align: center;">{{ "₹".number_format($subtotal, 2) }}</td>
               </tr>
            </tbody>
         </table>
         <!-- Summary Section -->
         <div class="summary clearfix">
            <div class="left">
               <p>Total in words:</p>
               <p><b><em class="text-capitalize">Indian Rupee {{numberToWords($totalAmount)}} Only</em></b></p>
            </div>
            <div class="right">
               <div style="padding:5px; border-bottom: 1px solid #333;">
                  <table>
                     @if (isset($discount) && $discount > 0)
                        <tr>
                           <td>Coupon Discount</td>
                           <td>-{{ "₹".number_format($discount, 2) }}</td>
                        </tr>
                     @endif
                     <tr>
                        <td>Subtotal</td>
                        <td>{{ "₹".number_format($subtotal-$discount,2) }}</td>
                     </tr>
                     <tr>
                        <td>GST (18%)</td>
                        <td>{{ "₹".number_format($finalGst, 2) }}</td>
                     </tr>
                     <tr>
                        <td>Total</td>
                        <td><b>{{ "₹".number_format($totalAmount) }}</b></td>
                     </tr>
                  </table>
               </div>
            </div>
         </div>
         <!-- Note Section -->
      
      </div>
      <div class="note">
        <p><b>Note:</b> {{ $note }}</p>
     </div>
   </body>
</html>