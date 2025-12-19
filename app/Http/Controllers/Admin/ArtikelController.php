<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\KategoriArtikel;
use App\Http\Requests\StoreArtikelRequest;
use App\Http\Requests\UpdateArtikelRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ArtikelController extends Controller
{
    public function index()
    {
        return view('admin.artikel.index');
    }

    public function data()
    {
        $artikel = Artikel::with(['kategoriArtikel', 'user'])->get();

        return DataTables::of($artikel)
            ->addIndexColumn()
            ->addColumn('actions', function ($artikel) {
                return '
                    <a href="'.route('admin.artikel.show', $artikel).'" class="btn rounded-pill btn-xs btn-outline-info" title="View">
                        <i class="bx bx-show"></i>
                    </a>
                    <a href="'.route('admin.artikel.edit', $artikel).'" class="btn rounded-pill btn-xs btn-outline-warning" title="Edit">
                        <i class="bx bx-edit"></i>
                    </a>
                    <button type="button" class="btn rounded-pill btn-xs btn-outline-danger" onclick="deleteItem('.$artikel->id.')" title="Delete">
                        <i class="bx bx-trash"></i>
                    </button>
                ';
            })
            ->addColumn('kategori', function ($artikel) {
                return $artikel->kategoriArtikel ? '<span class="badge bg-primary">'.$artikel->kategoriArtikel->nama.'</span>' : '-';
            })
            ->addColumn('status', function ($artikel) {
                $badges = [
                    'draft' => 'bg-secondary',
                    'published' => 'bg-success',
                    'archived' => 'bg-warning'
                ];
                $badge = $badges[$artikel->status] ?? 'bg-secondary';
                return '<span class="badge '.$badge.'">'.ucfirst($artikel->status).'</span>';
            })
            ->addColumn('gambar_utama', function ($artikel) {
                if ($artikel->gambar_thumbnail) {
                    $imageUrl = Storage::url($artikel->gambar_thumbnail);
                    return '<img src="'.$imageUrl.'" alt="'.$artikel->judul.'" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" onclick="window.open(\''.$imageUrl.'\', \'_blank\')">';
                } elseif ($artikel->gambar_utama) {
                    $imageUrl = Storage::url($artikel->gambar_utama);
                    return '<img src="'.$imageUrl.'" alt="'.$artikel->judul.'" class="img-thumbnail rounded" style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;" onclick="window.open(\''.$imageUrl.'\', \'_blank\')">';
                }
                return '<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="bx bx-image text-muted"></i></div>';
            })
            ->addColumn('created_at', function ($artikel) {
                return $artikel->created_at->format('d M Y H:i');
            })
            ->addColumn('views', function ($artikel) {
                return $artikel->views ?? 0;
            })
            ->rawColumns(['actions', 'kategori', 'status', 'gambar_utama'])
            ->make(true);
    }

    public function create()
    {
        $kategori = KategoriArtikel::where('status', 'aktif')->get();
        return view('admin.artikel.create', compact('kategori'));
    }

    public function store(StoreArtikelRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar_utama')) {
            $data['gambar_utama'] = $request->file('gambar_utama')->store('artikel', 'public');
        }

        if ($request->hasFile('gambar_thumbnail')) {
            $data['gambar_thumbnail'] = $request->file('gambar_thumbnail')->store('artikel/thumbnails', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['judul']);
        }

        $data['user_id'] = auth()->id();

        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        Artikel::create($data);

        return redirect()->route('admin.artikel.index')
            ->with('success', 'Artikel berhasil ditambahkan');
    }

    public function show(Artikel $artikel)
    {
        $artikel->increment('views');
        return view('admin.artikel.show', compact('artikel'));
    }

    public function edit(Artikel $artikel)
    {
        $kategori = KategoriArtikel::where('status', 'aktif')->get();
        return view('admin.artikel.edit', compact('artikel', 'kategori'));
    }

    public function update(UpdateArtikelRequest $request, Artikel $artikel)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar_utama')) {
            if ($artikel->gambar_utama) {
                Storage::disk('public')->delete($artikel->gambar_utama);
            }
            $data['gambar_utama'] = $request->file('gambar_utama')->store('artikel', 'public');
        }

        if ($request->hasFile('gambar_thumbnail')) {
            if ($artikel->gambar_thumbnail) {
                Storage::disk('public')->delete($artikel->gambar_thumbnail);
            }
            $data['gambar_thumbnail'] = $request->file('gambar_thumbnail')->store('artikel/thumbnails', 'public');
        }

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['judul']);
        }

        if ($data['status'] === 'published' && !$artikel->published_at) {
            $data['published_at'] = now();
        }

        $artikel->update($data);

        return redirect()->route('admin.artikel.index')
            ->with('success', 'Artikel berhasil diperbarui');
    }

    public function destroy(Artikel $artikel)
    {
        if ($artikel->gambar_utama) {
            Storage::disk('public')->delete($artikel->gambar_utama);
        }

        if ($artikel->gambar_thumbnail) {
            Storage::disk('public')->delete($artikel->gambar_thumbnail);
        }

        $artikel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dihapus'
        ]);
    }
}
