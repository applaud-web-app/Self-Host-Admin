<?php

namespace App\Services;

use Mpdf\Mpdf;

class PDFService
{
     public function generatePdf($view, $data)
    {
        // Render the view and get the HTML content
        $html = view($view, $data)->render();

        // mPDF Configuration options
        $config = [
            'mode' => 'utf-8',                  // Unicode mode
            'format' => 'A4',                   // Set paper size to A4
            'margin_left' => 5,                // Left margin
            'margin_right' => 5,               // Right margin
            'margin_top' => 5,                 // Top margin
            'margin_bottom' => 5,              // Bottom margin
            'margin_header' => 5,               // Header margin
            'margin_footer' => 5                // Footer margin
        ];

        // Initialize mPDF with the custom configuration
        $pdf = new Mpdf($config);

        // Write the HTML content to the PDF
        $pdf->WriteHTML($html);

        // Output PDF to browser (you can also use 'D' for download)
        return $pdf->Output('document.pdf', 'I');
    }
}
