@extends('layouts.dashboard')

@section('title', 'My Classes')
@section('user-role', 'Student • My Classes')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
@php
    $enrolledClasses = $student->classrooms;
    $totalAssignments = $enrolledClasses->flatMap->assignments->count();
    $totalStudents = $enrolledClasses->sum(function ($c) { return $c->students->count(); });
@endphp

<style>
    /* ============ HERO ============ */
    .st-hero {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border: 3px solid #0a4a2b;
        border-radius: 20px;
        color: #fff;
        padding: 28px 34px;
        margin-bottom: 26px;
        box-shadow: 0 14px 32px rgba(10, 92, 54, 0.24);
        position: relative;
        overflow: hidden;
    }

    .st-hero:before {
        content: '';
        position: absolute;
        top: -100px;
        right: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .st-hero h1 {
        margin: 0 0 4px;
        font-size: 2.1rem;
        line-height: 1.2;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }

    .st-hero p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.05rem;
        position: relative;
        z-index: 2;
    }

    /* ============ STATS STRIP ============ */
    .st-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .st-stat {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
        animation: stFadeUp 0.5s ease both;
    }

    .st-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 26px rgba(10, 92, 54, 0.12);
    }

    .st-stat:nth-child(2) { animation-delay: 0.06s; }
    .st-stat:nth-child(3) { animation-delay: 0.12s; }

    .st-stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #fff;
        flex-shrink: 0;
    }

    .st-stat-icon.blue { background: linear-gradient(135deg, #3498db, #2980b9); }
    .st-stat-icon.gold { background: linear-gradient(135deg, #d4af37, #b8860b); }
    .st-stat-icon.green { background: linear-gradient(135deg, #2ecc71, #27ae60); }

    .st-stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0a5c36;
        line-height: 1;
    }

    .st-stat-label {
        color: #666;
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 4px;
    }

    /* ============ LAYOUT ============ */
    .st-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
        gap: 26px;
        align-items: start;
    }

    /* ============ CLASSES COLUMN ============ */
    .st-toolbar {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .st-search {
        position: relative;
        flex: 1 1 200px;
        min-width: 180px;
    }

    .st-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0a5c36;
        font-size: 0.9rem;
    }

    .st-search input {
        width: 100%;
        padding: 11px 16px 11px 38px;
        border: 2px solid #cfd9d0;
        border-radius: 50px;
        font-size: 0.98rem;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        color: #1f2937;
        transition: all 0.2s ease;
    }

    .st-search input:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    .st-sort {
        position: relative;
        min-width: 170px;
    }

    .st-sort i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0a5c36;
        font-size: 0.9rem;
        pointer-events: none;
    }

    .st-sort select {
        width: 100%;
        padding: 11px 30px 11px 38px;
        border: 2px solid #cfd9d0;
        border-radius: 50px;
        font-size: 0.98rem;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        color: #1f2937;
        cursor: pointer;
        background: #fff;
        appearance: none;
        -webkit-appearance: none;
        transition: all 0.2s ease;
    }

    .st-sort select:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    .st-count { font-size: 0.95rem; color: #5f6f65; font-weight: 700; white-space: nowrap; }
    .st-count span { color: #0a5c36; }

    .st-list {
        display: grid;
        gap: 18px;
    }

    .st-card {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        animation: stFadeUp 0.45s ease both;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    .st-card:nth-child(2n) { animation-delay: 0.05s; }
    .st-card:nth-child(3n) { animation-delay: 0.1s; }

    .st-card:hover {
        transform: translateX(4px) translateY(-2px);
        box-shadow: 0 16px 34px rgba(10, 92, 54, 0.14);
    }

    .st-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
        border-radius: 18px 0 0 18px;
    }

    .st-card-main {
        flex: 1;
        min-width: 240px;
    }

    .st-card-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 6px;
        font-family: 'El Messiri', serif;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .st-card-title i { color: #d4af37; }

    .st-card-desc {
        color: #5f6f65;
        font-size: 0.93rem;
        line-height: 1.55;
        margin: 0 0 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .st-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 18px;
    }

    .st-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0a5c36;
        background: #f0faf5;
        border: 1px solid #bfe9d6;
        padding: 5px 12px;
        border-radius: 50px;
    }

    .st-chip i { color: #1abc9c; }

    .st-card-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
        min-width: 150px;
    }

    .st-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 20px;
        border-radius: 11px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
        width: 100%;
    }

    .st-btn.view {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.22);
    }

    .st-btn.view:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(10, 92, 54, 0.3); }

    .st-btn.leave {
        background: #fff;
        color: #e74c3c;
        border-color: #e74c3c;
    }

    .st-btn.leave:hover { background: #e74c3c; color: #fff; }

    /* Empty state */
    .st-empty {
        background: #fff;
        border: 2px dashed #b7c6ba;
        border-radius: 18px;
        padding: 60px 30px;
        text-align: center;
        color: #5f6f65;
        animation: stFadeUp 0.45s ease both;
    }

    .st-empty i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 14px;
        display: block;
        color: #0a5c36;
    }

    .st-empty h3 {
        color: #0a5c36;
        font-size: 1.4rem;
        margin: 0 0 6px;
        font-family: 'El Messiri', serif;
    }

    .st-empty p { margin: 0; font-size: 1.02rem; }

    /* ============ ENROLL COLUMN ============ */
    .st-enroll-wrap {
        position: sticky;
        top: 20px;
        display: grid;
        gap: 18px;
    }

    .st-enroll {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 24px;
        animation: stFadeUp 0.5s ease both;
    }

    .st-enroll-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        padding-bottom: 14px;
        border-bottom: 3px solid #0a5c36;
    }

    .st-enroll-header .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .st-enroll-header h3 {
        margin: 0;
        color: #0a5c36;
        font-family: 'El Messiri', serif;
        font-size: 1.3rem;
        line-height: 1.2;
    }

    .st-enroll-header p {
        margin: 2px 0 0;
        color: #5f6f65;
        font-size: 0.9rem;
    }

    .st-field-label {
        display: block;
        font-size: 0.92rem;
        font-weight: 800;
        color: #0a5c36;
        margin-bottom: 8px;
    }

    .st-code-input {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #cfd9d0;
        border-radius: 12px;
        font-family: 'Courier New', monospace;
        font-size: 1.4rem;
        font-weight: 800;
        letter-spacing: 6px;
        text-align: center;
        text-transform: uppercase;
        color: #0a5c36;
        background: #fafcf8;
        transition: all 0.2s ease;
    }

    .st-code-input:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
        background: #fff;
    }

    .st-enroll-btn {
        width: 100%;
        margin-top: 14px;
        padding: 14px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        font-weight: 800;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Cairo', sans-serif;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.25);
    }

    .st-enroll-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(10, 92, 54, 0.32);
    }

    .st-enroll-hint {
        margin-top: 14px;
        padding: 12px;
        background: #f8f1d5;
        border: 1px solid #e5d699;
        border-radius: 12px;
        font-size: 0.88rem;
        color: #7a6110;
        line-height: 1.6;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .st-enroll-hint i { color: #b8860b; margin-top: 3px; }

    /* How to enroll */
    .st-howto {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 20px 24px;
        animation: stFadeUp 0.5s ease both;
        animation-delay: 0.08s;
    }

    .st-howto h4 {
        margin: 0 0 12px;
        color: #0a5c36;
        font-family: 'El Messiri', serif;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .st-howto ol {
        margin: 0;
        padding-left: 20px;
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 2;
        font-weight: 600;
    }

    .st-hidden { display: none !important; }

    @keyframes stFadeUp {
        from { opacity: 0; transform: translateY(22px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1100px) {
        .st-layout { grid-template-columns: 1fr; }
        .st-enroll-wrap { position: static; }
    }

    @media (max-width: 720px) {
        .st-hero { padding: 22px; }
        .st-card-actions { width: 100%; flex-direction: row; }
        .st-card-actions .st-btn { flex: 1; }
    }
</style>

<!-- ============ HERO ============ -->
<div class="st-hero">
    <h1><i class="fas fa-school"></i> My Classes</h1>
    <p>Join and manage the classes where you learn Tajweed</p>
</div>

<!-- ============ STATS STRIP ============ -->
<div class="st-stats">
    <div class="st-stat">
        <div class="st-stat-icon blue"><i class="fas fa-chalkboard"></i></div>
        <div>
            <div class="st-stat-value">{{ $enrolledClasses->count() }}</div>
            <div class="st-stat-label">Enrolled Classes</div>
        </div>
    </div>
    <div class="st-stat">
        <div class="st-stat-icon gold"><i class="fas fa-tasks"></i></div>
        <div>
            <div class="st-stat-value">{{ $totalAssignments }}</div>
            <div class="st-stat-label">Assignments</div>
        </div>
    </div>
    <div class="st-stat">
        <div class="st-stat-icon green"><i class="fas fa-users"></i></div>
        <div>
            <div class="st-stat-value">{{ $totalStudents }}</div>
            <div class="st-stat-label">Classmates</div>
        </div>
    </div>
</div>

<div class="st-layout">
    <!-- ============ CLASSES COLUMN ============ -->
    <div>
        <div class="st-toolbar">
            <div class="st-search">
                <i class="fas fa-search"></i>
                <input type="text" id="stSearch" placeholder="Search classes..." onkeyup="filterClasses()">
            </div>
            <div class="st-sort">
                <i class="fas fa-filter"></i>
                <select id="stSort" onchange="sortClasses()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="students">Most Students</option>
                </select>
            </div>
            <div class="st-count"><span id="stCount">{{ $enrolledClasses->count() }}</span> class{{ $enrolledClasses->count() != 1 ? 'es' : '' }}</div>
        </div>

        @if($enrolledClasses->isEmpty())
            <div class="st-empty">
                <i class="fas fa-book-open"></i>
                <h3>No Classes Yet</h3>
                <p>Use the access code from your teacher to enroll in your first class</p>
            </div>
        @else
            <div class="st-list" id="stList">
                @foreach($enrolledClasses as $classroom)
                    <div class="st-card"
                         data-name="{{ strtolower($classroom->class_name) }}"
                         data-teacher="{{ strtolower($classroom->teacher->name ?? '') }}"
                         data-joined="{{ $classroom->pivot->created_at ?? now() }}"
                         data-students="{{ $classroom->students->count() }}">
                        <div class="st-card-main">
                            <h3 class="st-card-title">
                                <i class="fas fa-book-quran"></i>
                                {{ $classroom->class_name }}
                            </h3>
                            <p class="st-card-desc">{{ $classroom->description ?: 'No description available' }}</p>
                            <div class="st-card-meta">
                                <span class="st-chip"><i class="fas fa-user-tie"></i> {{ $classroom->teacher->name ?? 'Teacher' }}</span>
                                <span class="st-chip"><i class="fas fa-users"></i> {{ $classroom->students->count() }} Students</span>
                                <span class="st-chip"><i class="fas fa-tasks"></i> {{ $classroom->assignments->count() }} Tasks</span>
                            </div>
                        </div>
                        <div class="st-card-actions">
                            <a href="{{ route('classroom.show', $classroom->id) }}" class="st-btn view">
                                <i class="fas fa-eye"></i> View Class
                            </a>
                            <form method="POST" action="{{ route('classroom.leave', $classroom->id) }}"
                                  onsubmit="return confirm('Leave {{ $classroom->class_name }}? You will need a new access code to rejoin.');" style="width: 100%;">
                                @csrf
                                <button type="submit" class="st-btn leave">
                                    <i class="fas fa-sign-out-alt"></i> Leave
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- ============ ENROLL COLUMN ============ -->
    <div class="st-enroll-wrap">
        <div class="st-enroll">
            <div class="st-enroll-header">
                <div class="icon"><i class="fas fa-plus-circle"></i></div>
                <div>
                    <h3>Enroll in a Class</h3>
                    <p>Enter the access code from your teacher</p>
                </div>
            </div>

            @if($errors->any())
                <div style="background: #fee2e2; border: 2px solid #fca5a5; color: #b91c1c; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 0.95rem; font-weight: 700;">
                    <i class="fas fa-exclamation-circle"></i>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fee2e2; border: 2px solid #fca5a5; color: #b91c1c; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 0.95rem; font-weight: 700;">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div style="background: #dcfce7; border: 2px solid #86efac; color: #15803d; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; font-size: 0.95rem; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.enroll') }}">
                @csrf
                <label class="st-field-label">Access Code *</label>
                <input type="text" name="access_code" value="{{ old('access_code') }}"
                       class="st-code-input" placeholder="••••••" required maxlength="6" autocomplete="off">
                <button type="submit" class="st-enroll-btn">
                    <i class="fas fa-graduation-cap"></i> Join Class
                </button>
            </form>

            <div class="st-enroll-hint">
                <i class="fas fa-info-circle"></i>
                <span>Ask your teacher for the unique 6-digit access code to enter the classroom.</span>
            </div>
        </div>

        <div class="st-howto">
            <h4><i class="fas fa-lightbulb"></i> How to Enroll</h4>
            <ol>
                <li>Get the access code from your teacher</li>
                <li>Enter the code in the field above</li>
                <li>Click "Join Class" to enroll</li>
                <li>Start learning immediately!</li>
            </ol>
        </div>
    </div>
</div>

@endsection

@section('extra-scripts')
<script>
    function filterClasses() {
        const q = document.getElementById('stSearch').value.toLowerCase().trim();
        let visible = 0;

        document.querySelectorAll('#stList .st-card').forEach(function (card) {
            const name = card.dataset.name;
            const teacher = card.dataset.teacher;
            const show = q === '' || name.includes(q) || teacher.includes(q);
            card.classList.toggle('st-hidden', !show);
            if (show) visible++;
        });

        document.getElementById('stCount').textContent = visible;
    }

    function sortClasses() {
        const value = document.getElementById('stSort').value;
        const list = document.getElementById('stList');
        const cards = Array.from(document.querySelectorAll('#stList .st-card'));

        cards.sort(function (a, b) {
            switch (value) {
                case 'newest': return new Date(b.dataset.joined) - new Date(a.dataset.joined);
                case 'oldest': return new Date(a.dataset.joined) - new Date(b.dataset.joined);
                case 'students': return parseInt(b.dataset.students) - parseInt(a.dataset.students);
                default: return 0;
            }
        });

        cards.forEach(function (card) { list.appendChild(card); });
    }
</script>
@endsection
