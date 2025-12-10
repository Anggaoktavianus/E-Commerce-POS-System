<?php

namespace App\Http\Controllers;

use App\Models\Artikel;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    // Public methods for frontend
    public function publicIndex(Request $request)
    {
        $query = Artikel::with(['kategoriArtikel', 'user'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');
        
        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori_artikel_id', $request->kategori);
        }
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('konten', 'like', '%' . $request->search . '%')
                  ->orWhere('meta_description', 'like', '%' . $request->search . '%');
            });
        }
        
        $artikel = $query->paginate(9);
        $kategori = KategoriArtikel::where('status', 'aktif')
            ->withCount(['artikels' => function($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('nama')
            ->get();
        
        return view('artikel.index', compact('artikel', 'kategori'));
    }
    
    public function publicShow($slug)
    {
        $artikel = Artikel::with(['kategoriArtikel', 'user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Increment views (this will be done via AJAX to avoid page load delay)
        return view('artikel.show', compact('artikel'));
    }
    
    public function incrementViews($id)
    {
        try {
            $artikel = Artikel::findOrFail($id);
            $artikel->increment('views');
            
            return response()->json([
                'success' => true,
                'views' => $artikel->views
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to increment views'
            ], 500);
        }
    }
    
    // Admin methods
    public function index()
    {
        return view('admin.artikel.index');
    }
    
    public function data()
    {
        $query = Artikel::with(['kategoriArtikel', 'user'])
            ->select(['id', 'judul', 'slug', 'gambar_utama', 'gambar_thumbnail', 'kategori_artikel_id', 'user_id', 'status', 'views', 'created_at']);
        
        return datatables()->of($query)
            ->addIndexColumn()
            ->editColumn('gambar_utama', function($row) {
                if ($row->gambar_thumbnail) {
                    return '<img src="' . Storage::url($row->gambar_thumbnail) . '" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="' . $row->judul . '">';
                } elseif ($row->gambar_utama) {
                    return '<img src="' . Storage::url($row->gambar_utama) . '" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="' . $row->judul . '">';
                } else {
                    return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bx bx-image text-muted"></i></div>';
                }
            })
            ->editColumn('judul', function($row) {
                return '<strong>' . $row->judul . '</strong><br><small class="text-muted">' . $row->slug . '</small>';
            })
            ->editColumn('kategoriArtikel.nama', function($row) {
                return $row->kategoriArtikel ? '<span class="badge bg-primary">' . $row->kategoriArtikel->nama . '</span>' : '-';
            })
            ->editColumn('status', function($row) {
                $statusClass = $row->status === 'published' ? 'bg-success' : ($row->status === 'draft' ? 'bg-secondary' : 'bg-warning');
                return '<span class="badge ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('user.name', function($row) {
                return $row->user ? $row->user->name : '-';
            })
            ->editColumn('created_at', function($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->addColumn('actions', function($row) {
                return '
                    <div class="btn-group">
                        <a href="' . route('admin.artikel.show', $row->id) . '" class="btn btn-sm btn-info" title="View">
                            <i class="bx bx-show"></i>
                        </a>
                        <a href="' . route('admin.artikel.edit', $row->id) . '" class="btn btn-sm btn-warning" title="Edit">
                            <i class="bx bx-edit"></i>
                        </a>
                        <form action="' . route('admin.artikel.destroy', $row->id) . '" method="POST" style="display: inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm(\'Are you sure?\')">
                                <i class="bx bx-trash"></i>
                            </button>
                        </form>
                    </div>
                ';
            })
            ->rawColumns(['gambar_utama', 'judul', 'kategoriArtikel.nama', 'status', 'actions'])
            ->make(true);
    }
    
    public function create()
    {
        $kategori = KategoriArtikel::where('status', 'aktif')->orderBy('nama')->get();
        return view('admin.artikel.create', compact('kategori'));
    }
    
    public function store(StoreArtikelRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Handle file uploads
            if ($request->hasFile('gambar_utama')) {
                $data['gambar_utama'] = $request->file('gambar_utama')->store('artikel', 'public');
            }
            
            if ($request->hasFile('gambar_thumbnail')) {
                $data['gambar_thumbnail'] = $request->file('gambar_thumbnail')->store('artikel', 'public');
            }
            
            // Auto-generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = \Str::slug($data['judul']);
            }
            
            // Auto-generate meta fields if not provided
            if (empty($data['meta_title'])) {
                $data['meta_title'] = $data['judul'];
            }
            
            if (empty($data['meta_description'])) {
                $data['meta_description'] = \Str::limit(strip_tags($data['konten']), 160);
            }
            
            // Set published_at if status is published and not set
            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }
            
            $data['user_id'] = auth()->id();
            
            Artikel::create($data);
            
            return redirect()->route('admin.artikel.index')
                ->with('success', 'Artikel berhasil dibuat!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat artikel: ' . $e->getMessage());
        }
    }
    
    public function show(Artikel $artikel)
    {
        $artikel->load(['kategoriArtikel', 'user']);
        return view('admin.artikel.show', compact('artikel'));
    }
    
    public function edit(Artikel $artikel)
    {
        $kategori = KategoriArtikel::where('status', 'aktif')->orderBy('nama')->get();
        return view('admin.artikel.edit', compact('artikel', 'kategori'));
    }
    
    public function update(UpdateArtikelRequest $request, Artikel $artikel)
    {
        try {
            $data = $request->validated();
            
            // Handle file uploads
            if ($request->hasFile('gambar_utama')) {
                // Delete old image
                if ($artikel->gambar_utama) {
                    Storage::disk('public')->delete($artikel->gambar_utama);
                }
                $data['gambar_utama'] = $request->file('gambar_utama')->store('artikel', 'public');
            }
            
            if ($request->hasFile('gambar_thumbnail')) {
                // Delete old image
                if ($artikel->gambar_thumbnail) {
                    Storage::disk('public')->delete($artikel->gambar_thumbnail);
                }
                $data['gambar_thumbnail'] = $request->file('gambar_thumbnail')->store('artikel', 'public');
            }
            
            // Auto-generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = \Str::slug($data['judul']);
            }
            
            // Auto-generate meta fields if not provided
            if (empty($data['meta_title'])) {
                $data['meta_title'] = $data['judul'];
            }
            
            if (empty($data['meta_description'])) {
                $data['meta_description'] = \Str::limit(strip_tags($data['konten']), 160);
            }
            
            // Set published_at if status is published and not set
            if ($data['status'] === 'published' && empty($data['published_at']) && !$artikel->published_at) {
                $data['published_at'] = now();
            }
            
            $artikel->update($data);
            
            return redirect()->route('admin.artikel.index')
                ->with('success', 'Artikel berhasil diperbarui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui artikel: ' . $e->getMessage());
        }
    }
    
    public function destroy(Artikel $artikel)
    {
        try {
            // Delete images
            if ($artikel->gambar_utama) {
                Storage::disk('public')->delete($artikel->gambar_utama);
            }
            
            if ($artikel->gambar_thumbnail) {
                Storage::disk('public')->delete($artikel->gambar_thumbnail);
            }
            
            $artikel->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Artikel berhasil dihapus!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus artikel: ' . $e->getMessage()
            ], 500);
        }
    }
}
