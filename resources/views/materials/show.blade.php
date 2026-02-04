@php
    $currentUser = Auth::user();
    $isStudent = $currentUser->role_id == 2;
@endphp

@extends('layouts.dashboard')

@section('title', $material->title)
@section('user-role', ($isStudent ? 'Student' : 'Teacher') . ' • Material Details')

@section('navigation')
    @if($isStudent)
        @include('partials.student-nav')
    @else
        @include('partials.teacher-nav')
    @endif
@endsection

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 3px solid #4caf50; color: #2e7d32; padding: 15px 20px; border-radius: 15px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="{{ route('materials.index') }}" 
            style="display: inline-flex; align-items: center; gap: 10px; color: #1a1a1a; text-decoration: none; font-family: 'El Messiri', sans-serif; font-weight: 600; font-size: 1rem; padding: 8px 16px; border-radius: 50px; background: rgba(255, 255, 255, 0.9); border: 3px solid #2a2a2a; transition: all 0.3s ease;"
            onmouseover="this.style.background='rgba(10, 92, 54, 0.1)'; this.style.transform='translateX(-5px)'; this.style.borderColor='#0a5c36'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='translateX(0)'; this.style.borderColor='#2a2a2a'">
            <i class="fas fa-arrow-left"></i> Back to Materials
        </a>
    </div>

    <!-- Material Header -->
    <div style="background: white; border-radius: 25px; padding: 30px; margin-bottom: 30px; border: 3px solid #2a2a2a; box-shadow: 0 10px 30px rgba(10, 92, 54, 0.1);">
        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 20px;">
            <div style="flex: 1; min-width: 300px;">
                <h1 style="margin: 0 0 15px 0; font-family: 'El Messiri', serif; font-size: 2.5rem; color: #0a5c36; font-weight: 700; line-height: 1.2;">
                    {{ $material->title }}
                </h1>
                <p style="margin: 0 0 20px 0; font-size: 1.1rem; color: #666; font-family: 'Cairo', sans-serif; line-height: 1.6;">
                    {{ $material->description ?? 'No description available' }}
                </p>
                
                <!-- Meta Info -->
                <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                    <span style="padding: 6px 15px; background: rgba(10, 92, 54, 0.1); color: #0a5c36; border-radius: 12px; font-size: 0.95rem; font-weight: 600; font-family: 'Cairo', sans-serif;">
                        <i class="fas fa-calendar"></i> {{ $material->created_at->format('M d, Y') }}
                    </span>
                    @if($material->is_public)
                        <span style="padding: 6px 15px; background: rgba(26, 188, 156, 0.15); color: #1abc9c; border-radius: 12px; font-size: 0.95rem; font-weight: 600; font-family: 'Cairo', sans-serif;">
                            <i class="fas fa-globe"></i> Public
                        </span>
                    @endif
                    <span style="padding: 6px 15px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 12px; font-size: 0.95rem; font-weight: 600; font-family: 'Cairo', sans-serif;">
                        @if($material->video_link)
                            <i class="fas fa-video"></i> Video
                        @elseif($material->file_path && Str::endsWith($material->file_path, '.pdf'))
                            <i class="fas fa-file-pdf"></i> PDF
                        @elseif($material->file_path && (Str::endsWith($material->file_path, '.mp3') || Str::endsWith($material->file_path, '.wav')))
                            <i class="fas fa-volume-up"></i> Audio
                        @else
                            <i class="fas fa-file"></i> Document
                        @endif
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            @if(!$isStudent)
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('materials.edit', $material->material_id) }}" 
                       style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: rgba(212, 175, 55, 0.15); color: #d4af37; border-radius: 15px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease; border: 3px solid #d4af37;"
                       onmouseover="this.style.background='rgba(212, 175, 55, 0.25)'"
                       onmouseout="this.style.background='rgba(212, 175, 55, 0.15)'">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('materials.destroy', $material->material_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this material? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                style="padding: 12px 20px; background: rgba(231, 76, 60, 0.15); color: #e74c3c; border: 3px solid #e74c3c; border-radius: 15px; font-weight: 600; font-family: 'Cairo', sans-serif; cursor: pointer; transition: all 0.3s ease;"
                                onmouseover="this.style.background='rgba(231, 76, 60, 0.25)'"
                                onmouseout="this.style.background='rgba(231, 76, 60, 0.15)'">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
        <!-- Main Content -->
        <div>
            <!-- Video Player -->
            @if($material->video_link)
                <div style="background: white; border-radius: 20px; overflow: hidden; margin-bottom: 25px; border: 3px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
                    <div style="padding: 20px; border-bottom: 3px solid #f5f5f5;">
                        <h3 style="margin: 0; font-size: 1.3rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                            <i class="fas fa-video"></i> Video Lecture
                        </h3>
                    </div>
                    <div style="position: relative; padding-top: 56.25%; background: #000;">
                        @php
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_link, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                        @else
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                                <p>Invalid video link</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- File Download -->
            @if($material->file_path)
                <div style="background: white; border-radius: 20px; padding: 25px; border: 3px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
                    <h3 style="margin: 0 0 15px 0; font-size: 1.3rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                        <i class="fas fa-file-download"></i> Downloadable File
                    </h3>
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: rgba(10, 92, 54, 0.05); border-radius: 15px; border: 2px solid rgba(10, 92, 54, 0.2);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #0a5c36, #1abc9c); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                @if(Str::endsWith($material->file_path, '.pdf'))
                                    <i class="fas fa-file-pdf"></i>
                                @elseif(Str::endsWith($material->file_path, ['.mp3', '.wav']))
                                    <i class="fas fa-volume-up"></i>
                                @elseif(Str::endsWith($material->file_path, ['.mp4', '.avi']))
                                    <i class="fas fa-film"></i>
                                @else
                                    <i class="fas fa-file"></i>
                                @endif
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; font-weight: 700; color: #1a1a1a; font-family: 'Cairo', sans-serif; font-size: 1.1rem;">
                                    {{ basename($material->file_path) }}
                                </p>
                                <p style="margin: 0; font-size: 0.9rem; color: #666; font-family: 'Cairo', sans-serif;">
                                    Click to download
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $material->file_path) }}" 
                           download
                           style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: linear-gradient(135deg, #0a5c36, #1abc9c); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; font-family: 'Cairo', sans-serif; transition: all 0.3s ease;"
                           onmouseover="this.style.transform='scale(1.05)'"
                           onmouseout="this.style.transform='scale(1)'">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Thumbnail -->
            @if($material->thumbnail)
                <div style="background: white; border-radius: 20px; overflow: hidden; margin-bottom: 25px; border: 3px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
                    <img src="{{ asset('storage/' . $material->thumbnail) }}" 
                         alt="{{ $material->title }}"
                         style="width: 100%; display: block;"
                         onerror="this.parentElement.style.display='none'">
                </div>
            @endif

            <!-- Info Card -->
            <div style="background: white; border-radius: 20px; padding: 25px; border: 3px solid #e0e0e0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
                <h3 style="margin: 0 0 20px 0; font-size: 1.3rem; color: #0a5c36; font-weight: 700; font-family: 'El Messiri', serif;">
                    <i class="fas fa-info-circle"></i> Material Information
                </h3>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div style="padding: 15px; background: rgba(10, 92, 54, 0.05); border-radius: 12px;">
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Cairo', sans-serif;">
                            Created
                        </p>
                        <p style="margin: 0; font-size: 1rem; color: #1a1a1a; font-weight: 600; font-family: 'Cairo', sans-serif;">
                            {{ $material->created_at->format('F d, Y') }}
                        </p>
                    </div>
                    <div style="padding: 15px; background: rgba(10, 92, 54, 0.05); border-radius: 12px;">
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Cairo', sans-serif;">
                            Last Updated
                        </p>
                        <p style="margin: 0; font-size: 1rem; color: #1a1a1a; font-weight: 600; font-family: 'Cairo', sans-serif;">
                            {{ $material->updated_at->format('F d, Y') }}
                        </p>
                    </div>
                    <div style="padding: 15px; background: rgba(10, 92, 54, 0.05); border-radius: 12px;">
                        <p style="margin: 0 0 5px 0; font-size: 0.85rem; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; font-family: 'Cairo', sans-serif;">
                            Visibility
                        </p>
                        <p style="margin: 0; font-size: 1rem; color: #1a1a1a; font-weight: 600; font-family: 'Cairo', sans-serif;">
                            @if($material->is_public)
                                <i class="fas fa-globe" style="color: #1abc9c;"></i> Public
                            @else
                                <i class="fas fa-lock" style="color: #e74c3c;"></i> Private
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
