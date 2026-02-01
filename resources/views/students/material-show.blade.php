@extends('layouts.template')

@section('title', $material->title)
@section('page-title', $material->title)
@section('page-subtitle', 'Material Details')

@section('content')
<div style="padding: 0;">
    <div style="margin-bottom: 20px;">
        @if(isset($from) && $from === 'classroom' && isset($classId))
            <a href="{{ route('classroom.show', $classId) }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
                ← Back to Classroom
            </a>
        @elseif(isset($from) && $from === 'assignment' && isset($assignmentId))
            <a href="{{ route('student.assignment.submit', $assignmentId) }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
                ← Back to Assignment
            </a>
        @else
            <a href="{{ route('student.materials') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-gold); text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.color='var(--color-light-green)'" onmouseout="this.style.color='var(--color-gold)'">
                ← Back to Materials
            </a>
        @endif
    </div>

    <div style="background: rgba(31, 39, 27, 0.6); backdrop-filter: blur(10px); border: 2px solid rgba(77, 139, 49, 0.3); border-radius: 15px; padding: 30px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(77, 139, 49, 0.3);">
            <div style="width: 60px; height: 60px; background: var(--color-dark-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 4px 15px rgba(77, 139, 49, 0.4);">
                📚
            </div>
            <div style="flex: 1;">
                <h2 style="color: var(--color-gold); font-size: 1.8rem; margin-bottom: 8px;">{{ $material->title }}</h2>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    @if($material->category)
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(77, 139, 49, 0.2); color: var(--color-light-green); padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; border: 1px solid rgba(77, 139, 49, 0.4);">
                            🏷️ {{ $material->category }}
                        </span>
                    @endif
                    @if($material->is_public)
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(77, 139, 49, 0.2); color: var(--color-gold); padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; border: 1px solid rgba(77, 139, 49, 0.4);">
                            🌍 Public
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(70, 63, 58, 0.4); color: var(--color-gold); padding: 6px 14px; border-radius: 8px; font-size: 0.85rem; border: 1px solid rgba(70, 63, 58, 0.6);">
                            🔒 Private
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($material->thumbnail_path || $material->video_link)
            <div style="margin-bottom: 30px; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);">
                @if($material->video_link)
                    @php
                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_link, $matches);
                        $videoId = $matches[1] ?? null;
                    @endphp
                    @if($videoId)
                        <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                            <iframe 
                                src="https://www.youtube.com/embed/{{ $videoId }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                            </iframe>
                        </div>
                    @else
                        <a href="{{ $material->video_link }}" target="_blank" style="display: block; padding: 40px; background: rgba(77, 139, 49, 0.2); text-align: center; text-decoration: none; transition: all 0.3s ease; border: 2px solid rgba(77, 139, 49, 0.4);" onmouseover="this.style.background='rgba(77, 139, 49, 0.3)'" onmouseout="this.style.background='rgba(77, 139, 49, 0.2)'">
                            <div style="font-size: 3rem; margin-bottom: 15px;">🎥</div>
                            <div style="color: var(--color-gold); font-size: 1.1rem; font-weight: 600;">Watch Video</div>
                            <div style="color: var(--color-light-green); font-size: 0.9rem; margin-top: 5px; opacity: 0.8;">Click to open video</div>
                        </a>
                    @endif
                @elseif($material->thumbnail_path)
                    @if(str_starts_with($material->thumbnail_path, 'http'))
                        <img src="{{ $material->thumbnail_path }}" alt="{{ $material->title }}" style="width: 100%; height: auto; display: block;">
                    @else
                        <img src="{{ asset('storage/' . $material->thumbnail_path) }}" alt="{{ $material->title }}" style="width: 100%; height: auto; display: block;">
                    @endif
                @endif
            </div>
        @endif

        @if($material->description)
            <div style="margin-bottom: 30px;">
                <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <span>📋</span> Description
                </h3>
                <div style="background: rgba(70, 63, 58, 0.4); padding: 25px; border-radius: 12px; border: 2px solid rgba(77, 139, 49, 0.2);">
                    <p style="color: var(--color-light-green); margin: 0; line-height: 1.8; white-space: pre-wrap; font-size: 1rem;">{{ $material->description }}</p>
                </div>
            </div>
        @endif

        @if($material->file_path || $material->video_link)
            <div style="margin-bottom: 20px;">
                <h3 style="color: var(--color-gold); font-size: 1.3rem; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <span>📁</span> Available Resources
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                    @if($material->file_path)
                        <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" download
                            style="display: flex; align-items: center; gap: 15px; padding: 20px; background: rgba(227, 216, 136, 0.1); border: 2px solid var(--color-gold); border-radius: 12px; text-decoration: none; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(227, 216, 136, 0.2)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(227, 216, 136, 0.3)'"
                            onmouseout="this.style.background='rgba(227, 216, 136, 0.1)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <div style="font-size: 2.5rem;">📄</div>
                            <div style="flex: 1;">
                                <div style="color: var(--color-gold); font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;">PDF Document</div>
                                <div style="color: var(--color-light-green); font-size: 0.85rem; opacity: 0.8;">Click to download or view</div>
                            </div>
                            <div style="color: var(--color-gold); font-size: 1.5rem;">→</div>
                        </a>
                    @endif

                    @if($material->video_link)
                        <a href="{{ $material->video_link }}" target="_blank"
                            style="display: flex; align-items: center; gap: 15px; padding: 20px; background: rgba(227, 216, 136, 0.1); border: 2px solid var(--color-gold); border-radius: 12px; text-decoration: none; transition: all 0.3s ease;"
                            onmouseover="this.style.background='rgba(227, 216, 136, 0.2)'; this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(227, 216, 136, 0.3)'"
                            onmouseout="this.style.background='rgba(227, 216, 136, 0.1)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <div style="font-size: 2.5rem;">🎥</div>
                            <div style="flex: 1;">
                                <div style="color: var(--color-gold); font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;">Video Link</div>
                                <div style="color: var(--color-light-green); font-size: 0.85rem; opacity: 0.8;">Click to watch video</div>
                            </div>
                            <div style="color: var(--color-gold); font-size: 1.5rem;">→</div>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <div style="margin-top: 30px; padding: 20px; background: rgba(227, 216, 136, 0.1); border: 2px solid rgba(227, 216, 136, 0.3); border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="font-size: 1.3rem;">💡</span>
                <h4 style="color: var(--color-gold); font-size: 1rem; font-weight: 600;">Study Tips</h4>
            </div>
            <ul style="color: var(--color-light-green); opacity: 0.9; font-size: 0.9rem; margin-left: 30px; line-height: 1.8; list-style: disc;">
                <li>Review the material carefully before starting assignments</li>
                <li>Take notes while watching videos or reading documents</li>
                <li>Practice regularly to improve your Tajweed skills</li>
                <li>Ask your teacher if you have any questions</li>
            </ul>
        </div>
    </div>
</div>
@endsection
