<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WMS Pakaian - @yield('title')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .navbar-brand { font-weight: bold; color: #4e73df !important; }
        .card { border: none; border-radius: 10px; }
        .active-nav { font-weight: bold; color: #4e73df !important; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-tshirt me-2"></i>WMS Pakaian
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active-nav' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>

                    @can('manage_products')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active-nav' : '' }}" href="{{ route('products.index') }}">Data Produk</a>
                    </li>
                    @endcan

                    @canany(['create_inbound', 'approve_inbound'])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('inbounds.*') ? 'active-nav' : '' }}" href="{{ route('inbounds.index') }}">Barang Masuk</a>
                    </li>
                    @endcanany

                    @canany(['create_outbound', 'approve_outbound'])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('outbounds.*') ? 'active-nav' : '' }}" href="{{ route('outbounds.index') }}">Barang Keluar</a>
                    </li>
                    @endcanany

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('stock_opnames.*') ? 'active-nav' : '' }}" href="{{ route('stock_opnames.index') }}">Stock Opname</a>
                    </li>

                    @role('Super Admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active-nav' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-users-cog"></i> Users
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active-nav' : '' }}" href="{{ route('reports.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i> Laporan
                        </a>
                    </li>
                    @endrole
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }} 
                            <span class="badge bg-secondary ms-1">{{ ucfirst(Auth::user()->getRoleNames()->first()) }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

</body>
</html>