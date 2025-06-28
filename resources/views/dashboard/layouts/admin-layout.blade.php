<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dashboard/css/style.css') }}">
    @stack('styles')

</head>

<body>
    <!-- Overlay for Mobile -->
    <div id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">DigiTrack</div>
        
        <ul>
            @can('Dashboard')
            <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            @endcan
        
            <li class="has-submenu">
                <a href="#"><i class="fas fa-cogs"></i> General Settings</a>
                <ul class="submenu">
                    <li><a href="{{ route('settings.update') }}"><i class="fas fa-tags"></i> General Settings</a></li>
                    <li><a href="{{ route('dashboard.categories') }}"><i class="fas fa-tags"></i> Manage Category</a></li>
                    <li><a href="{{ route('dashboard.subcategories') }}"><i class="fas fa-tags"></i> Manage Sub Category</a></li>
                    <li><a href="{{ route('dashboard.units') }}"><i class="fas fa-tags"></i> Manage Unit</a></li>
                    <li><a href="{{ route('dashboard.brands') }}"><i class="fas fa-tags"></i> Manage Brand</a></li>
                </ul>
            </li>

            <li class="has-submenu">
                <a href="#"><i class="fas fa-cogs"></i> Product Management</a>
                <ul class="submenu">
                    <li><a href="{{ route('dashboard.colors') }}"><i class="fas fa-tags"></i> Manage Product Color</a></li>
                    <li><a href="{{ route('dashboard.sizes') }}"><i class="fas fa-tags"></i> Manage Product Size</a></li>
                    <li><a href="{{ route('dashboard.suppliers') }}"><i class="fas fa-tags"></i> Supplier Management</a></li>
                    <li><a href="{{ route('products.index') }}"><i class="fas fa-tags"></i> Product Management</a></li>
                    <li><a href="{{ route('purchases.index') }}"><i class="fas fa-tags"></i> Purchase Mgmt</a></li>
                </ul>
            </li>
        
            

            <li><a href="#"><i class="fas fa-database"></i> Data Entry Forms</a></li>
        
          
        
            
    
            <!-- Logout Menu Option for Authenticated Users -->
            @auth
            <li>
                <li><a href="{{ route('admin.logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </li>
            @endauth
        </ul>
    </div>
    
    

    <div class="header">
        <button id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="profile-menu">
            <button class="profile-button">
                <i class="fas fa-user"></i> {{ Auth::user()->name }}
            </button>
            <div class="dropdown-menu">
                <a href="#">My Profile</a>
                <a href="#">Settings</a>
                <a href="#">Log Out</a>
            </div>
        </div>
    </div>
    
    

    <!-- Content -->
    <div class="content" id="content">

        @yield('content')
        <div id="loader-overlay">
            <div id="loader"></div>
        </div>

    </div>
    <!-- Bootstrap JS & jQuery (optional) -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!-- Bootstrap Bundle --> --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <script src="{{ asset('dashboard/js/custom.js') }}"></script> <!-- Your custom JS file -->

    @stack('scripts')
    <script>
        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Pending', 'In Progress'],
                datasets: [{
                    data: [152, 104, 45],
                    backgroundColor: ['#28a745', '#ffc107', '#17a2b8'],
                }]
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May'],
                datasets: [{
                    label: 'Interventions',
                    data: [30, 45, 60, 50, 80],
                    backgroundColor: '#007bff',
                }]
            }
        });
    </script>

    <script>
        // District-wise Bar Chart
        const districtBarCtx = document.getElementById('districtBarChart').getContext('2d');
        new Chart(districtBarCtx, {
            type: 'bar',
            data: {
                labels: ['Dhaka', 'Chattogram', 'Khulna'],
                datasets: [{
                        label: 'Total Cases',
                        data: [120, 100, 80],
                        backgroundColor: '#007bff',
                    },
                    {
                        label: 'Resolved',
                        data: [90, 70, 50],
                        backgroundColor: '#28a745',
                    },
                    {
                        label: 'Pending',
                        data: [30, 30, 30],
                        backgroundColor: '#ffc107',
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
            },
        });

        // PNGO-wise Doughnut Chart
        const pngoDoughnutCtx = document.getElementById('pngoDoughnutChart').getContext('2d');
        new Chart(pngoDoughnutCtx, {
            type: 'doughnut',
            data: {
                labels: ['PNGO A', 'PNGO B', 'PNGO C'],
                datasets: [{
                    data: [100, 80, 70],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                },
            },
        });
    </script>
</body>

</html>
