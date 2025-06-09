<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Payment;
use DataTables;

class UserController extends Controller
{
    public function ajaxUsers(Request $request)
    {
        $search = $request->get('q'); // Select2 sends 'q' as the search term
        $usersQuery = User::query();

        if ($search) {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        $users = $usersQuery->role('customer')->select('id', 'email')->limit(10)->get();

        // Format for Select2
        $results = $users->map(function ($user) {
            return [
                'id'   => $user->id,
                'text' => $user->email, // you can also do "$user->name ( $user->email )" if you prefer
            ];
        });

        return response()->json($results);
    }

    public function showUsers(Request $request)
    {
        // If this is an AJAX request, we return JSON for DataTables:
        if ($request->ajax()) {
            $query = User::select([
                    'id',
                    'name',
                    'email',
                    'avatar',
                    'country_code',    // assuming you store country code here
                    'phone',
                    'created_at',
                ])->role('customer')
                ->when($request->filled('search_term'), function ($q) use ($request) {
                    $term = $request->input('search_term');
                    // Search by name OR email OR phone
                    return $q->where(function ($sub) use ($term) {
                        $sub->where('name', 'LIKE', "%{$term}%")
                            ->orWhere('email', 'LIKE', "%{$term}%")
                            ->orWhere('phone', 'LIKE', "%{$term}%");
                    });
                })
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    return $q->where('id', $request->input('user_id'));
                })
                ->orderBy('created_at', 'desc');

            return DataTables::eloquent($query)
                ->addIndexColumn() 
               ->addColumn('name', function ($row) {
                    $avatarHtml = $row->avatar
                        ? '<img src="' . asset('storage/'.$row->avatar) . '" alt="avatar" width="30" height="30" class="rounded-circle" style="margin-right:6px;">'
                        : '';
                    return $avatarHtml . e($row->name);
                })
                ->addColumn('full_phone', function ($row) {
                    return ($row->country_code ? $row->country_code . ' ' : '') . $row->phone;
                })
                ->addColumn('join_date', function ($row) {
                    return $row->created_at->format('d M Y');
                })
                ->addColumn('actions', function ($row) {

                    $editUrl = route('admin.users.edit');
                    $param = ['user_id' => $row->id];
                    $encryptedUrl = encryptUrl($editUrl, $param);

                    $featureUrl = route('admin.user.features');
                    $param = ['user_id' => $row->id];
                    $featureEncrypturl = encryptUrl($featureUrl, $param);

                    $editBtn  = '<button class="btn btn-sm btn-primary btn-edit-user" data-url="' . $encryptedUrl . '">
                                    Edit
                                  </button>';
                    $featureBtn = '<button data-url="' . $featureEncrypturl . '" 
                                      class="btn btn-sm btn-info btn-user-feature">
                                      Features
                                   </button>';
                    return $editBtn . ' ' . $featureBtn;
                })
                ->rawColumns(['actions','name','join_date'])
                ->toJson();
        }

        // Non-AJAX: just render the Blade view.
        return view('admin.users.show');
    }

    public function editUser(Request $request)
    {
        $response = decryptUrl($request->eq);
        $userId = $response['user_id'];

        // Find the user by ID
        $user = User::findOrFail($userId);

        $updateUrl = route('admin.users.update');
        $param = ['user_id' => $user->id];
        $encryptedUrl = encryptUrl($updateUrl, $param);

        return response()->json([
            'url'           => $encryptedUrl,
            'name'         => $user->name,
            'email'        => $user->email,
            'country_code' => $user->country_code,
            'phone'        => $user->phone,
        ]);
    }

    public function updateUser(Request $request)
    {
        // 1. Validate the incoming fields (including 'eq' so we can decrypt it below).
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'country_code' => 'required|string|max:5',
            'phone'        => 'required|string|max:20',
            'password'     => 'nullable|string|min:8|confirmed',
            'eq'           => 'required|string',
        ]);

        // 2. Decrypt 'eq' and fetch the User in one step.
        $payload = decryptUrl($validated['eq']);
        $user    = User::findOrFail($payload['user_id']);

        // 3. Check for any other user (id ≠ current) with the same email OR the same (country_code + phone).
        $conflict = User::where('id', '!=', $user->id)
            ->where(function($q) use ($validated) {
                $q->where('email', $validated['email'])
                ->orWhere(function($q2) use ($validated) {
                    $q2->where('country_code', $validated['country_code'])
                        ->where('phone', $validated['phone']);
                });
            })
            // Only pull the fields we need to differentiate the conflict type
            ->select(['email', 'country_code', 'phone'])
            ->first();

        if ($conflict) {
            if ($conflict->email === $validated['email']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists.',
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => 'Phone number already exists for this country code.',
            ], 422);
        }

        // 4. Build a data array for update()
        $data = [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'country_code' => $validated['country_code'],
            'phone'        => $validated['phone'],
        ];

        // Only hash & set the password if the user actually provided one
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // 5. Perform a single update() call
        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
        ]);
    }

    public function showFeaturesUser(Request $request)
    {
        // 1. Decrypt the 'eq' parameter to get the user ID
        $response = decryptUrl($request->eq);
        $userId   = $response['user_id'];

        // 2. Fetch all payments for this user where status is 'paid', including the related product
        $payments = Payment::with('product')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->get();

        // 3. If no paid payments exist, return a 404
        if ($payments->isEmpty()) {
            return response()->json([
                'error' => 'No paid payments found for this user.'
            ], 404);
        }

        // 4. Transform each Payment into a structured array that includes:
        $features = $payments->map(function (Payment $payment) {
            return [
                'product' => [
                    'id'          => $payment->product->id,
                    'uuid'        => $payment->product->uuid,
                    'slug'        => $payment->product->slug,
                    'name'        => $payment->product->name,
                    'icon'        => $payment->product->icon,
                    'price'       => $payment->product->price,
                    'type'        => $payment->product->type,
                    'description' => $payment->product->description,
                    'status'      => $payment->product->status,
                ],
                'purchased_at' => date('d M Y', strtotime($payment->created_at)),
                'amount'       => $payment->amount,
            ];
        });

        // 5. Return the list of purchased features as JSON
        return response()->json([
            'data' => $features
        ]);
    }

}
