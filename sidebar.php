<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AW Sports Dashboard</title>
    <style>
        .bg-dark-green {
            background-color:rgb(0, 87, 70);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <ul class="navbar-nav bg-dark-green sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="home.php">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-bicycle"></i>
            </div>
            <div class="sidebar-brand-text mx-3">AW Store</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Grafik Data Adventure Works
        </div>

        <!-- Nav Item - Sepeda Terlaris -->
        <li class="nav-item">
            <a class="nav-link" href="s_produkterlaris.php">
                <i class="fas fa-bicycle"></i>
                <span>Produk Terlaris</span>
            </a>
        </li>

        <!-- Nav Item - Jumlah Customer -->
        <li class="nav-item">
            <a class="nav-link" href="s_totalpelanggan.php">
                <i class="fas fa-users"></i>
                <span>Jumlah Pelanggan</span>
            </a>
        </li>

        <!-- Nav Item - Pendapatan Toko -->
        <li class="nav-item">
            <a class="nav-link" href="s_totalpenjualan.php">
                <i class="fas fa-chart-line"></i>
                <span>Pendapatan Toko</span>
            </a>
        </li>

        <!-- Nav Item - Penjualan per Negara -->
        <li class="nav-item">
            <a class="nav-link" href="s_terjualtiapnegara.php">
                <i class="fas fa-globe"></i>
                <span>Penjualan per Negara</span>
            </a>
        </li>

        <!-- Nav Item - Penjualan Seluruh Barang -->
        <li class="nav-item">
            <a class="nav-link" href="s_barangterjual.php">
                <i class="fas fa-shopping-basket"></i>
                <span>Penjualan Barang</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            OLAP
        </div>

        <!-- Nav Item - Mondrian -->
        <li class="nav-item">
            <a class="nav-link" href="olap.php">
                <i class="fas fa-database"></i>
                <span>Mondrian</span>
            </a>
        </li>

        
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">


        <!-- Nav Item - Logout -->
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>


    </ul>
    <!-- End of Sidebar -->
</body>
</html>