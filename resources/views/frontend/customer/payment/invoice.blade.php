<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Aplu Receipt</title>
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
            border: 1px solid #858585;
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
            line-height: 1.5;
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
            border-bottom: 2px solid #858585;
         }
         .invoice-block {
            width: 45%;
            float: left;
            padding: 10px;
         }
         .invoice-block.left {
            border-right: 2px solid #858585;
         }
         .invoice-details p {
            margin: 5px 0;
         }

         /* Bill To Section */
         .heading {
            margin-top: 30px;
            background: #f4f4f4;
            padding:  5px 15px;
            text-transform: uppercase;
            font-weight: bold;
            border-radius: 10px;
         }
         .billto {
            margin-top: 10px;
             padding:  10px 15px;
            background: #f9f9f9;
            border-radius: 10px;
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
            border: 1px solid #858585;
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
            border-left: 2px solid #858585;
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
                      <td>INV123456</td>
                    </tr>
                    <tr>
                      <td><b>Date</b></td>
                      <td>:</td>
                      <td>2025-06-26</td>
                    </tr>
                  </table>
            </div>
            <div class="invoice-block">
               <table>
                  <tr>
                     <td>Place of Supply</td>
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
               <p><b>John Doe</b></p>
               <p>john.doe@example.com</p>
               <p>+91-1234567890</p>
               <p>123 Main St, Dehradun, Uttarakhand</p>
               <p>Pan Card : ABCD1234E</p>
               <p>GST Number : 05ABCDE1234F1Z1</p>
            </div>
         </div>

         <!-- Items Table -->
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
               <tr>
                  <td>1</td>
                  <td><b>Web Development Services (6 Months)</b></td>
                  <td>998315</td>
                  <td>₹90.00</td>
                  <td>₹90.00</td>
                  <td>₹1000.00</td>
               </tr>
            </tbody>
         </table>

         <!-- Summary Section -->
         <div class="summary clearfix">
            <div class="left">
               <p><strong>Total in Words:</strong></p>
               <p><b><em class="text-capitalize">Indian Rupee One Thousand Only</em></b></p>
            </div>
            <div class="right">
               <table>
                  <tr>
                     <td>Unit Price</td>
                     <td>₹1000.00</td>
                  </tr>
                  <tr>
                     <td>Subtotal</td>
                     <td>₹1000.00</td>
                  </tr>
                  <tr>
                     <td>GST (18%)</td>
                     <td>₹180.00</td>
                  </tr>
                  <tr>
                     <td><strong>Total</strong></td>
                     <td><strong>₹1180.00</strong></td>
                  </tr>
               </table>
            </div>
         </div>

         <!-- Note Section -->
         <div class="note">
            <p><b>Note:</b> Payment is due within 30 days. Please reference the invoice number when making payments.</p>
         </div>
      </div>
   </body>
</html>
