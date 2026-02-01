<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index()
    {
        $materials = Material::orderBy('created_at', 'desc')->paginate(12);
        return view('materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create()
    {
        return view('materials.create');
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp3,mp4|max:20480',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'nullable|boolean',
        ]);

        $material = new Material();
        $material->title = $validated['title'];
        $material->description = $validated['description'] ?? null;
        $material->video_link = $validated['video_link'] ?? null;
        $material->is_public = $request->has('is_public');

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->getError() === UPLOAD_ERR_OK && $file->isValid() && $file->getSize() > 0) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/materials');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                
                if (move_uploaded_file($file->getPathname(), $fullPath)) {
                    $material->file_path = 'materials/' . $filename;
                }
            }
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail->getError() === UPLOAD_ERR_OK && $thumbnail->isValid() && $thumbnail->getSize() > 0) {
                $filename = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/thumbnails');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                
                if (move_uploaded_file($thumbnail->getPathname(), $fullPath)) {
                    $material->thumbnail = 'thumbnails/' . $filename;
                }
            }
        }

        // Auto-extract YouTube thumbnail if video link provided but no thumbnail uploaded
        if (!$material->thumbnail && $material->video_link) {
            if (preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/ ]{11})/', $material->video_link, $matches)) {
                $material->thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
            }
        }

        $material->save();

        return redirect()->route('materials.index')->with('success', 'Material created successfully!');
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    /**
     * Update the specified material in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,mp3,mp4|max:20480',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'nullable|boolean',
        ]);

        $material->title = $validated['title'];
        $material->description = $validated['description'] ?? null;
        $material->video_link = $validated['video_link'] ?? null;
        $material->is_public = $request->has('is_public');

        // Handle file upload and delete old file
        if ($request->hasFile('file') && $request->file('file')->getError() === UPLOAD_ERR_OK) {
            $file = $request->file('file');
            if ($file->isValid() && $file->getSize() > 0) {
                // Delete old file if exists
                if ($material->file_path) {
                    $oldPath = storage_path('app/public/' . $material->file_path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/materials');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                
                if (move_uploaded_file($file->getPathname(), $fullPath)) {
                    $material->file_path = 'materials/' . $filename;
                }
            }
        }

        // Handle thumbnail upload and delete old thumbnail
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->getError() === UPLOAD_ERR_OK) {
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail->isValid() && $thumbnail->getSize() > 0) {
                // Delete old thumbnail if exists
                if ($material->thumbnail) {
                    $oldPath = storage_path('app/public/' . $material->thumbnail);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $filename = time() . '_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/thumbnails');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $fullPath = $destinationPath . '/' . $filename;
                
                if (move_uploaded_file($thumbnail->getPathname(), $fullPath)) {
                    $material->thumbnail = 'thumbnails/' . $filename;
                }
            }
        }

        // Auto-extract YouTube thumbnail if video link provided but no custom thumbnail uploaded
        if ($material->video_link && !$request->hasFile('thumbnail')) {
            if (preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/ ]{11})/', $material->video_link, $matches)) {
                $material->thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
            }
        }

        $material->save();

        return redirect()->route('materials.show', $material->material_id)->with('success', 'Material updated successfully!');
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(Material $material)
    {
        // Delete associated files
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        if ($material->thumbnail_path && !filter_var($material->thumbnail_path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($material->thumbnail_path);
        }

        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material deleted successfully!');
    }

    /**
     * Extract YouTube thumbnail URL from video link.
     */
    private function extractYoutubeThumbnail($url)
    {
        // Extract YouTube video ID from various URL formats
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        
        if (isset($matches[1])) {
            $videoId = $matches[1];
            return "https://img.youtube.com/vi/{$videoId}/maxresdefault.jpg";
        }

        return null;
    }
}

