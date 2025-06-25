<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use DataTables;

class PaymentController extends Controller
{
    public function showPayment(Request $request)
    { 
        // 1) From the Laravel Request wrapper
        $fromRequestServer = $request->server('SERVER_ADDR');

        // 2) Directly from the PHP superglobal
        $fromSuperGlobal   = $_SERVER['SERVER_ADDR'] ?? null;

        // 3) What the OS thinks our hostname is…
        $hostname          = gethostname();
        // …and its DNS-resolved IPv4
        $ipViaDns          = gethostbyname($hostname);

        // 4) Public-facing IP via external service
        //    (requires allow_url_fopen or use Guzzle/http client instead)
        try {
            $publicIp = file_get_contents('https://api.ipify.org');
        } catch (\Throwable $e) {
            $publicIp = 'error: '.$e->getMessage();
        }

        // 5) Client IP sources (e.g. Cloudflare)
        $cfConnectingIp    = $request->header('CF-Connecting-IP');
        $clientIp          = $request->getClientIp();
        $forwardedIp       = $cfConnectingIp ?? $clientIp;

        // Bundle them up and dump
        $allIps = [
            'Laravel $request->server("SERVER_ADDR")' => $fromRequestServer,
            '$_SERVER["SERVER_ADDR"]'                => $fromSuperGlobal,
            'gethostname()'                          => $hostname,
            'gethostbyname(hostname)'                => $ipViaDns,
            'Public IP (ipify)'                      => $publicIp,
            'CF-Connecting-IP header'                => $cfConnectingIp,
            'getClientIp()'                          => $clientIp,
            'Chosen client IP'                       => $forwardedIp,
        ];

        dd($allIps);
        
        if ($request->ajax()) {
            $query = Payment::with(['product:id,name,type,uuid','user:id,email'])
                ->when($request->filled('search_term'), function ($q) use ($request) {
                    $term = $request->input('search_term');
                    $q->where('razorpay_order_id', 'like', "%{$term}%")
                      ->orWhereHas('product', function ($q2) use ($term) {
                          $q2->where('name', 'like', "%{$term}%");
                      });
                })
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    $q->where('user_id', $request->input('user_id'));
                })
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('product_name', fn($row) => $row->product->name ?? '—')
                ->addColumn('product_type', fn($row) => strtoupper($row->product->type ?? '—'))
                ->addColumn('user_email', fn($row) => $row->user->email ?? '—')
                ->addColumn('amount', fn($row) => '₹'.number_format($row->amount, 2))
                ->addColumn('paid_at', fn($row) => $row->created_at->format('d-m-Y'))
                ->addColumn('status', function($row) {
                    $class = $row->status==='paid'?'success':'danger';
                    return "<span class='badge badge-{$class}'>".ucfirst($row->status)."</span>";
                })
                ->addColumn('generate_key', fn($row) => "<button class='btn btn-sm btn-info btn-generate-key' data-uuid='{$row->product->uuid}'>Generate Key</button>")
                ->rawColumns(['status','generate_key'])
                ->toJson();
        }
        return view('admin.payment.show');
    }
}
