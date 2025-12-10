<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.index');
    }

    /**
     * Get data for DataTables server-side processing.
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            try {
                $pages = Page::latest();
                
                return DataTables::of($pages)
                    ->addIndexColumn()
                    ->addColumn('title', function($page) {
                        return $page->title;
                    })
                    ->addColumn('slug', function($page) {
                        return '<span class="badge bg-label-secondary">' . $page->slug . '</span>';
                    })
                    ->addColumn('status', function($page) {
                        if ($page->is_published) {
                            return '<span class="badge bg-success"><i class="bx bx-show me-1"></i>Diterbitkan</span>';
                        } else {
                            return '<span class="badge bg-warning"><i class="bx bx-edit me-1"></i>Draft</span>';
                        }
                    })
                    ->addColumn('created_at', function($page) {
                        return '<span class="text-muted">' . $page->created_at->format('d M Y') . '</span><br>' .
                               '<small class="text-muted">' . $page->created_at->format('H:i') . '</small>';
                    })
                    ->addColumn('action', function($page) {
                        return '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('pages.show', $page->slug) . '" target="_blank">
                                    <i class="bx bx-show me-1"></i> Lihat
                                </a>
                                <a class="dropdown-item" href="' . route('admin.pages.edit', $page->id) . '">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <button type="button" class="dropdown-item text-danger delete-page" data-id="' . $page->id . '">
                                    <i class="bx bx-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>';
                    })
                    ->rawColumns(['slug', 'status', 'created_at', 'action'])
                    ->make(true);
                    
            } catch (\Exception $e) {
                \Log::error('Error in getData: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Server error occurred',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
        
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'attachments.*' => 'nullable|file|max:5120',
            'video_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_published' => 'boolean'
        ]);

        $data = $validated;
        $data['slug'] = Page::generateSlug($data['title']);
        $data['created_by'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published');

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('uploads/pages', 'public');
            $data['featured_image'] = 'storage/'.$path;
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/pages/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => 'storage/'.$path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
            $data['attachments'] = json_encode($attachments);
        }

        Page::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
                   ->where('is_published', true)
                   ->firstOrFail();
        
        // Add view count if needed
        // $page->increment('view_count');
        
        // Set meta tags
        $page->setMeta();
        
        return view('pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'attachments.*' => 'nullable|file|max:5120',
            'video_url' => 'nullable|url',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_published' => 'boolean',
            'remove_attachments' => 'array',
            'remove_attachments.*' => 'string'
        ]);

        $data = $validated;
        $data['is_published'] = $request->boolean('is_published');

        // Handle featured image update
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($page->featured_image && str_starts_with($page->featured_image, 'storage/')) {
                $rel = substr($page->featured_image, 8);
                Storage::disk('public')->delete($rel);
            }
            $path = $request->file('featured_image')->store('uploads/pages', 'public');
            $data['featured_image'] = 'storage/'.$path;
        }

        // Handle file attachments
        $attachments = $page->attachments ? json_decode($page->attachments, true) : [];
        
        // Remove attachments marked for deletion
        if (!empty($validated['remove_attachments'])) {
            foreach ($validated['remove_attachments'] as $path) {
                if (str_starts_with($path, 'storage/')) {
                    $rel = substr($path, 8);
                    Storage::disk('public')->delete($rel);
                }
                $attachments = array_filter($attachments, function($item) use ($path) {
                    return $item['path'] !== $path;
                });
            }
        }

        // Add new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/pages/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => 'storage/'.$path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType()
                ];
            }
        }

        $data['attachments'] = !empty($attachments) ? json_encode(array_values($attachments)) : null;

        $page->update($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        // Delete featured image
        if ($page->featured_image && str_starts_with($page->featured_image, 'storage/')) {
            $rel = substr($page->featured_image, 8);
            Storage::disk('public')->delete($rel);
        }
        
        // Delete attachments
        if ($page->attachments) {
            $attachments = json_decode($page->attachments, true);
            foreach ($attachments as $attachment) {
                if (str_starts_with($attachment['path'], 'storage/')) {
                    $rel = substr($attachment['path'], 8);
                    Storage::disk('public')->delete($rel);
                }
            }
        }
        
        $page->delete();

        return response()->json(['success' => 'Halaman berhasil dihapus']);
    }
}
