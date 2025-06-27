<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models as MD;

class CouponManagement extends Controller
{
    public function coupons(Request $request){

        $coupon = MD\Coupon::select('coupon_code','id','discount_type','discount_amount','expiry_date','usage_type','usage_limit','status');
        $coupon->when($request->coupon, function ($q) use ($request) {
            return $q->where('coupon_code', 'like', '%' . $request->coupon . '%');
        });
        $coupon = $coupon->whereIn('status', [1, 0])->latest();
        if ($request->ajax()) {
            return DataTables::of($coupon)
                ->addIndexColumn()
                ->addColumn('coupon', function ($row) {
                    return $row->coupon_code;
                })
                ->addColumn('discount_amount', function ($row) {
                    return $row->discount_type === "percentage" ? number_format($row->discount_amount,2)."%" : "₹".number_format($row->discount_amount,2);
                })
                ->addColumn('expiry_date', function ($row) {
                    return date('d M, Y',strtotime($row->expiry_date));
                })
                ->addColumn('usage_type', function ($row) {
                    return $limit = $row->usage_type === "multiple" ? '<span class="ms-2 text-capitalize">'.$row->usage_type.': '.$row->usage_limit.'</span>' : '<span class="ms-2 text-capitalize">'.$row->usage_type.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $todayDate = date('Y-m-d'); 
                    $expiryDate = date('Y-m-d', strtotime($row->expiry_date)); 
                    $isExpired = strtotime($expiryDate) <= strtotime($todayDate);
                    if($isExpired){
                        return '<span class="badge badge-dark">Expired</span>';
                    }
                    $color = $row->status == 1 ? "success" : ($row->status == 0 ? "danger" : "warning");
                    $status = $row->status == 1 ? 'Active' : 'Inactive';
                    return '<span class="badge badge-'.$color.'">'.$status.'</span>';
                })
                ->addColumn('created_date', function ($row) {
                    return '<p class="mb-0">'.date('d/m/Y, H:m A', strtotime($row->created_at)).'</p>';
                })
                ->addColumn('action', function ($row) {
                    $remove = route('admin.coupons.remove');
                    $edit = route('admin.coupons.edit');
                    $param = ['coupon_id' => $row->id];
                    $deleteUrl = encryptUrl($remove, $param);
                    $editUrl = encryptUrl($edit, $param);
                    return '<a href="'.$editUrl.'" class="btn btn-secondary btn-xs sharp me-2"><i class="fa fa-pencil"></i></a><button data-url="'.$deleteUrl.'" class="btn btn-danger btn-xs sharp deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteUser"><i class="fa fa-trash"></i></button>';
                })
                ->rawColumns(['coupon','discount_type','discount_amount','expiry_date','usage_type','status','action','created_date']) // Ensure HTML is rendered properly
                ->make(true);
        }
        return view('admin.coupon.coupons');
    }

    public function addCoupon(){
        return view('admin.coupon.add-coupon');
    }

    public function storeCoupon(Request $request){
        try {
            $request->validate([
                'code'=>'required|regex:/^\S+$/',
                'discount_type'=>'required|in:percentage,fixed',
                'discount_amount'=>'required|integer|min:1|max:99999', // 5 digit number eg - 99999
                'expiry_date'=>'required|date|after:today',
                'usage_type'=>'required|in:single,multiple',
                'usage_limit'=>'nullable|integer|min:1|max:99999',
                'description'=>'required|max:225',
                'status'=>'required|in:1,0',
            ]);
    
            if($request->discount_type === "percentage" && $request->discount_amount > 100){
                return redirect()->back()->with('error','Invalid percentage given');
            }
    
            if($request->usage_type === "multiple" && $request->usage_limit <= 1){
                return redirect()->back()->with('error','Usage Limit must be greater than 1');
            }
    
            // UNIQUE COUPON CODE 
            $check = MD\Coupon::where('coupon_code',$request->code)->exists();
            if($check){
                return redirect()->back()->with('error','Coupon Already Exist');
            }
    
            MD\Coupon::create([
                'coupon_code'=>$request->code,
                'discount_type'=>$request->discount_type,
                'discount_amount'=>$request->discount_amount,
                'expiry_date'=>$request->expiry_date,
                'usage_type'=>$request->usage_type,
                'usage_limit'=>$request->usage_type === "multiple" ? $request->usage_limit : 1,
                'description'=>$request->description,
                'status'=>$request->status,
            ]);

            return redirect()->route('admin.coupons.show')->with('success','Coupon Created Successfully!!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Something Went Wrong: '.$th->getMessage());
        }
    }

    public function editCoupon(Request $request){
       try {
            $response = decryptUrl($request->eq);
            $data['coupon'] = MD\Coupon::select('coupon_code','discount_type','discount_amount','expiry_date','usage_type','usage_limit','description','status')->where('id',$response['coupon_id'])->first();
            $couponUrl = route('admin.coupons.update');
            $param = ['coupon_id' => $response['coupon_id']];
            $url = encryptUrl($couponUrl, $param);
            $url2 = encryptUrl(route('admin.coupons.check'), $param);
            $data['encryptUrl'] = $url;
            $data['checkCoupon'] = $url2;
            return view('admin.coupon.edit-coupon',compact('data'));
       } catch (\Throwable $th) {
            return redirect()->back()->with('error','Invalid Request: '.$th->getMessage());
       }
    }

    public function updateCoupon(Request $request)
    {
        try {
            // Validate request input
            $request->validate([
                'code' => 'required|regex:/^\S+$/',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_amount' => 'required|integer|min:1|max:99999',
                'expiry_date' => 'required|date|after:today',
                'usage_type' => 'required|in:single,multiple',
                'usage_limit' => 'nullable|integer|min:2|max:99999',
                'description' => 'required|max:225',
                'status' => 'required|in:1,0', 
                'eq' => 'required', 
            ]);
            

            $response = decryptUrl($request->eq);
            $couponId = $response['coupon_id'] ?? null;

            if (!$couponId) {
                return redirect()->back()->with('error', 'Invalid Request: Unable to determine coupon ID.');
            }

            if ($request->discount_type === "percentage" && $request->discount_amount > 100) {
                return redirect()->back()->with('error', 'Invalid percentage given. Maximum allowed is 100%.');
            }

            if ($request->usage_type === "multiple" && (!$request->usage_limit || $request->usage_limit < 2)) {
                return redirect()->back()->with('error', 'Usage Limit must be greater than 1 for multiple usage.');
            }

            $isDuplicate = MD\Coupon::where('coupon_code', $request->code)
                ->where('id', '!=', $couponId)
                ->exists();

            if ($isDuplicate) {
                return redirect()->back()->with('error', 'Coupon Code Already Exists.');
            }

            $coupon = MD\Coupon::find($couponId);

            if (!$coupon) {
                return redirect()->back()->with('error', 'Coupon not found.');
            }

            $coupon->update([
                'coupon_code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_amount' => $request->discount_amount,
                'expiry_date' => $request->expiry_date,
                'usage_type' => $request->usage_type,
                'usage_limit' => $request->usage_type === "multiple" ? $request->usage_limit : 1,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.coupons.show')->with('success', 'Coupon Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something Went Wrong: ' . $th->getMessage());
        }
    }

    public function removeCoupon(Request $request){
        try {
            $response =  decryptUrl($request->eq);
            $couponId = $response['coupon_id'];

            $coupon = MD\Coupon::find($couponId);
            if($coupon){
                $coupon->status = 2;
                $coupon->save();
                return redirect()->back()->with('success','Coupon Remove Successfully');
            }
            return redirect()->back()->with('error','Invalid Action');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Invalid Request: '.$th->getMessage());
        }
    }

    public function checkCoupon(Request $request){
       try {
            $request->validate([
                'code'=>'required'
            ]);

            $couponExists = MD\Coupon::where('coupon_code',$request->code);
            if($request->eq){
                $response =  decryptUrl($request->eq);
                $couponId = $response['coupon_id'];
                $couponExists =  $couponExists->where('id','!=',$couponId);
            }
            $couponExists = $couponExists->exists();
           
            // Respond based on coupon existence
            return response()->json([
                'message' => $couponExists ? 'Coupon Already Exists 🥹' : 'Coupon is Available 😊',
                'status' => !$couponExists,
            ], 200);
       } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Invalid Request: '.$th->getMessage(),
                'status'=>false
            ], 500);
       }
    }

}
