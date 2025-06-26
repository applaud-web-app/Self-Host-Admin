<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Aplu Self Hosting Invoice</title>
      <style>
         /* Base Styles */
         body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
         }
         .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #616161;
            border-radius: 8px;
         }

         /* Clearfix for floated elements */
         .clearfix::after {
            content: "";
            display: block;
            clear: both;
         }

         /* Header Section */
         .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            margin-bottom: 20px;
         }
         .header-table td {
            padding: 10px;
         }
         .header-img {
            width: 25%;
            text-align: left;
         }
         .header-img img {
            width: 100%;
            max-width: 120px;
            height: auto;
         }
         .contact-info {
            width: 50%;
            text-align: left;
         }
         .header-title {
            width: 25%;
            text-align: right;
            text-transform: uppercase;
         }
         .header-title h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
         }

         /* Invoice Details Section */
         .invoice-details {
            margin-bottom: 30px;
            border-bottom: 2px solid #616161;
         }
         .invoice-block {
            width: 45%;
            float: left;
            padding: 10px;
         }
         .invoice-block.left {
            border-right: 2px solid #616161;
         }
         .invoice-details p {
            margin: 5px 0;
         }

         /* Bill To Section */
         .heading {
            margin-top: 30px;
            background: #f9f9f9;
            padding: 5px 15px;
            text-transform: uppercase;
            font-weight: bold;
         }
         .billto {
            margin-top: 10px;
            padding: 5px 15px;
            background: #f9f9f9;
            border-radius: 8px;
         }
         .billto .content p {
            margin: 5px 0;
         }

         /* Items Table */
         .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
         }
         .table th, .table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #616161;
         }
         .table th {
            background: #f4f4f4;
         }
         .table tr:nth-child(even) {
            background: #f9f9f9;
         }
         .table td {
            text-align: center;
         }

         /* Summary Section */
         .summary {
            margin-top: 20px;
            text-align: right;
         }
         .summary .left {
            float: left;
            width: 50%;
         }
         .summary .right {
            width: 40%;
            float: right;
            border-left: 2px solid #616161;
            padding-left: 20px;
         }
         .summary table {
            width: 100%;
         }
         .summary table td {
            padding: 8px;
         }

         /* Note Section */
         .note {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
         }

         /* Responsive Design */
         @media (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            .invoice-block {
                width: 100%;
                float: none;
                margin-bottom: 15px;
            }
            .summary .left, .summary .right {
                width: 100%;
                float: none;
                padding-left: 0;
            }
            .summary {
                text-align: left;
            }
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
                  <h4 style="font-size: 16px; font-weight: bold;">Applaud Web Media Pvt. Ltd.</h4>
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
                <table>
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
               <table>
                  <tr>
                     <td>Place of Supply</td>
                     <td>:</td>
                     <td><b>{{ $placeOfSupply }}</b></td>
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
               <p><b>{{ $billTo['name'] }}</b></p>
               <p>{{ $billTo['email'] }}</p>
               <p>{{ $billTo['phone'] }}</p>
               <p>{{ $billTo['address'] }}</p>
               <p>Pan Card : {{ $billTo['pan'] }}</p>
               <p>GST Number : {{ $billTo['gst'] }}</p>
            </div>
         </div>

         <!-- Items Table (Self Hosting Service) -->
         <table class="table">
            <thead>
               <tr>
                  <th>#</th>
                  <th>Item & Description</th>
                  <th>HSN/SAC</th>
                  <th>CGST(9%)</th>
                  <th>SGST(9%)</th>
                  <th>Amount</th>
               </tr>
            </thead>
            <tbody>
               @foreach($items as $item)
               <tr>
                  <td>1</td>
                  <td><b>{{ $item['description'] }}</b></td>
                  <td>{{ $item['hsn'] }}</td>
                  <td>{{ $item['cgst'] }}</td>
                  <td>{{ $item['sgst'] }}</td>
                  <td>{{ $item['amount'] }}</td>
               </tr>
               @endforeach
            </tbody>
         </table>

         <!-- Summary Section -->
         <div class="summary clearfix">
            <div class="left">
               <p><strong>Total in Words:</strong></p>
               <p><b><em class="text-capitalize">{{ $summary['totalWords'] }}</em></b></p>
            </div>
            <div class="right">
               <table>
                  <tr>
                     <td>Self Hosting Service</td>
                     <td>{{ $summary['serviceAmount'] }}</td>
                  </tr>
                  <tr>
                     <td>GST (18%)</td>
                     <td>{{ $summary['gst'] }}</td>
                  </tr>
                  <tr>
                     <td><strong>Total</strong></td>
                     <td><strong>{{ $summary['total'] }}</strong></td>
                  </tr>
               </table>
            </div>
         </div>

         <!-- Note Section -->
         <div class="note">
               <p><b>Note:</b> Thank you for choosing Aplu Self Hosting Service. We ensure the highest quality hosting with 24/7 support.</p>
         </div>
      </div>
   </body>
</html>
