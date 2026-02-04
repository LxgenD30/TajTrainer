<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Display a listing of materials.
     */
    public function index(Request $request)
    {
        $query = Material::with('items')->orderBy('created_at', 'desc');
        
        // Filter by category if provided
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        $materials = $query->paginate(12);
        $isStudent = auth()->check() && auth()->user()->role_id == 2;
        
        // Get category counts for filter badges
        $categoryCounts = [
            'all' => Material::count(),
            'Madd Rules' => Material::where('category', 'Madd Rules')->count(),
            'Idgham Billa Ghunnah' => Material::where('category', 'Idgham Billa Ghunnah')->count(),
            'Idgham Bi Ghunnah' => Material::where('category', 'Idgham Bi Ghunnah')->count(),
        ];
        
        return view('materials.index', compact('materials', 'isStudent', 'categoryCounts'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create()
    {
        return view('materials.create');
    }

    /**
     * Search for educational materials online using Tavily API.
     */
    public function searchOnline(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|max:255',
            'type' => 'nullable|in:pdf,youtube',
        ]);

        $searchQuery = $validated['query'];
        $searchType = $validated['type'] ?? 'pdf';
        $apiKey = config('services.tavily.api_key');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Tavily API key not configured. Please add TAVILY_API_KEY to your .env file.'
            ], 500);
        }

        try {
            // Build search query based on type
            if ($searchType === 'youtube') {
                $finalQuery = $searchQuery . ' site:youtube.com';
            } else {
                // Filter for educational institutions and PDF files
                $finalQuery = $searchQuery . ' (site:.edu OR site:.ac.uk OR site:.gov OR site:scholar.google.com) filetype:pdf';
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.tavily.com/search', [
                'query' => $finalQuery,
                'search_depth' => 'basic',
                'max_results' => 4,
                'include_images' => $searchType === 'youtube',
                'include_answer' => false,
                'topic' => 'general',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'] ?? [];
                
                // Process results based on type
                $processedResults = [];
                foreach ($results as $result) {
                    $processed = [
                        'title' => $result['title'] ?? '',
                        'url' => $result['url'] ?? '',
                        'content' => $result['content'] ?? '',
                        'type' => $searchType,
                    ];
                    
                    if ($searchType === 'pdf') {
                        // Check if URL is a direct PDF link
                        $processed['is_pdf'] = str_ends_with(strtolower($result['url'] ?? ''), '.pdf');
                        $processed['download_url'] = $processed['is_pdf'] ? $result['url'] : null;
                    } elseif ($searchType === 'youtube') {
                        // Extract YouTube video ID
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/', $result['url'] ?? '', $matches)) {
                            $processed['video_id'] = $matches[1];
                            $processed['thumbnail'] = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                        }
                    }
                    
                    $processedResults[] = $processed;
                }
                
                return response()->json([
                    'success' => true,
                    'results' => $processedResults,
                    'search_type' => $searchType,
                ]);
            } else {
                Log::error('Tavily API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to search online resources. Please try again.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Tavily Search Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching. Please try again.'
            ], 500);
        }
    }

    /**
     * Suggest a category for a material using AI
     */
    public function suggestCategory(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'nullable|string'
            ]);

            $text = $validated['title'];
            if (!empty($validated['description'])) {
                $text .= ' ' . $validated['description'];
            }

            $category = $this->categorizeMaterial($text);

            if ($category) {
                return response()->json([
                    'success' => true,
                    'category' => $category,
                    'message' => 'AI suggested category based on content analysis'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to determine category. Please select manually.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Category Suggestion Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please select category manually.'
            ], 500);
        }
    }

    /**
     * Store a newly created material in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Madd Rules,Idgham Billa Ghunnah,Idgham Bi Ghunnah',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Multiple items support
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:file,youtube,url',
            'items.*.file' => 'nullable|file|mimes:pdf,doc,docx,mp3,mp4|max:20480',
            'items.*.youtube_link' => 'nullable|url',
            'items.*.url' => 'nullable|url',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Create the main material
            $material = new Material();
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->is_public = true;
            
            // Category is now required - teacher must select manually
            $material->category = $validated['category'];

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

            $material->save();

            // Process material items
            if (!empty($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $itemData) {
                    $item = new MaterialItem();
                    $item->material_id = $material->material_id;
                    $item->type = $itemData['type'];
                    $item->title = $itemData['title'] ?? null;
                    $item->description = $itemData['description'] ?? null;

                    // Handle different item types
                    if ($itemData['type'] === 'file' && $request->hasFile("items.{$index}.file")) {
                        $file = $request->file("items.{$index}.file");
                        if ($file->getError() === UPLOAD_ERR_OK && $file->isValid() && $file->getSize() > 0) {
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $destinationPath = storage_path('app/public/materials');
                            
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            
                            $fullPath = $destinationPath . '/' . $filename;
                            
                            if (move_uploaded_file($file->getPathname(), $fullPath)) {
                                $item->path = 'materials/' . $filename;
                            }
                        }
                    } elseif ($itemData['type'] === 'youtube') {
                        $item->path = $itemData['youtube_link'] ?? null;
                        
                        // Auto-extract thumbnail if not set
                        if (!$material->thumbnail && $item->path) {
                            if (preg_match('/(?:youtube\\.com\\/(?:[^\\/]+\\/.+\\/|(?:v|e(?:mbed)?)\\/|.*[?&]v=)|youtu\\.be\\/)([^"&?\\/ ]{11})/', $item->path, $matches)) {
                                $material->thumbnail = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                                $material->save();
                            }
                        }
                    } elseif ($itemData['type'] === 'url') {
                        $item->path = $itemData['url'] ?? null;
                    }

                    if ($item->path) {
                        $item->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('materials.index')->with('success', 'Material created successfully with ' . $material->items()->count() . ' item(s)!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material creation failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to create material. Please try again.']);
        }
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material)
    {
        // Eager load items
        $material->load('items');
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

    /**
     * Auto-categorize material using OpenAI API based on title and description.
     */
    private function categorizeMaterial($title, $description = null)
    {
        $apiKey = config('services.openai.api_key');
        
        if (!$apiKey) {
            Log::warning('OpenAI API key not configured for auto-categorization');
            return 'Madd Rules'; // Default category
        }

        try {
            $content = "Title: $title";
            if ($description) {
                $content .= "\nDescription: $description";
            }

            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert in Tajweed (Quranic recitation rules). Analyze the given material and categorize it into exactly ONE of these categories: "Madd Rules", "Idgham Billa Ghunnah", or "Idgham Bi Ghunnah". Respond with ONLY the category name, nothing else.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $content
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 50,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $category = trim($data['choices'][0]['message']['content'] ?? '');
                
                // Validate category
                $validCategories = ['Madd Rules', 'Idgham Billa Ghunnah', 'Idgham Bi Ghunnah'];
                if (in_array($category, $validCategories)) {
                    return $category;
                }
                
                Log::warning('OpenAI returned invalid category: ' . $category);
            } else {
                Log::error('OpenAI API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Categorization Exception: ' . $e->getMessage());
        }

        // Default to Madd Rules if categorization fails
        return 'Madd Rules';
    }
}

