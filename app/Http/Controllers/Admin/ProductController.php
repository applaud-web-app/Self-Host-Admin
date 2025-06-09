<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function showAddons(Request $request)
    {
        // 1) Build a base query: only “addon” products that are active.
        $query = Product::select('id','uuid','slug','name','icon','price','version','description','created_at')
                        ->where('type', 'addon')
                        ->where('status', 1);

        // 2) If “search” is present, filter by name LIKE %search%
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // 3) Filter by purchase_status (if provided)
        //    We assume there is a relationship `payment` on Product that belongs to the current user
        if ($request->filled('purchase_status')) {
            $status = $request->input('purchase_status');

            if ($status === 'purchased') {
                // Only keep products where the current user has a payment record
                $query->whereHas('payment', function($q) {
                    $q->where('user_id', Auth::id());
                });
            }
            elseif ($status === 'not_purchased') {
                // Only keep products where the current user has NO payment record
                $query->whereDoesntHave('payment', function($q) {
                    $q->where('user_id', Auth::id());
                });
            }
            // If “purchase_status” is empty or not one of those two, we do nothing (i.e. show all)
        }

        // 4) Sort: “oldest” or default to “latest”
        if ($request->filled('sort') && $request->input('sort') === 'oldest') {
            $query->orderBy('id', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        // 5) Eager‐load the current user’s payment & license, then paginate
        $products = $query
            ->with([
                'payment' => function($q) {
                    // Only eager‐load this user’s payment row (if any)
                    $q->where('user_id', Auth::id());
                },
                'license' => function($q) {
                    // Likewise, only load this user’s license (if you also filter by license elsewhere)
                    $q->where('user_id', Auth::id());
                },
            ])
            ->paginate(8)
            ->appends($request->only('search','sort','purchase_status'));

        // 6) If AJAX (filter, sort, or page link), return only the partial (cards + pagination)
        if ($request->ajax()) {
            return view('admin.addons.partials.cards', [
                'products' => $products,
            ]);
        }

        // 7) Normal page load: return full “show” view
        return view('admin.addons.show', [
            'products' => $products,
        ]);
    }

    public function uploadZip(Request $request)
    {
        try {
            $file  = $request->file('file');
            $uuid  = $request->input('dzuuid');
            $total = (int)$request->input('dztotalchunkcount', 1);
            $index = (int)$request->input('dzchunkindex', 0);

            $tmpDir = storage_path("app/public/addons/tmp/{$uuid}");
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }

            // save this chunk
            $file->move($tmpDir, "{$index}.part");

            // if last chunk, assemble
            if ($index === $total - 1) {
                $finalName = Str::uuid().'.zip';
                $out = fopen(storage_path("app/public/addons/{$finalName}"), 'wb');

                for ($i = 0; $i < $total; $i++) {
                    $part = fopen("{$tmpDir}/{$i}.part", 'rb');
                    stream_copy_to_stream($part, $out);
                    fclose($part);
                }
                fclose($out);

                // cleanup temp parts & dir
                array_map('unlink', glob("{$tmpDir}/*.part"));
                rmdir($tmpDir);

                return response()->json(['filename'=>$finalName]);
            }

            return response()->json([],204);

        } catch (\Throwable $e) {
            // attempt cleanup
            if (!empty($tmpDir) && is_dir($tmpDir)) {
                array_map('unlink', glob("{$tmpDir}/*.part"));
                @rmdir($tmpDir);
            }
            return response()->json([
              'error'=>'Upload failed: '.$e->getMessage()
            ], 500);
        }
    }

    public function deleteZip(Request $request)
    {
        $request->validate(['filename'=>'required|string']);
        try {
            Storage::disk('public')->delete("addons/{$request->filename}");
            return response()->json([],204);
        } catch (\Throwable $e) {
            return response()->json([
              'error'=>'Could not delete: '.$e->getMessage()
            ], 500);
        }
    }

    public function storeAddons(Request $request)
    {
        try {
            $data = $request->validate([
                'name'        =>'required|string|max:255',
                'zip_file'    =>'required|string',
                'price'       =>'required|numeric|min:0',
                'version'     =>'required|string|max:50',
                'description' =>'required|string',
                'icon'        =>'required|image|mimes:jpg,png,svg'
            ]);
            
            $payload = [
                'uuid'        => (string) Str::uuid(),
                'slug'        => "addons/{$data['zip_file']}",
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'price'       => $data['price'] ?? 0,
                'version'     => $data['version'] ?? null,
                'type'        => 'addon',
                'status'      => 1,
            ];

            if ($request->hasFile('icon')) {
                $path = uploadImage($request->file('icon'), 'icons', 'public');
                $payload['icon'] = basename($path);
            }

            Product::create($payload);

            return redirect()->route('admin.addons.show')->with('success','Addon created.');
        } catch (\Throwable $th) {
            return redirect()->route('admin.addons.show')->with('error','Addon creation failed. '.$th->getMessage());
        }
    }

    public function downloadAddons(string $uuid)
    {
        $product = Product::where('uuid',$uuid)->firstOrFail();
        return Storage::disk('public')->download($product->slug, 'addon.zip');
    }

    public function editAddons(Request $request, string $uuid)
    {
        try {
            $data = $request->validate([
                'name'          => 'required|string|max:255',
                'zip_file'      => 'nullable|string',
                'existing_zip'  => 'nullable|string',
                'dzuuid'        => 'nullable|string',
                'price'         => 'required|numeric|min:0',
                'version'       => 'required|string|max:50',
                'description'   => 'required|string',
                'icon'          => 'nullable|image|mimes:jpg,png,svg',
                'existing_icon' => 'nullable|string',
                'delete_icon'   => 'required|boolean',
            ]);

            $product = Product::where('uuid', $uuid)->firstOrFail();

            // ZIP replacement
            if (!empty($data['zip_file']) && $data['zip_file'] !== $data['existing_zip']) {
                if ($data['existing_zip']) {
                    Storage::disk('public')->delete("addons/{$data['existing_zip']}");
                }
                $product->slug = "addons/{$data['zip_file']}";
            }

            // Icon replacement / deletion
            if ($data['delete_icon'] && $data['existing_icon']) {
                Storage::disk('public')->delete("addons/icons/{$data['existing_icon']}");
                $product->icon = null;
            }
            if ($request->hasFile('icon')) {
                if ($data['existing_icon']) {
                    Storage::disk('public')->delete("addons/icons/{$data['existing_icon']}");
                }
                $path = uploadImage($request->file('icon'), 'icons', 'public');
                $product->icon = basename($path);
            }

            // Other fields
            $product->name        = $data['name'];
            $product->description = $data['description'];
            $product->price       = $data['price'];
            $product->version     = $data['version'];
            $product->save();

            // **Cleanup the tmp chunks folder**
            if (!empty($data['dzuuid'])) {
                Storage::disk('public')->deleteDirectory("addons/tmp/{$data['dzuuid']}");
            }

            return redirect()
                ->route('admin.addons.show')
                ->with('success', 'Addon updated successfully.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.addons.show')
                ->with('error', 'Failed to update addon: ' . $e->getMessage());
        }
    }

}
