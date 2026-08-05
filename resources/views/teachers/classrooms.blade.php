@extends('layouts.dashboard')

@section('title', 'My Classrooms')
@section('user-role', 'Teacher • Classroom Management')

@section('navigation')
    @include('partials.teacher-nav')
@endsection

@section('content')
@php
    $totalClassrooms = $classrooms->count();
    $totalStudents   = $classrooms->sum('students_count');
    $totalAssignments = $classrooms->sum('assignments_count');
    $totalPending    = $classrooms->sum('pending_assignments_count');
@endphp

<style>
    /* ============ HERO ============ */
    .cl-hero {
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

    .cl-hero:before {
        content: '';
        position: absolute;
        top: -100px;
        right: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
        pointer-events: none;
    }

    .cl-hero-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .cl-hero h1 {
        margin: 0 0 4px;
        font-size: 2.1rem;
        line-height: 1.2;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cl-hero p {
        margin: 0;
        opacity: 0.95;
        font-size: 1.05rem;
    }

    .cl-hero-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .cl-btn-create {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: linear-gradient(135deg, #d4af37, #f4d03f);
        color: #111827;
        padding: 12px 24px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        border: 2px solid #3d3520;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        transition: all 0.25s ease;
        font-family: 'Cairo', sans-serif;
        white-space: nowrap;
    }

    .cl-btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.28);
        background: linear-gradient(135deg, #f4d03f, #d4af37);
    }

    /* ============ STATS STRIP ============ */
    .cl-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }

    .cl-stat {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s ease;
        animation: clFadeUp 0.5s ease both;
    }

    .cl-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 26px rgba(10, 92, 54, 0.12);
    }

    .cl-stat:nth-child(2) { animation-delay: 0.06s; }
    .cl-stat:nth-child(3) { animation-delay: 0.12s; }
    .cl-stat:nth-child(4) { animation-delay: 0.18s; }

    .cl-stat-icon {
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

    .cl-stat-icon.green { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .cl-stat-icon.blue { background: linear-gradient(135deg, #3498db, #2980b9); }
    .cl-stat-icon.gold { background: linear-gradient(135deg, #d4af37, #b8860b); }
    .cl-stat-icon.amber { background: linear-gradient(135deg, #f39c12, #e67e22); }

    .cl-stat-value {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0a5c36;
        line-height: 1;
    }

    .cl-stat-label {
        color: #666;
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 4px;
    }

    /* ============ TOOLBAR ============ */
    .cl-toolbar {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 26px;
        box-shadow: 0 8px 20px rgba(14, 28, 18, 0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .cl-search {
        position: relative;
        flex: 1 1 240px;
        min-width: 220px;
    }

    .cl-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0a5c36;
        font-size: 0.9rem;
    }

    .cl-search input {
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

    .cl-search input:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    .cl-sort {
        position: relative;
        min-width: 200px;
    }

    .cl-sort i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #0a5c36;
        font-size: 0.9rem;
        pointer-events: none;
    }

    .cl-sort select {
        width: 100%;
        padding: 11px 34px 11px 38px;
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

    .cl-sort select:focus {
        outline: none;
        border-color: #0a5c36;
        box-shadow: 0 0 0 3px rgba(10, 92, 54, 0.12);
    }

    .cl-count {
        font-size: 0.95rem;
        color: #5f6f65;
        font-weight: 700;
        white-space: nowrap;
    }

    .cl-count span { color: #0a5c36; }

    /* ============ CARDS ============ */
    .cl-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
        gap: 24px;
    }

    .cl-card {
        background: #fff;
        border: 2px solid #2a2a2a;
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(14, 28, 18, 0.07);
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        animation: clFadeUp 0.5s ease both;
        display: flex;
        flex-direction: column;
    }

    .cl-card:nth-child(2n) { animation-delay: 0.06s; }
    .cl-card:nth-child(3n) { animation-delay: 0.12s; }
    .cl-card:nth-child(4n) { animation-delay: 0.18s; }

    .cl-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 38px rgba(10, 92, 54, 0.16);
    }

    .cl-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 6px;
        background: linear-gradient(180deg, #1abc9c, #0a5c36);
        border-radius: 18px 0 0 18px;
    }

    .cl-card-header {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 14px;
    }

    .cl-card-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.25);
    }

    .cl-card-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #0a5c36;
        margin: 0 0 5px;
        font-family: 'El Messiri', serif;
        line-height: 1.3;
    }

    .cl-card-desc {
        color: #5f6f65;
        font-size: 0.95rem;
        line-height: 1.55;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 2.9em;
    }

    /* Access code */
    .cl-code {
        background: #f8f1d5;
        border: 2px solid #e5d699;
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .cl-code-label {
        font-size: 0.78rem;
        font-weight: 800;
        color: #8a6d0b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .cl-code-value {
        font-family: 'Courier New', monospace;
        font-size: 1.35rem;
        font-weight: 800;
        color: #0a5c36;
        letter-spacing: 5px;
        background: #fff;
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid #e5d699;
    }

    .cl-code-toggle {
        background: #0a5c36;
        color: #fff;
        border: none;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .cl-code-toggle:hover {
        background: #1abc9c;
        transform: scale(1.08);
    }

    /* Stats row */
    .cl-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }

    .cl-mini-stat {
        text-align: center;
        padding: 12px 6px;
        background: #f8fcf8;
        border: 2px solid #deeadf;
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .cl-mini-stat:hover { background: #edf8ef; }

    .cl-mini-stat .v {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0a5c36;
        line-height: 1.1;
    }

    .cl-mini-stat .v.amber { color: #f39c12; }
    .cl-mini-stat .v.green { color: #2ecc71; }

    .cl-mini-stat .k {
        font-size: 0.72rem;
        font-weight: 700;
        color: #5f6f65;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 3px;
    }

    /* Actions */
    .cl-actions {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 10px;
        margin-top: auto;
    }

    .cl-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 11px 16px;
        border-radius: 11px;
        text-decoration: none;
        font-weight: 800;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
    }

    .cl-btn.view {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        color: #fff;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.22);
    }

    .cl-btn.view:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(10, 92, 54, 0.3); }

    .cl-btn.edit {
        background: #fff;
        color: #0a5c36;
        border-color: #0a5c36;
    }

    .cl-btn.edit:hover { background: #0a5c36; color: #fff; }

    .cl-btn.delete {
        background: #fff;
        color: #e74c3c;
        border-color: #e74c3c;
        padding: 11px 14px;
    }

    .cl-btn.delete:hover { background: #e74c3c; color: #fff; }

    /* Empty state */
    .cl-empty {
        background: #fff;
        border: 2px dashed #b7c6ba;
        border-radius: 18px;
        padding: 70px 30px;
        text-align: center;
        color: #5f6f65;
        animation: clFadeUp 0.5s ease both;
    }

    .cl-empty i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 16px;
        display: block;
        color: #0a5c36;
    }

    .cl-empty h3 {
        color: #0a5c36;
        font-size: 1.5rem;
        margin: 0 0 8px;
        font-family: 'El Messiri', serif;
    }

    .cl-empty p {
        margin: 0 0 24px;
        font-size: 1.05rem;
    }

    .cl-hidden { display: none !important; }

    /* Keyframes */
    @keyframes clFadeUp {
        from { opacity: 0; transform: translateY(22px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 720px) {
        .cl-hero { padding: 22px; }
        .cl-hero-content { flex-direction: column; align-items: flex-start; }
        .cl-btn-create { width: 100%; justify-content: center; }
        .cl-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ============ HERO ============ -->
<div class="cl-hero">
    <div class="cl-hero-content">
        <div>
            <h1><i class="fas fa-chalkboard-teacher"></i> My Classrooms</h1>
            <p>Create and manage virtual classrooms for your Tajweed students</p>
        </div>
        <div class="cl-hero-actions">
            <a href="{{ route('classroom.create') }}" class="cl-btn-create">
                <i class="fas fa-plus-circle"></i> Create New Class
            </a>
        </div>
    </div>
</div>

@if($classrooms->isEmpty())
    <!-- ============ EMPTY STATE ============ -->
    <div class="cl-empty">
        <i class="fas fa-chalkboard"></i>
        <h3>No Classrooms Yet</h3>
        <p>Create your first classroom to start teaching and managing students</p>
        <a href="{{ route('classroom.create') }}" class="cl-btn-create" style="display: inline-flex;">
            <i class="fas fa-plus-circle"></i> Create Your First Classroom
        </a>
    </div>
@else
    <!-- ============ STATS STRIP ============ -->
    <div class="cl-stats">
        <div class="cl-stat">
            <div class="cl-stat-icon blue"><i class="fas fa-chalkboard"></i></div>
            <div>
                <div class="cl-stat-value">{{ $totalClassrooms }}</div>
                <div class="cl-stat-label">Classrooms</div>
            </div>
        </div>
        <div class="cl-stat">
            <div class="cl-stat-icon green"><i class="fas fa-user-graduate"></i></div>
            <div>
                <div class="cl-stat-value">{{ $totalStudents }}</div>
                <div class="cl-stat-label">Total Students</div>
            </div>
        </div>
        <div class="cl-stat">
            <div class="cl-stat-icon gold"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="cl-stat-value">{{ $totalAssignments }}</div>
                <div class="cl-stat-label">Assignments</div>
            </div>
        </div>
        <div class="cl-stat">
            <div class="cl-stat-icon amber"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <div class="cl-stat-value">{{ $totalPending }}</div>
                <div class="cl-stat-label">Pending Tasks</div>
            </div>
        </div>
    </div>

    <!-- ============ TOOLBAR ============ -->
    <div class="cl-toolbar">
        <div class="cl-search">
            <i class="fas fa-search"></i>
            <input type="text" id="clSearch" placeholder="Search classrooms..." onkeyup="filterClassrooms()">
        </div>
        <div class="cl-sort">
            <i class="fas fa-filter"></i>
            <select id="clSort" onchange="sortClassrooms()">
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
                <option value="students">Most Students</option>
                <option value="assignments">Most Assignments</option>
            </select>
        </div>
        <div class="cl-count"><span id="clCount">{{ $totalClassrooms }}</span> classroom{{ $totalClassrooms != 1 ? 's' : '' }}</div>
    </div>

    <!-- ============ CARDS ============ -->
    <div class="cl-grid" id="clGrid">
        @foreach($classrooms as $classroom)
            <div class="cl-card"
                 data-name="{{ strtolower($classroom->class_name) }}"
                 data-desc="{{ strtolower($classroom->description ?? '') }}"
                 data-created="{{ $classroom->created_at }}"
                 data-students="{{ $classroom->students_count ?? 0 }}"
                 data-assignments="{{ $classroom->assignments_count ?? 0 }}">

                <div class="cl-card-header">
                    <div class="cl-card-icon"><i class="fas fa-book-quran"></i></div>
                    <div>
                        <h3 class="cl-card-title">{{ $classroom->class_name }}</h3>
                        <p class="cl-card-desc">{{ $classroom->description ?: 'No description provided' }}</p>
                    </div>
                </div>

                <!-- Access Code -->
                <div class="cl-code">
                    <div>
                        <div class="cl-code-label"><i class="fas fa-key"></i> Access Code</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="cl-code-value" id="code-{{ $classroom->id }}">••••••</span>
                        <span style="display: none;" id="real-{{ $classroom->id }}">{{ $classroom->access_code }}</span>
                        <button class="cl-code-toggle" onclick="toggleCode({{ $classroom->id }})" title="Show/Hide access code">
                            <i class="fas fa-eye" id="icon-{{ $classroom->id }}"></i>
                        </button>
                    </div>
                </div>

                <!-- Stats -->
                <div class="cl-stats-row">
                    <div class="cl-mini-stat">
                        <div class="v">{{ $classroom->students_count ?? 0 }}</div>
                        <div class="k">Students</div>
                    </div>
                    <div class="cl-mini-stat">
                        <div class="v">{{ $classroom->assignments_count ?? 0 }}</div>
                        <div class="k">Tasks</div>
                    </div>
                    <div class="cl-mini-stat">
                        <div class="v amber">{{ $classroom->pending_assignments_count ?? 0 }}</div>
                        <div class="k">Pending</div>
                    </div>
                    <div class="cl-mini-stat">
                        <div class="v green">{{ $classroom->completed_assignments_count ?? 0 }}</div>
                        <div class="k">Done</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="cl-actions">
                    <a href="{{ route('classroom.show', $classroom->id) }}" class="cl-btn view">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('classroom.edit', $classroom->id) }}" class="cl-btn edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('classroom.destroy', $classroom->id) }}" method="POST"
                          onsubmit="return confirm('Delete this classroom? This cannot be undone.');" style="display: contents;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="cl-btn delete" title="Delete classroom">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection

@section('extra-scripts')
<script>
    function toggleCode(id) {
        const el = document.getElementById('code-' + id);
        const real = document.getElementById('real-' + id).textContent.trim();
        const icon = document.getElementById('icon-' + id);

        if (el.textContent.indexOf('•') !== -1) {
            el.textContent = real;
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            el.textContent = '••••••';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function filterClassrooms() {
        const q = document.getElementById('clSearch').value.toLowerCase().trim();
        let visible = 0;

        document.querySelectorAll('.cl-card').forEach(function (card) {
            const name = card.dataset.name;
            const desc = card.dataset.desc;
            const show = q === '' || name.includes(q) || desc.includes(q);
            card.classList.toggle('cl-hidden', !show);
            if (show) visible++;
        });

        document.getElementById('clCount').textContent = visible;
    }

    function sortClassrooms() {
        const value = document.getElementById('clSort').value;
        const grid = document.getElementById('clGrid');
        const cards = Array.from(document.querySelectorAll('.cl-card'));

        cards.sort(function (a, b) {
            switch (value) {
                case 'newest': return new Date(b.dataset.created) - new Date(a.dataset.created);
                case 'oldest': return new Date(a.dataset.created) - new Date(b.dataset.created);
                case 'students': return parseInt(b.dataset.students) - parseInt(a.dataset.students);
                case 'assignments': return parseInt(b.dataset.assignments) - parseInt(a.dataset.assignments);
                default: return 0;
            }
        });

        cards.forEach(function (card) { grid.appendChild(card); });
    }
</script>
@endsection
