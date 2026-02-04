<a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-home"></i></div>
    <div class="nav-label">Dashboard</div>
</a>
<a href="{{ route('student.classes') }}" class="nav-item {{ request()->routeIs('student.classes') || request()->routeIs('classroom.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-chalkboard"></i></div>
    <div class="nav-label">My Classes</div>
</a>
<a href="{{ route('student.progress') }}" class="nav-item {{ request()->routeIs('student.progress') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-chart-line"></i></div>
    <div class="nav-label">Progress</div>
</a>
<a href="{{ route('materials.index') }}" class="nav-item {{ request()->routeIs('materials.*') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-book-open"></i></div>
    <div class="nav-label">Materials</div>
</a>
<a href="{{ route('student.practice') }}" class="nav-item {{ request()->routeIs('student.practice') ? 'active' : '' }}">
    <div class="nav-icon"><i class="fas fa-microphone"></i></div>
    <div class="nav-label">Practice</div>
</a>
