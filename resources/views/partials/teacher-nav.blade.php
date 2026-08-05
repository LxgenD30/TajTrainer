<a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-home"></i></div>
    <div class="nav-label">Dashboard</div>
</a>
<a href="{{ route('classroom.index') }}" class="nav-item {{ request()->routeIs('classroom.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></div>
    <div class="nav-label">My Classes</div>
</a>
<a href="{{ route('students.list') }}" class="nav-item {{ request()->routeIs('students.*') || request()->routeIs('teacher.student.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-user-graduate"></i></div>
    <div class="nav-label">My Students</div>
</a>
<a href="{{ route('materials.index') }}" class="nav-item {{ request()->routeIs('materials.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-book-open"></i></div>
    <div class="nav-label">Materials</div>
</a>
