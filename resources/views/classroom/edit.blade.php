@extends('layouts.template')

@section('page-title', 'Edit Classroom')
@section('page-subtitle', 'Update classroom information')

@section('content')
    <!-- Success Message -->
    @if(session('success'))
        <div style="background: rgba(46, 125, 50, 0.2); border: 2px solid #4caf50; color: #a5d6a7; padding: 15px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 1.5rem;">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="content-card">
        <div class="card-header">
            <h3 class="card-title">✏️ Edit Classroom</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('classroom.update', $classroom->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Class Name -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gold);">
                        Class Name <span style="color: #e74c3c;">*</span>
                    </label>
                    <input type="text" name="class_name" value="{{ old('class_name', $classroom->class_name) }}" required
                        style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); border: 2px solid var(--color-dark-green); border-radius: 8px; color: var(--color-light-green); font-size: 1rem; font-family: 'Cairo', sans-serif;"
                        placeholder="e.g., Tajweed Level 1">
                    @error('class_name')
                        <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--color-gold);">
                        Description
                    </label>
                    <textarea name="description" rows="5"
                        style="width: 100%; padding: 12px 15px; background: rgba(31, 39, 27, 0.5); border: 2px solid var(--color-dark-green); border-radius: 8px; color: var(--color-light-green); font-size: 1rem; font-family: 'Cairo', sans-serif; resize: vertical;"
                        placeholder="Describe the classroom objectives and content...">{{ old('description', $classroom->description) }}</textarea>
                    @error('description')
                        <span style="color: #e74c3c; font-size: 0.9rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Access Code Display -->
                <div style="background: rgba(227, 216, 136, 0.1); border: 2px solid var(--color-gold); border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; align-items: center; gap: 15px; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 15px; flex: 1;">
                            <div style="font-size: 2rem;">🔑</div>
                            <div style="flex: 1;">
                                <h4 style="color: var(--color-gold); margin: 0 0 8px 0; font-family: 'Amiri', serif;">Access Code</h4>
                                <p style="margin: 0 0 10px 0; opacity: 0.9;">Current access code for this classroom:</p>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="background: rgba(31, 39, 27, 0.8); padding: 15px; border-radius: 8px; display: inline-block;">
                                        <span id="accessCodeDisplay" style="font-family: 'JetBrains Mono', monospace; font-size: 2rem; font-weight: 700; color: var(--color-gold); letter-spacing: 5px;">
                                            ••••••
                                        </span>
                                        <span id="accessCodeHidden" style="display: none;">{{ $classroom->access_code }}</span>
                                    </div>
                                    <button type="button" onclick="toggleAccessCodeEdit(this)" 
                                        style="padding: 10px 15px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-weight: 600;"
                                        onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                                        <span id="toggleIconEdit">👁️</span> <span id="toggleTextEdit">Show</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" onclick="confirmRegenerate()" 
                            style="padding: 12px 25px; background: transparent; color: #ff9800; border: 2px solid #ff9800; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif; font-weight: 600; white-space: nowrap;"
                            onmouseover="this.style.background='rgba(255, 152, 0, 0.1)'"
                            onmouseout="this.style.background='transparent'">
                            🔄 Regenerate
                        </button>
                    </div>
                </div>

                <script>
                    function toggleAccessCodeEdit(btn) {
                        const codeElement = document.getElementById('accessCodeDisplay');
                        const hiddenCode = document.getElementById('accessCodeHidden').textContent.trim();
                        const toggleIcon = document.getElementById('toggleIconEdit');
                        const toggleText = document.getElementById('toggleTextEdit');
                        
                        if (codeElement.textContent.includes('•')) {
                            codeElement.textContent = hiddenCode;
                            toggleIcon.textContent = '🙈';
                            toggleText.textContent = 'Hide';
                        } else {
                            codeElement.textContent = '••••••';
                            toggleIcon.textContent = '👁️';
                            toggleText.textContent = 'Show';
                        }
                    }
                    
                    function confirmRegenerate() {
                        if (confirm('⚠️ Warning: Generating a new access code will invalidate the current code. Students with the old code will no longer be able to join. Continue?')) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("classroom.regenerate", $classroom->id) }}';
                            
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            
                            const methodField = document.createElement('input');
                            methodField.type = 'hidden';
                            methodField.name = '_method';
                            methodField.value = 'PATCH';
                            
                            form.appendChild(csrfToken);
                            form.appendChild(methodField);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    }
                </script>

                <!-- Buttons -->
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('classroom.index') }}" 
                        style="padding: 12px 30px; background: transparent; color: var(--color-light-green); border: 2px solid var(--color-light-green); border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-block;"
                        onmouseover="this.style.background='rgba(226, 241, 175, 0.1)'"
                        onmouseout="this.style.background='transparent'">
                        Cancel
                    </a>
                    <button type="submit"
                        style="padding: 12px 30px; background: var(--color-dark-green); color: var(--color-gold); border: none; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-family: 'Cairo', sans-serif;"
                        onmouseover="this.style.background='var(--color-gold)'; this.style.color='var(--color-dark)'"
                        onmouseout="this.style.background='var(--color-dark-green)'; this.style.color='var(--color-gold)'">
                        Update Classroom
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
