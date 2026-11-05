<nav class="navbar navbar-light bg-white border-bottom px-4 py-3">
    <div class="container-fluid">
        <span class="navbar-text">
            Selamat datang, <strong>{{ auth()->user()->name }}</strong> 
            <span class="badge bg-primary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
        </span>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</nav>