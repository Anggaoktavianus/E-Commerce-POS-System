<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use App\Http\Requests\StoreKategoriArtikelRequest;
use App\Http\Requests\UpdateKategoriArtikelRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class KategoriArtikelController extends Controller
{
    public function index()
    {
        return view('admin.kategori_artikel.index');
    }

    public function data()
    {
        $kategori = KategoriArtikel::withCount(['artikels' => function($query) {
            $query->where('status', 'published');
        }])->get();

        return DataTables::of($kategori)
            ->addIndexColumn()
            ->addColumn('actions', function ($kategori) {
                return '
                    <a href="'.route('admin.kategori_artikel.edit', $kategori).'" class="btn rounded-pill btn-xs btn-outline-warning" title="Edit">
                        <i class="bx bx-edit"></i>
                    </a>
                    <button type="button" class="btn rounded-pill btn-xs  btn-outline-danger" onclick="deleteItem('.$kategori->id.')" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                ';
            })
            ->addColumn('status', function ($kategori) {
                $badge = $kategori->status === 'aktif' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge '.$badge.'">'.ucfirst($kategori->status).'</span>';
            })
            ->addColumn('gambar', function ($kategori) {
                if ($kategori->gambar) {
                    $imageUrl = Storage::url($kategori->gambar);
                    return '<img src="'.$imageUrl.'" alt="'.$kategori->nama.'" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" onclick="window.open(\''.$imageUrl.'\', \'_blank\')">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bx bx-image text-muted"></i></div>';
            })
            ->addColumn('artikel_count', function ($kategori) {
                return $kategori->artikels_count;
            })
            ->rawColumns(['actions', 'status', 'gambar'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.kategori_artikel.create');
    }

    public function store(StoreKategoriArtikelRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('kategori-artikel', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama']);
        }

        KategoriArtikel::create($data);

        return redirect()->route('admin.kategori_artikel.index')
            ->with('success', 'Kategori artikel berhasil ditambahkan');
    }

    public function show(KategoriArtikel $kategoriArtikel)
    {
        return view('admin.kategori_artikel.show', compact('kategoriArtikel'));
    }

    public function edit(KategoriArtikel $kategoriArtikel)
    {
        return view('admin.kategori_artikel.edit', compact('kategoriArtikel'));
    }

    public function update(UpdateKategoriArtikelRequest $request, KategoriArtikel $kategoriArtikel)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($kategoriArtikel->gambar) {
                Storage::disk('public')->delete($kategoriArtikel->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('kategori-artikel', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['nama']);
        }

        $kategoriArtikel->update($data);

        return redirect()->route('admin.kategori_artikel.index')
            ->with('success', 'Kategori artikel berhasil diperbarui');
    }

    public function destroy(KategoriArtikel $kategoriArtikel)
    {
        if ($kategoriArtikel->artikels()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki artikel'
            ]);
        }

        if ($kategoriArtikel->gambar) {
            Storage::disk('public')->delete($kategoriArtikel->gambar);
        }

        $kategoriArtikel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori artikel berhasil dihapus'
        ]);
    }
}
