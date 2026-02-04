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
        
        // Search by title or description
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Filter by category if provided
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
        
        $materials = $query->paginate(12);
        $isStudent = auth()->check() && auth()->user()->role_id == 2;
        
        // Get category counts for filter badges (respecting search filter)
        $baseQuery = Material::query();
        
        // Apply search filter to counts if present
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $baseQuery->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $categoryCounts = [
            'all' => (clone $baseQuery)->count(),
            'Madd Rules' => (clone $baseQuery)->where('category', 'Madd Rules')->count(),
            'Idgham Billa Ghunnah' => (clone $baseQuery)->where('category', 'Idgham Billa Ghunnah')->count(),
            'Idgham Bi Ghunnah' => (clone $baseQuery)->where('category', 'Idgham Bi Ghunnah')->count(),
            'Others' => (clone $baseQuery)->where('category', 'Others')->count(),
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
            // Build search query and domain filters based on type
            if ($searchType === 'youtube') {
                // Add Islamic context to ensure relevant results, but prioritize user's search
                $enhancedQuery = $searchQuery . ' (Islam OR Islamic OR Muslim OR Quran OR Quranic)';
                
                $searchParams = [
                    'query' => $enhancedQuery,
                    'search_depth' => 'basic',
                    'max_results' => 10,
                    'include_images' => true,
                    'include_answer' => false,
                    'topic' => 'general',
                    'include_domains' => ['youtube.com', 'youtu.be'],
                ];
            } else {
                // PDF search - add Tajweed/Islamic context to query
                $enhancedQuery = $searchQuery . ' (tajweed OR Quran OR Quranic OR Islamic OR "Arabic recitation" OR "Quran studies") filetype:pdf';
                
                // PDF search - only educational institutions and Google Scholar
                $searchParams = [
                    'query' => $enhancedQuery,
                    'search_depth' => 'advanced',
                    'max_results' => 10,
                    'include_images' => false,
                    'include_answer' => false,
                    'topic' => 'general',
                    'include_domains' => [
                        'scholar.google.com',
                        '*.edu',
                        '*.ac.uk',
                        '*.gov',
                        'researchgate.net',
                        'academia.edu',
                        'arxiv.org',
                        'jstor.org',
                        'sciencedirect.com',
                        'springer.com',
                        'wiley.com',
                        'tandfonline.com',
                        'cambridge.org',
                        'oxford.ac.uk',
                    ],
                ];
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.tavily.com/search', $searchParams);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'] ?? [];
                
                // Islamic keywords for content filtering (broader scope)
                $islamicKeywords = [
                    // Core Islamic terms
                    'tajweed', 'quran', 'quranic', 'islamic', 'islam', 'muslim', 'recitation',
                    'surah', 'ayah', 'verse', 'chapter', 'arabic', 'prophet', 'muhammad',
                    // Tajweed specific
                    'idgham', 'ghunnah', 'madd', 'qalqalah', 'ikhfa', 'iqlab',
                    'izhar', 'noon sakinah', 'tanween', 'makhraj', 'makharij',
                    'tilawah', 'tarteel', 'mujawwad', 'hafs', 'warsh', 'qira',
                    // Quranic terms
                    'al-fatiha', 'al-baqarah', 'al-kahf', 'yaseen', 'rahman',
                    'juz', 'hizb', 'ruku', 'sajdah', 'bismillah', 'alhamdulillah',
                    // General Islamic
                    'mosque', 'prayer', 'salah', 'allah', 'quran reading', 'quran learning',
                    'islamic studies', 'arabic learning', 'qari', 'sheikh', 'imam'
                ];
                
                // Process results based on type
                $processedResults = [];
                foreach ($results as $result) {
                    $title = strtolower($result['title'] ?? '');
                    $content = strtolower($result['content'] ?? '');
                    $combinedText = $title . ' ' . $content;
                    
                    // Check if result contains Islamic/Tajweed keywords
                    $isRelevant = false;
                    foreach ($islamicKeywords as $keyword) {
                        if (stripos($combinedText, $keyword) !== false) {
                            $isRelevant = true;
                            break;
                        }
                    }
                    
                    // Skip irrelevant results
                    if (!$isRelevant) {
                        Log::info('Skipping irrelevant result', [
                            'title' => $result['title'] ?? '',
                            'url' => $result['url'] ?? ''
                        ]);
                        continue;
                    }
                    
                    $processed = [
                        'title' => $result['title'] ?? '',
                        'url' => $result['url'] ?? '',
                        'content' => $result['content'] ?? '',
                        'type' => $searchType,
                    ];
                    
                    if ($searchType === 'pdf') {
                        // Mark as PDF since we specifically searched for PDFs
                        $processed['is_pdf'] = true;
                        $processed['download_url'] = $result['url'];
                    } elseif ($searchType === 'youtube') {
                        // Filter out YouTube channels, playlists, and non-video pages
                        $url = $result['url'] ?? '';
                        
                        // Skip if URL contains channel, user, playlist, or @username patterns
                        if (preg_match('/(\/channel\/|\/user\/|\/c\/|\/playlist|\/\@[^\/]+$|\/\@[^\/]+\/videos|\/\@[^\/]+\/playlists|\/\@[^\/]+\/community|\/\@[^\/]+\/about)/', $url)) {
                            Log::info('Skipping YouTube channel/playlist page', [
                                'title' => $result['title'] ?? '',
                                'url' => $url
                            ]);
                            continue;
                        }
                        
                        // Extract YouTube video ID - only process if it's a valid video URL
                        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([^"&?\/#\s]{11})/', $url, $matches)) {
                            $processed['video_id'] = $matches[1];
                            $processed['thumbnail'] = 'https://img.youtube.com/vi/' . $matches[1] . '/maxresdefault.jpg';
                        } else {
                            // Not a valid video URL, skip it
                            Log::info('Skipping non-video YouTube URL', [
                                'title' => $result['title'] ?? '',
                                'url' => $url
                            ]);
                            continue;
                        }
                    }
                    
                    $processedResults[] = $processed;
                }
                
                return response()->json([
                    'success' => true,
                    'results' => $processedResults,
                    'search_type' => $searchType,
                    'filtered_count' => count($results) - count($processedResults),
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
     * Generate title and description using AI
     */
    public function generateInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'context' => 'required|string'
            ]);

            $apiKey = config('services.openai.api_key');
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API key not configured'
                ], 500);
            }

            $context = $validated['context'];
            
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an educational content specialist for Islamic studies. Generate a concise, professional title and description for a learning material collection. Return ONLY a JSON object with "title" and "description" fields. Title should be 3-8 words. Description should be 1-2 sentences explaining what students will learn.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $context
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = trim($data['choices'][0]['message']['content'] ?? '');
                
                Log::info('OpenAI Generate Info Response', [
                    'input' => $context,
                    'raw_response' => $content
                ]);

                // Try to parse JSON response
                $generated = json_decode($content, true);
                
                if ($generated && isset($generated['title'])) {
                    return response()->json([
                        'success' => true,
                        'title' => $generated['title'] ?? '',
                        'description' => $generated['description'] ?? '',
                        'message' => 'Information generated successfully'
                    ]);
                } else {
                    Log::warning('OpenAI returned invalid format: ' . $content);
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not parse AI response'
                    ]);
                }
            } else {
                $statusCode = $response->status();
                $errorBody = $response->json();
                $errorMessage = 'AI service error';
                
                // Check for specific OpenAI errors
                if (isset($errorBody['error']['code'])) {
                    $errorCode = $errorBody['error']['code'];
                    if ($errorCode === 'insufficient_quota' || $statusCode === 429) {
                        $errorMessage = 'OpenAI quota exceeded. Please contact administrator or try again later.';
                    } elseif ($errorCode === 'invalid_api_key') {
                        $errorMessage = 'OpenAI API key is invalid. Please contact administrator.';
                    }
                }
                
                Log::error('OpenAI API Error: ' . $statusCode . ' | Body: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Generate Info Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
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
            'category' => 'required|in:Madd Rules,Idgham Billa Ghunnah,Idgham Bi Ghunnah,Others',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // Multiple items support
            'items' => 'nullable|array',
            'items.*.type' => 'required|in:file,youtube,url',
            'items.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp|max:20480',
            'items.*.youtube_link' => 'nullable|url',
            'items.*.pdf_url' => 'nullable|url',
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
            
            // Set the creator - ensure user is a teacher
            if (!auth()->user()->teacher) {
                DB::rollBack();
                return back()->withInput()->withErrors(['error' => 'Only teachers can create materials.']);
            }
            $material->teacher_id = auth()->user()->teacher->id;
            
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
                Log::info('Processing material items', ['count' => count($validated['items'])]);
                
                foreach ($validated['items'] as $index => $itemData) {
                    Log::info('Processing item', [
                        'index' => $index,
                        'type' => $itemData['type'],
                        'has_file' => $request->hasFile("items.{$index}.file"),
                        'has_pdf_url' => !empty($itemData['pdf_url']),
                        'pdf_url' => $itemData['pdf_url'] ?? null,
                    ]);
                    
                    $item = new MaterialItem();
                    $item->material_id = $material->material_id;
                    $item->type = $itemData['type'];
                    $item->title = $itemData['title'] ?? null;
                    $item->description = $itemData['description'] ?? null;

                    // Handle different item types
                    if ($itemData['type'] === 'file' && $request->hasFile("items.{$index}.file")) {
                        $file = $request->file("items.{$index}.file");
                        Log::info('Processing file upload', [
                            'item_index' => $index,
                            'filename' => $file->getClientOriginalName(),
                            'extension' => $file->getClientOriginalExtension(),
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ]);
                        
                        if ($file->getError() === UPLOAD_ERR_OK && $file->isValid() && $file->getSize() > 0) {
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $destinationPath = storage_path('app/public/materials');
                            
                            if (!file_exists($destinationPath)) {
                                mkdir($destinationPath, 0755, true);
                            }
                            
                            $fullPath = $destinationPath . '/' . $filename;
                            
                            if (move_uploaded_file($file->getPathname(), $fullPath)) {
                                $item->path = 'materials/' . $filename;
                                Log::info('File saved successfully', [
                                    'path' => $item->path,
                                    'full_path' => $fullPath,
                                ]);
                            } else {
                                Log::error('Failed to move uploaded file', [
                                    'temp_path' => $file->getPathname(),
                                    'destination' => $fullPath,
                                ]);
                            }
                        } else {
                            Log::error('File validation failed', [
                                'error' => $file->getError(),
                                'is_valid' => $file->isValid(),
                                'size' => $file->getSize(),
                            ]);
                        }
                    } elseif ($itemData['type'] === 'file' && !empty($itemData['pdf_url'])) {
                        // PDF from search result - try to download it
                        Log::info('Attempting to download PDF from search', [
                            'url' => $itemData['pdf_url'],
                            'index' => $index
                        ]);
                        
                        try {
                            $downloadedPath = $this->downloadPDF($itemData['pdf_url']);
                            if ($downloadedPath) {
                                $item->path = $downloadedPath;
                                Log::info('PDF downloaded successfully', [
                                    'url' => $itemData['pdf_url'],
                                    'path' => $downloadedPath,
                                    'full_path' => storage_path('app/public/' . $downloadedPath)
                                ]);
                            } else {
                                // Download failed, save as external URL instead
                                Log::warning('PDF download failed, saving as URL instead', [
                                    'url' => $itemData['pdf_url']
                                ]);
                                $item->type = 'url';
                                $item->path = $itemData['pdf_url'];
                            }
                        } catch (\Exception $e) {
                            Log::error('PDF download exception', [
                                'url' => $itemData['pdf_url'],
                                'error' => $e->getMessage(),
                                'trace' => $e->getTraceAsString()
                            ]);
                            // Save as URL on exception
                            $item->type = 'url';
                            $item->path = $itemData['pdf_url'];
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
                        $urlPath = $itemData['url'] ?? null;
                        
                        // Check if URL is a PDF and download it
                        if ($urlPath && str_ends_with(strtolower($urlPath), '.pdf')) {
                            try {
                                $downloadedPath = $this->downloadPDF($urlPath);
                                if ($downloadedPath) {
                                    // Switch to file type since we downloaded it
                                    $item->type = 'file';
                                    $item->path = $downloadedPath;
                                } else {
                                    // Keep as URL if download failed
                                    $item->path = $urlPath;
                                }
                            } catch (\Exception $e) {
                                Log::warning('PDF download failed: ' . $e->getMessage());
                                $item->path = $urlPath;
                            }
                        } else {
                            $item->path = $urlPath;
                        }
                    }

                    // Save item if it has a path OR if it's youtube type (path will be set)
                    if ($item->path || $itemData['type'] === 'youtube') {
                        $item->save();
                        Log::info('Material item saved', [
                            'item_id' => $item->item_id,
                            'type' => $item->type,
                            'path' => $item->path,
                            'material_id' => $item->material_id,
                            'title' => $item->title
                        ]);
                    } else {
                        Log::warning('Material item not saved - no path', [
                            'type' => $itemData['type'],
                            'index' => $index,
                            'item_data' => $itemData
                        ]);
                    }
                }
                
                Log::info('Finished processing all items');
            }

            DB::commit();

            Log::info('Material created successfully', [
                'material_id' => $material->material_id,
                'items_count' => $material->items()->count()
            ]);

            return redirect()->route('materials.index')->with('success', 'Material created successfully with ' . $material->items()->count() . ' item(s)!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->withErrors(['error' => 'Failed to create material: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified material.
     */
    public function show(Material $material)
    {
        // Eager load items and teacher with user
        $material->load(['items', 'teacher.user']);
        $isStudent = auth()->check() && auth()->user()->role_id == 2;
        return view('materials.show', compact('material', 'isStudent'));
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Material $material)
    {
        // Check if the current user is a teacher and is the owner
        if (!auth()->user()->teacher || $material->teacher_id !== auth()->user()->teacher->id) {
            abort(403, 'You are not authorized to edit this material.');
        }
        
        // Eager load items
        $material->load('items');
        return view('materials.edit', compact('material'));
    }

    /**
     * Update the specified material in storage.
     */
    public function update(Request $request, Material $material)
    {
        // Check if the current user is a teacher and is the owner
        if (!auth()->user()->teacher || $material->teacher_id !== auth()->user()->teacher->id) {
            abort(403, 'You are not authorized to update this material.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:Madd Rules,Idgham Billa Ghunnah,Idgham Bi Ghunnah,Others',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_public' => 'nullable|boolean',
            
            // Multiple items support
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|integer', // Existing item ID
            'items.*.type' => 'required|in:file,youtube,url',
            'items.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp|max:20480',
            'items.*.youtube_link' => 'nullable|url',
            'items.*.title' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Update basic material info
            $material->title = $validated['title'];
            $material->description = $validated['description'] ?? null;
            $material->category = $validated['category'];
            $material->is_public = $request->has('is_public');

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                if ($thumbnail->getError() === UPLOAD_ERR_OK && $thumbnail->isValid() && $thumbnail->getSize() > 0) {
                    // Delete old thumbnail if exists and not a URL
                    if ($material->thumbnail && !filter_var($material->thumbnail, FILTER_VALIDATE_URL)) {
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

            $material->save();

            // Track which existing items are being kept
            $keptItemIds = [];

            // Process material items
            if (!empty($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $itemData) {
                    // Check if this is an update to existing item
                    if (!empty($itemData['id'])) {
                        $item = MaterialItem::find($itemData['id']);
                        if ($item && $item->material_id == $material->material_id) {
                            $keptItemIds[] = $item->item_id;
                        } else {
                            $item = new MaterialItem();
                            $item->material_id = $material->material_id;
                        }
                    } else {
                        // New item
                        $item = new MaterialItem();
                        $item->material_id = $material->material_id;
                    }

                    $item->type = $itemData['type'];
                    $item->title = $itemData['title'] ?? null;
                    $item->description = $itemData['description'] ?? null;

                    // Handle different item types
                    if ($itemData['type'] === 'file' && $request->hasFile("items.{$index}.file")) {
                        $file = $request->file("items.{$index}.file");
                        if ($file->getError() === UPLOAD_ERR_OK && $file->isValid() && $file->getSize() > 0) {
                            // Delete old file if exists
                            if ($item->path && !filter_var($item->path, FILTER_VALIDATE_URL)) {
                                $oldPath = storage_path('app/public/' . $item->path);
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
                                $item->path = 'materials/' . $filename;
                            }
                        }
                    } elseif ($itemData['type'] === 'file' && !empty($itemData['pdf_url'])) {
                        // PDF from search result - download it
                        try {
                            $downloadedPath = $this->downloadPDF($itemData['pdf_url']);
                            if ($downloadedPath) {
                                // Delete old file if exists
                                if ($item->path && !filter_var($item->path, FILTER_VALIDATE_URL)) {
                                    $oldPath = storage_path('app/public/' . $item->path);
                                    if (file_exists($oldPath)) {
                                        unlink($oldPath);
                                    }
                                }
                                $item->path = $downloadedPath;
                                Log::info('PDF downloaded from search in update', [
                                    'url' => $itemData['pdf_url'],
                                    'path' => $downloadedPath
                                ]);
                            } else {
                                Log::warning('PDF download failed in update, skipping item', [
                                    'url' => $itemData['pdf_url']
                                ]);
                                continue;
                            }
                        } catch (\Exception $e) {
                            Log::error('PDF download error in update: ' . $e->getMessage());
                            continue;
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
                        $urlPath = $itemData['url'] ?? null;
                        
                        // Check if URL is a PDF and download it
                        if ($urlPath && str_ends_with(strtolower($urlPath), '.pdf')) {
                            try {
                                $downloadedPath = $this->downloadPDF($urlPath);
                                if ($downloadedPath) {
                                    // Switch to file type since we downloaded it
                                    $item->type = 'file';
                                    $item->path = $downloadedPath;
                                } else {
                                    // Keep as URL if download failed
                                    $item->path = $urlPath;
                                }
                            } catch (\Exception $e) {
                                Log::warning('PDF download failed: ' . $e->getMessage());
                                $item->path = $urlPath;
                            }
                        } else {
                            $item->path = $urlPath;
                        }
                    }

                    if ($item->path || $item->type === 'file') {
                        $item->save();
                        if (!in_array($item->item_id, $keptItemIds)) {
                            $keptItemIds[] = $item->item_id;
                        }
                    }
                }
            }

            // Delete items that were removed
            $removedItems = $material->items()->whereNotIn('item_id', $keptItemIds)->get();
            foreach ($removedItems as $removedItem) {
                // Delete associated file if exists
                if ($removedItem->path && !filter_var($removedItem->path, FILTER_VALIDATE_URL)) {
                    $oldPath = storage_path('app/public/' . $removedItem->path);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                $removedItem->delete();
            }

            DB::commit();

            return redirect()->route('materials.show', $material->material_id)->with('success', 'Material updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material update failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Failed to update material. Please try again.']);
        }
    }

    /**
     * Remove the specified material from storage.
     */
    public function destroy(Material $material)
    {
        // Check if the current user is a teacher and is the owner
        if (!auth()->user()->teacher || $material->teacher_id !== auth()->user()->teacher->id) {
            abort(403, 'You are not authorized to delete this material.');
        }
        
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
                            'content' => 'You are an expert in Tajweed (Quranic recitation rules). Analyze the material and categorize it into EXACTLY ONE category: "Madd Rules", "Idgham Billa Ghunnah", "Idgham Bi Ghunnah", or "Others". 

IMPORTANT Category Guidelines:

**"Idgham Bi Ghunnah"** - Select this if the content mentions:
- Ghunnah (غنة)
- Nasal sound / Nasalization
- Letters: ي (Ya), ن (Noon), م (Meem), و (Waw) 
- "With Ghunnah" / "Bi Ghunnah"
- Merging WITH nasal sound
- نون ساكنة followed by يَنْمُو letters

**"Idgham Billa Ghunnah"** - Select this if the content mentions:
- Letters: ل (Lam), ر (Ra)
- "Without Ghunnah" / "Billa Ghunnah" 
- Clear merging (no nasal sound)
- Merging WITHOUT nasal sound

**"Madd Rules"** - Select this if the content mentions:
- Elongation / Lengthening / Stretching
- Madd Tabi\'i, Madd Munfasil, Madd Muttasil, Madd Lazim, Madd Arid
- Prolongation
- Vowel extension
- Duration (2, 4, 6 counts)

**"Others"** - Select this if:
- Content is NOT about Tajweed rules
- General Islamic education, Quran translation, history, etc.
- Does NOT fit any of the above three Tajweed categories

Respond with ONLY the category name, nothing else. If Ghunnah or nasal sound is mentioned, it MUST be "Idgham Bi Ghunnah".'
                        ],
                        [
                            'role' => 'user',
                            'content' => $content
                        ]
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 50,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $category = trim($data['choices'][0]['message']['content'] ?? '');
                
                Log::info('OpenAI Category Response', [
                    'input' => $content,
                    'raw_response' => $category,
                    'full_data' => $data
                ]);
                
                // Validate category
                $validCategories = ['Madd Rules', 'Idgham Billa Ghunnah', 'Idgham Bi Ghunnah', 'Others'];
                if (in_array($category, $validCategories)) {
                    Log::info('Category validated successfully: ' . $category);
                    return $category;
                }
                
                Log::warning('OpenAI returned invalid category: ' . $category . ' | Valid options: ' . implode(', ', $validCategories));
            } else {
                Log::error('OpenAI API Error: ' . $response->status() . ' | Body: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Categorization Exception: ' . $e->getMessage());
        }

        // Default to Others if categorization fails (most generic option)
        return 'Others';
    }

    /**
     * Download PDF from URL and save to storage
     */
    private function downloadPDF(string $url): ?string
    {
        Log::info('downloadPDF called', ['url' => $url]);
        
        try {
            // Download PDF with proper headers and longer timeout
            Log::info('Sending HTTP request to download PDF');
            
            // Try with browser-like headers to avoid blocks
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/pdf,application/octet-stream,*/*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                    'Upgrade-Insecure-Requests' => '1',
                ])
                ->timeout(60) // Increased timeout to 60 seconds
                ->retry(3, 1000) // Retry 3 times with 1 second delay
                ->get($url);

            Log::info('HTTP response received', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'size' => strlen($response->body())
            ]);

            if (!$response->successful()) {
                Log::warning('PDF download HTTP request failed', [
                    'status' => $response->status(),
                    'url' => $url,
                    'reason' => $response->reason()
                ]);
                return null;
            }

            $content = $response->body();
            
            // Check if content is too small (likely error page)
            if (strlen($content) < 1024) {
                Log::warning('PDF content too small, likely an error page', [
                    'url' => $url,
                    'size' => strlen($content),
                    'content_preview' => substr($content, 0, 200)
                ]);
                return null;
            }
            
            // Validate it's actually a PDF
            $header = substr($content, 0, 4);
            Log::info('Checking PDF header', [
                'header' => $header,
                'is_pdf' => $header === '%PDF'
            ]);
            
            if ($header !== '%PDF') {
                Log::warning('URL does not contain valid PDF content', [
                    'url' => $url,
                    'header' => $header,
                    'first_100_chars' => substr($content, 0, 100)
                ]);
                return null;
            }

            // Create directory if it doesn't exist
            $destinationPath = storage_path('app/public/materials/pdfs');
            Log::info('Checking/creating destination directory', ['path' => $destinationPath]);
            
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
                Log::info('Created directory', ['path' => $destinationPath]);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.pdf';
            $fullPath = $destinationPath . '/' . $filename;
            $relativePath = 'materials/pdfs/' . $filename;

            Log::info('Saving PDF file', [
                'filename' => $filename,
                'full_path' => $fullPath,
                'relative_path' => $relativePath,
                'content_size' => strlen($content)
            ]);

            // Save file
            $bytesWritten = file_put_contents($fullPath, $content);
            
            if ($bytesWritten !== false) {
                Log::info('PDF file saved successfully', [
                    'bytes_written' => $bytesWritten,
                    'file_exists' => file_exists($fullPath),
                    'file_size' => filesize($fullPath),
                    'relative_path' => $relativePath
                ]);
                return $relativePath;
            } else {
                Log::error('Failed to write PDF file', ['full_path' => $fullPath]);
                return null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PDF download exception', [
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}

