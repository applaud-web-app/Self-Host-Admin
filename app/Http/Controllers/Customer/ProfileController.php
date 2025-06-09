<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserDetail; 

class ProfileController extends Controller
{
    public function showProfile()
    {
        $userId = Auth::id();

        $user = User::select('id', 'name', 'email', 'country_code', 'phone', 'avatar')
            ->with([
                'detail:id,user_id,billing_name,address,state,city,pin_code,pan_card,gst_number'
            ])
            ->findOrFail($userId);

        return view('frontend.customer.profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {

        $userId = Auth::id();

        $data = $request->validate([
            'avatar'       => ['nullable', 'image', 'max:2048'],
            'billing_name' => ['required', 'string', 'max:255'],
            'state'        => ['required', 'string'],
            'city'         => ['required', 'string'],
            'pin_code'     => ['required', 'string', 'max:10'],
            'address'      => ['required', 'string', 'max:500'],
            'pan_card'     => ['nullable', 'string', 'max:20'],
            'gst_number'   => ['nullable', 'string', 'max:20'],
        ]);

        try {
            
            // 1) Update user table
            $user = User::findOrFail($userId);
            if ($request->hasFile('avatar')) {
                $path = uploadImage($request->file('avatar'), 'avatars', 'public');
                $user->avatar = $path;
                $user->save();
            }

            // 2) Update or create detail table
            $detailData = [
                'billing_name' => $data['billing_name'],
                'state'        => $data['state'],
                'city'         => $data['city'],
                'pin_code'     => $data['pin_code'],
                'address'      => $data['address'],
                'pan_card'     => $data['pan_card'],
                'gst_number'   => $data['gst_number'],
            ];

            $user->detail()->updateOrCreate(
                ['user_id' => $userId],
                $detailData
            );

            return redirect()->route('customer.profile.show')->with('success', 'Profile updated successfully.');

        } catch (\Throwable $th) {
            Log::error("message: " . $th->getMessage());
            return redirect()->route('customer.profile.show')->with('error', 'Profile update failed.');
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password'          => ['required', 'string'],
            'new_password'              => ['required', 'string', 'min:8', 'confirmed']
        ]);

        try {
            // Check current password
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }

            // Update to new password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return back()->with('success', 'Password changed successfully.');
        } catch (\Throwable $th) {
           Log::error("message: " . $th->getMessage());
           return back()->with('error', 'Password change failed.');
        }
    }
}
