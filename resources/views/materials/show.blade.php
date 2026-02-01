@extends('layouts.template')

@section('page-title', $material->title)
@section('page-subtitle', 'Material Details')

@section('content')
    <div class="content-card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">📚 {{ $material->title }}</h3>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('materials.edit', $material->material_id) }}" 
                    style="padding: 8px 20px; background: var(--color-gold); color: var(--color-dark); border: none; border-radius: 20px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                    onmouseover="this.style.opacity='0.8'"
                    onmouseout="this.style.opacity='1'">
                    ✏️ Edit
                </a>
                <form action="{{ route('materials.destroy', $material->material_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this material?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        style="padding: 8px 20px; background: #e74c3c; color: white; border: none; border-radius: 20px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='#c0392b'"
                        onmouseout="this.style.background='#e74c3c'">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div style="background: rgba(77, 139, 49, 0.2); border: 2px solid var(--color-light-green); color: var(--color-light-green); padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="card-body">
            <!-- Thumbnail/Preview -->
            @if($material->thumbnail || $material->file_path || $material->video_link)
                <div style="margin-bottom: 30px; text-align: center;">
                    @if($material->thumbnail)
                        @if(filter_var($material->thumbnail, FILTER_VALIDATE_URL))
                            <img src="{{ $material->thumbnail }}" alt="{{ $material->title }}" 
                                style="max-width: 100%; max-height: 400px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);"
                                onerror="this.style.display='none'">
                        @else
                            <img src="{{ Storage::url($material->thumbnail) }}" alt="{{ $material->title }}" 
                                style="max-width: 100%; max-height: 400px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);"
                                onerror="this.style.display='none'">
                        @endif
                    @elseif($material->video_link && strpos($material->video_link, 'youtube.com') !== false)
                        @php
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $material->video_link, $matches);
                            $videoId = $matches[1] ?? null;
                        @endphp
                        @if($videoId)
                            <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg" alt="{{ $material->title }}" 
                                style="max-width: 100%; max-height: 400px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        @endif
                    @endif
                </div>
            @endif

            <!-- Visibility -->
            <div style="display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap;">
                @if($material->is_public)
                    <span style="display: inline-block; background: rgba(77, 139, 49, 0.2); color: var(--color-light-green); padding: 8px 18px; border-radius: 20px; font-weight: 600;">
                        🌍 Public
                    </span>
                @else
                    <span style="display: inline-block; background: rgba(31, 39, 27, 0.5); color: var(--color-light); padding: 8px 18px; border-radius: 20px; font-weight: 600;">
                        🔒 Private
                    </span>
                @endif
            </div>

            <!-- Description -->
            @if($material->description)
                <div style="margin-bottom: 30px;">
                    <h4 style="color: var(--color-gold); margin: 0 0 15px 0;">📋 Description</h4>
                    <div style="background: rgba(31, 39, 27, 0.5); padding: 20px; border-radius: 12px; border: 2px solid var(--color-dark-green);">
                        <p style="color: var(--color-light); margin: 0; line-height: 1.8; white-space: pre-wrap;">{{ $material->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Resources -->
            <div style="margin-bottom: 30px;">
                <h4 style="color: var(--color-gold); margin: 0 0 15px 0;">📁 Available Resources</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    @if($material->file_path)
                        @php
                            $fileName = basename($material->file_path);
                            // Remove timestamp prefix if exists
                            $fileName = preg_replace('/^\d+_[a-f0-9]+\./', '', $fileName);
                        @endphp
                        <a href="{{ Storage::url($material->file_path) }}" target="_blank"
                            style="display: flex; align-items: center; gap: 15px; padding: 20px; background: rgba(227, 216, 136, 0.1); border: 2px solid var(--color-gold); border-radius: 12px; text-decoration: none; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                            onmouseover="this.style.background='rgba(227, 216, 136, 0.2)'; this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.background='rgba(227, 216, 136, 0.1)'; this.style.transform='translateY(0)'">
                            <div style="font-size: 2.5rem;">📄</div>
                            <div style="flex: 1;">
                                <div style="color: var(--color-gold); font-weight: 600; font-size: 1rem; font-family: 'Cairo', sans-serif; word-break: break-word;">{{ $fileName }}</div>
                                <div style="color: var(--color-gold); font-size: 0.9rem; font-family: 'Cairo', sans-serif; margin-top: 4px; opacity: 0.8;">Click to download</div>
                            </div>
                        </a>
                    @endif

                    @if($material->video_link)
                        <a href="{{ $material->video_link }}" target="_blank"
                            style="display: flex; align-items: center; gap: 15px; padding: 20px; background: rgba(77, 139, 49, 0.1); border: 2px solid var(--color-light-green); border-radius: 12px; text-decoration: none; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                            onmouseover="this.style.background='rgba(77, 139, 49, 0.2)'; this.style.transform='translateY(-3px)'"
                            onmouseout="this.style.background='rgba(77, 139, 49, 0.1)'; this.style.transform='translateY(0)'">
                            <div style="font-size: 2.5rem;">🎥</div>
                            <div style="flex: 1;">
                                <div style="color: var(--color-light-green); font-weight: 600; font-size: 1rem; font-family: 'Cairo', sans-serif; word-break: break-word;">{{ $material->title }}</div>
                                <div style="color: var(--color-light-green); font-size: 0.9rem; font-family: 'Cairo', sans-serif; margin-top: 4px; opacity: 0.8;">Click to watch</div>
                            </div>
                        </a>
                    @endif
                </div>

                @if(!$material->file_path && !$material->video_link)
                    <div style="text-align: center; padding: 40px; background: rgba(31, 39, 27, 0.3); border-radius: 12px; border: 2px dashed var(--color-dark-green);">
                        <div style="font-size: 3rem; opacity: 0.5; margin-bottom: 10px;">📭</div>
                        <p style="color: var(--color-light); opacity: 0.7; margin: 0;">No resources attached to this material.</p>
                    </div>
                @endif
            </div>

            <!-- Metadata -->
            <div style="padding: 20px; background: rgba(31, 39, 27, 0.3); border-radius: 12px; border: 2px solid var(--color-dark-green); margin-bottom: 30px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Created</label>
                        <p style="color: var(--color-light); font-size: 1rem; margin: 0; font-weight: 600;">{{ $material->created_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Last Updated</label>
                        <p style="color: var(--color-light); font-size: 1rem; margin: 0; font-weight: 600;">{{ $material->updated_at->format('F d, Y') }}</p>
                    </div>
                    <div>
                        <label style="display: block; color: var(--color-gold); font-size: 0.9rem; margin-bottom: 5px; opacity: 0.8;">Used in Assignments</label>
                        <p style="color: var(--color-light); font-size: 1rem; margin: 0; font-weight: 600;">{{ $material->assignments->count() }} assignment(s)</p>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div>
                <a href="{{ route('materials.index') }}" 
                    style="display: inline-block; padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;"
                    onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                    onmouseout="this.style.background='transparent'">
                    ← Back to Materials
                </a>
            </div>
        </div>
    </div>
@endsection
