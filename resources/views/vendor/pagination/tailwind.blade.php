@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display: flex; align-items: center; justify-content: center; margin: 30px 0;">
        <div style="display: inline-flex; gap: 10px; align-items: center;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="padding: 12px 20px; background: rgba(255,255,255,0.5); border: 2px solid #e0e0e0; border-radius: 12px; color: #999; font-weight: 600; font-family: 'Cairo', sans-serif; cursor: not-allowed;">
                    <i class="fas fa-chevron-left"></i> Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   style="padding: 12px 20px; background: white; border: 3px solid #0a5c36; border-radius: 12px; color: #0a5c36; font-weight: 700; font-family: 'Cairo', sans-serif; text-decoration: none; transition: all 0.3s ease;"
                   onmouseover="this.style.background='#0a5c36'; this.style.color='white'"
                   onmouseout="this.style.background='white'; this.style.color='#0a5c36'">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="padding: 12px 16px; color: #666; font-weight: 600; font-family: 'Cairo', sans-serif;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="padding: 12px 18px; background: linear-gradient(135deg, #0a5c36, #1abc9c); border: 3px solid #0a5c36; border-radius: 12px; color: white; font-weight: 700; font-family: 'Cairo', sans-serif;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" 
                               style="padding: 12px 18px; background: white; border: 2px solid #e0e0e0; border-radius: 12px; color: #333; font-weight: 600; font-family: 'Cairo', sans-serif; text-decoration: none; transition: all 0.3s ease;"
                               onmouseover="this.style.borderColor='#0a5c36'; this.style.color='#0a5c36'"
                               onmouseout="this.style.borderColor='#e0e0e0'; this.style.color='#333'">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   style="padding: 12px 20px; background: white; border: 3px solid #0a5c36; border-radius: 12px; color: #0a5c36; font-weight: 700; font-family: 'Cairo', sans-serif; text-decoration: none; transition: all 0.3s ease;"
                   onmouseover="this.style.background='#0a5c36'; this.style.color='white'"
                   onmouseout="this.style.background='white'; this.style.color='#0a5c36'">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span style="padding: 12px 20px; background: rgba(255,255,255,0.5); border: 2px solid #e0e0e0; border-radius: 12px; color: #999; font-weight: 600; font-family: 'Cairo', sans-serif; cursor: not-allowed;">
                    Next <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
