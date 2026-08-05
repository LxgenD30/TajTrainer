<div class="nav-bar" data-navbar>
    <button type="button" class="nav-bar-toggle" data-navbar-toggle aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
    </button>

    <div class="nav-bar-links" data-navbar-links>
        <div class="nav-section">Main</div>
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
        <div class="nav-section">Resources</div>
        <a href="{{ route('materials.index') }}" class="nav-item {{ request()->routeIs('materials.*') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-book-open"></i></div>
            <div class="nav-label">Materials</div>
        </a>
    </div>
</div>

<script>
(function () {
    const bar = document.querySelector('[data-navbar]');
    if (!bar) return;
    const toggle = bar.querySelector('[data-navbar-toggle]');
    const links = bar.querySelector('[data-navbar-links]');
    if (!toggle || !links) return;

    function close() {
        bar.classList.remove('open');
        const icon = toggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    }

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = bar.classList.toggle('open');
        const icon = toggle.querySelector('i');
        if (isOpen) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    links.querySelectorAll('.nav-item').forEach(function (item) {
        item.addEventListener('click', close);
    });

    document.addEventListener('click', function (e) {
        if (!bar.contains(e.target)) close();
    });
})();
</script>
