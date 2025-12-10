<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function logo()
    {
        $siteLogo = DB::table('settings')->where('key', 'site_logo')->first();
        $nameLogo = DB::table('settings')->where('key', 'site_name_logo')->first();
        
        return view('admin.settings.logo', compact('siteLogo', 'nameLogo'));
    }

    public function deleteLogo(Request $request)
    {
        $settingId = $request->input('setting_id');
        $setting = DB::table('settings')->where('id', $settingId)->first();
        
        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], 404);
        }
        
        // Clear the logo value and dimensions
        DB::table('settings')->where('id', $settingId)->update([
            'value' => null,
            'logo_width' => null,
            'logo_height' => null,
            'logo_object_fit' => 'contain',
            'updated_at' => now()
        ]);
        
        Cache::forget('home.settings');
        
        return response()->json(['success' => 'Logo deleted successfully']);
    }

    public function data(Request $request)
    {
        $query = DB::table('settings')->select(['id','key','value','description','type','created_at']);
        
        // Apply category filter
        if ($request->has('category_filter') && !empty($request->category_filter)) {
            $category = strtolower($request->category_filter);
            
            switch($category) {
                case 'branding':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%brand%')
                          ->orWhere('key', 'like', '%logo%')
                          ->orWhere('key', 'like', '%site_name%');
                    });
                    break;
                case 'homepage':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%homepage%')
                          ->orWhere('key', 'like', '%hero%')
                          ->orWhere('key', 'like', '%products%')
                          ->orWhere('key', 'like', '%vegetables%')
                          ->orWhere('key', 'like', '%bestseller%');
                    });
                    break;
                case 'product':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%product%')
                          ->orWhere('key', 'like', '%cart%')
                          ->orWhere('key', 'like', '%currency%')
                          ->orWhere('key', 'like', '%price%')
                          ->orWhere('key', 'like', '%quantity%');
                    });
                    break;
                case 'contact':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%contact%')
                          ->orWhere('key', 'like', '%email%')
                          ->orWhere('key', 'like', '%phone%')
                          ->orWhere('key', 'like', '%address%');
                    });
                    break;
                case 'navigation':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%nav%')
                          ->orWhere('key', 'like', '%breadcrumb%')
                          ->orWhere('key', 'like', '%login%')
                          ->orWhere('key', 'like', '%register%');
                    });
                    break;
                case 'footer':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%footer%')
                          ->orWhere('key', 'like', '%copyright%')
                          ->orWhere('key', 'like', '%subscribe%');
                    });
                    break;
                case 'mitra':
                    $query->where(function($q) {
                        $q->where('key', 'like', '%mitra%')
                          ->orWhere('key', 'like', '%dashboard%');
                    });
                    break;
            }
        }
        
        // Apply search filter
        if ($request->has('search_filter') && !empty($request->search_filter)) {
            $searchTerm = strtolower($request->search_filter);
            $query->where(function($q) use ($searchTerm) {
                $q->where('key', 'like', '%' . $searchTerm . '%')
                  ->orWhere('value', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        return DataTables::of($query)
            ->addIndexColumn() // This adds DT_RowIndex
            ->addColumn('category', function($row){
                // Auto-categorize based on key
                $key = strtolower($row->key);
                
                if (str_contains($key, 'brand') || str_contains($key, 'logo') || str_contains($key, 'site_name')) {
                    return '<span class="badge bg-primary">Branding & Logo</span>';
                } elseif (str_contains($key, 'homepage') || str_contains($key, 'hero') || str_contains($key, 'products') || str_contains($key, 'vegetables') || str_contains($key, 'bestseller')) {
                    return '<span class="badge bg-success">Homepage</span>';
                } elseif (str_contains($key, 'product') || str_contains($key, 'cart') || str_contains($key, 'currency') || str_contains($key, 'price') || str_contains($key, 'quantity')) {
                    return '<span class="badge bg-info">Produk & E-commerce</span>';
                } elseif (str_contains($key, 'contact') || str_contains($key, 'email') || str_contains($key, 'phone') || str_contains($key, 'address')) {
                    return '<span class="badge bg-warning">Kontak & Form</span>';
                } elseif (str_contains($key, 'nav') || str_contains($key, 'breadcrumb') || str_contains($key, 'login') || str_contains($key, 'register')) {
                    return '<span class="badge bg-secondary">Navigasi & Menu</span>';
                } elseif (str_contains($key, 'footer') || str_contains($key, 'copyright') || str_contains($key, 'subscribe')) {
                    return '<span class="badge bg-dark">Footer</span>';
                } elseif (str_contains($key, 'mitra') || str_contains($key, 'dashboard')) {
                    return '<span class="badge bg-danger">Mitra Dashboard</span>';
                } else {
                    return '<span class="badge bg-light text-dark">Lainnya</span>';
                }
            })
            ->addColumn('key_display', function($row){
                // Format key untuk better readability
                $formatted = ucwords(str_replace('_', ' ', $row->key));
                return '<code class="text-primary">' . $formatted . '</code>';
            })
            ->addColumn('value_display', function($row){
                $value = $row->value;
                
                // Truncate long values
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 50) . '...';
                }
                
                // Handle empty values
                if (empty($value)) {
                    return '<span class="text-muted fst-italic">Kosong</span>';
                }
                
                // Handle boolean values
                if ($value === 'true') {
                    return '<span class="badge bg-success">Ya</span>';
                } elseif ($value === 'false') {
                    return '<span class="badge bg-danger">Tidak</span>';
                }
                
                // Handle file/image values
                if (str_contains($value, 'storage/')) {
                    return '<span class="badge bg-info"><i class="bx bx-image me-1"></i>File/Gambar</span>';
                }
                
                return '<span class="text-dark">' . htmlspecialchars($value) . '</span>';
            })
            ->addColumn('status', function($row){
                if (empty($row->value)) {
                    return '<span class="badge bg-secondary"><i class="bx bx-x me-1"></i>Kosong</span>';
                } else {
                    return '<span class="badge bg-success"><i class="bx bx-check me-1"></i>Aktif</span>';
                }
            })
            ->addColumn('actions', function($row){
                $edit = route('admin.settings.edit', $row->id);
                $del = route('admin.settings.destroy', $row->id);
                $key = $row->key;
                $value = $row->value;
                return view('admin.settings.partials.actions', compact('edit','del','key','value'))->render();
            })
            ->rawColumns(['category', 'key_display', 'value_display', 'status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $setting = null;
        return view('admin.settings.form', compact('setting'));
    }

    public function store(SettingRequest $request)
    {
        $data = $request->validated();
        
        // handle optional file upload (e.g., hero_bg)
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/settings', 'public');
            $data['value'] = 'storage/'.$path;
        }
        
        unset($data['file']);
        
        // Add timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        DB::table('settings')->insert($data);
        Cache::forget('home.settings');
        
        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $setting = DB::table('settings')->where('id',$id)->first();
        abort_if(!$setting,404);
        return view('admin.settings.form', compact('setting'));
    }

    public function update(SettingRequest $request, $id)
    {
        $setting = DB::table('settings')->where('id',$id)->first();
        abort_if(!$setting,404);
        
        $data = $request->validated();
        
        // handle optional file upload (e.g., hero_bg)
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/settings', 'public');
            $data['value'] = 'storage/'.$path;
        }
        
        // Handle logo dimensions
        if ($request->has(['logo_width', 'logo_height'])) {
            $data['logo_width'] = $request->input('logo_width');
            $data['logo_height'] = $request->input('logo_height');
            $data['logo_object_fit'] = $request->input('logo_object_fit', 'contain');
        }
        
        unset($data['file']);
        
        // Update with timestamp
        $data['updated_at'] = now();
        
        DB::table('settings')->where('id',$id)->update($data);
        Cache::forget('home.settings');
        
        // Redirect based on setting type
        if (in_array($setting->key, ['site_logo', 'site_name_logo'])) {
            return redirect()
                ->route('admin.settings.logo')
                ->with('success', 'Logo berhasil diperbarui!');
        } else {
            return redirect()
                ->route('admin.settings.index')
                ->with('success', 'Pengaturan berhasil diperbarui!');
        }
    }

    public function destroy($id)
    {
        $s = DB::table('settings')->where('id',$id)->first();
        if ($s) {
            DB::table('settings')->where('id',$id)->delete();
            Cache::forget('home.settings');
        }
        return redirect()->route('admin.settings.index')->with('success','Setting deleted');
    }
}
