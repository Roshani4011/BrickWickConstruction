<?php session_start(); if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
} 
include 'dashboard_data.php'; // Include the data handling file
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the data from session variables
$totalProjects = $_SESSION['total_projects'];
$newServices = $_SESSION['new_services'];
$totalRevenue = $_SESSION['total_revenue'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e3c87;
            --sidebar-bg: #2c3e6e;
            --bg-light: #f6f8fb;
            --text-color: #333;
            --light-text: #6c757d;
            --white: #ffffff;
            --border-radius: 8px;
            --box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            --sidebar-width: 240px;
            --header-height: 60px;
            --card-accent-green: rgba(40, 167, 69, 0.1);
            --card-accent-blue: rgba(0, 123, 255, 0.1);
            --card-accent-orange: rgba(255, 153, 51, 0.1);
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-color);
        }
        
        .dashboard {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--white);
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .sidebar-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
        
        .sidebar-header h2 {
            font-weight: 500;
            font-size: 18px;
            color: var(--white);
        }
        
        .sidebar-menu {
            padding: 10px 0;
        }
        
        .menu-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            padding: 15px 20px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu ul li {
            margin-bottom: 2px;
        }
        
        .sidebar-menu ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            font-size: 14px;
            position: relative;
        }
        
        .sidebar-menu ul li a.active {
            color: var(--white);
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid var(--white);
            font-weight: 500;
        }
        
        .sidebar-menu ul li a:hover {
            color: var(--white);
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar-menu ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .sidebar-menu ul li a span.toggle-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
        }
        
        .submenu {
            padding-left: 30px;
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease;
        }
        
        .submenu.show {
            height: auto;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--white);
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
        }
        
        .page-title h1 {
            font-size: 20px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .search-form {
            position: relative;
        }
        
        .search-form input {
            padding: 8px 15px 8px 35px;
            border: 1px solid #e0e6ed;
            border-radius: 20px;
            font-size: 14px;
            width: 220px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .search-form input:focus {
            border-color: #b6c2db;
            width: 240px;
        }
        
        .search-form i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-text);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-menu img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .notification-bell {
            position: relative;
            color: var(--light-text);
            cursor: pointer;
        }
        
        .notification-bell i {
            font-size: 18px;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--light-text);
            font-size: 18px;
            cursor: pointer;
            display: none;
        }
        
        /* Alert Banner */
        .alert-banner {
            background-color: #5587ec;
            color: var(--white);
            padding: 12px 20px;
            border-radius: var(--border-radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .alert-banner p {
            font-size: 14px;
        }
        
        .alert-banner a {
            color: var(--white);
            text-decoration: underline;
        }
        
        .alert-banner .close-btn {
            background: none;
            border: none;
            color: var(--white);
            cursor: pointer;
            font-size: 16px;
        }
        
        /* Card Styles */
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .card-header h2 {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        .card-header .card-actions {
            color: var(--light-text);
            font-size: 16px;
            cursor: pointer;
        }
        
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: var(--white);
            min-width: 150px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
            border-radius: var(--border-radius);
        }
        
        .dropdown-content a {
            color: var(--text-color);
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        
        .dropdown-content a:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .stat-icon.green {
            background-color: var(--card-accent-green);
            color: var(--success-color);
        }
        
        .stat-icon.blue {
            background-color: var(--card-accent-blue);
            color: var(--info-color);
        }
        
        .stat-icon.orange {
            background-color: var(--card-accent-orange);
            color: #ff9933;
        }
        
        .stat-icon i {
            font-size: 24px;
        }
        
        .stat-content h3 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stat-content p {
            color: var(--light-text);
            font-size: 14px;
        }
        
        /* Project Cards */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .project-card {
            position: relative;
            padding-bottom: 15px;
        }
        
        .project-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .project-header h3 {
            font-size: 16px;
            font-weight: 500;
        }
        
        .project-progress {
            height: 6px;
            background-color: #e9ecef;
            border-radius: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
        }
        
        .progress-bar.green {
            background-color: var(--success-color);
        }
        
        .progress-bar.blue {
            background-color: var(--info-color);
        }
        
        .progress-value {
            font-size: 14px;
            color: var(--light-text);
            float: right;
        }
        
        /* Contact Cards */
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .contact-card {
            text-align: center;
            padding: 15px;
            border-radius: var(--border-radius);
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            background-color: #e9ecef;
        }
        
        .contact-card img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid var(--white);
        }
        
        .contact-card h4 {
            font-size: 16px;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .contact-card p {
            color: var(--light-text);
            font-size: 13px;
        }
        
        .contact-actions {
            margin-top: 10px;
        }
        
        .contact-actions a {
            display: inline-block;
            width: 30px;
            height: 30px;
            background-color: var(--white);
            border-radius: 50%;
            color: var(--light-text);
            line-height: 30px;
            margin: 0 3px;
            transition: all 0.3s ease;
        }
        
        .contact-actions a:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-grid, .projects-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 992px) {
            .toggle-sidebar {
                display: block;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.pushed {
                margin-left: var(--sidebar-width);
            }
            
            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
                z-index: 999;
                display: none;
            }
            
            .overlay.active {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid, .projects-grid {
                grid-template-columns: 1fr;
            }
            
            .contacts-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                margin-top: 15px;
                justify-content: space-between;
            }
            
            .search-form input {
                width: 180px;
            }
        }
        
        @media (max-width: 576px) {
            .contacts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Overlay for mobile -->
        <div class="overlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="pictures\Group 7.png" alt="Logo">
                <h2>Admin Panel</h2>
            </div>
            
            <div class="sidebar-menu">
                <div class="menu-label">Main</div>
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="contacts.php"><i class="fas fa-users"></i> Contacts</a></li>
                    <li>
                        <a href="#"><i class="fas fa-project-diagram"></i> Projects <span class="toggle-btn"><i class="fas fa-chevron-down"></i></span></a>
                        <ul class="submenu">
                            <li><a href="projects.php"><i class="fas fa-list"></i> All Projects</a></li>
                            <li><a href="add_projects.php"><i class="fas fa-plus"></i> Add New</a></li>
                        </ul>
                    </li>
                    <li><a href="services.php"><i class="fas fa-tools"></i> Services</a></li>
                </ul>
                
                <div class="menu-label">Account</div>
                <ul>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <button class="toggle-sidebar"><i class="fas fa-bars"></i></button>
                
                <div class="page-title">
                    <h1>Dashboard</h1>
                </div>
                
                <div class="header-actions">
                    <form class="search-form">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </form>
                    
                    <div class="notification-bell">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    
                    <div class="user-menu">
                        <img src="https://via.placeholder.com/36" alt="Admin">
                        <span>Anil Sharma</span>
                    </div>
                </div>
            </header>
            
            <!-- Alert Banner -->
            <div class="alert-banner">
                <div>
                    <p>Introducing new dashboard! <a href="#">Download now</a> at themeforest.net</p>
                </div>
                <button class="close-btn"><i class="fas fa-times"></i></button>
            </div>
            
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="card">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>$<?php echo $totalRevenue; ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalProjects; ?></h3>
                            <p>Total Projects</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $newServices; ?></h3>
                            <p>New Service Requests</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Projects Section -->
            <div class="card">
                <div class="card-header">
                    <h2>Ongoing Projects</h2>
                    <div class="dropdown card-actions">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="dropdown-content">
                            <a href="#"><i class="fas fa-sync-alt"></i> Refresh</a>
                            <a href="#"><i class="fas fa-eye"></i> View All</a>
                            <a href="#"><i class="fas fa-plus"></i> Add New</a>
                        </div>
                    </div>
                </div>
                
                <div class="projects-grid">
                    <div class="project-card">
                        <div class="project-header">
                            <h3>Project A</h3>
                            <span class="badge">In Progress</span>
                        </div>
                        <div class="project-progress">
                            <div class="progress-bar green" style="width: 60%"></div>
                        </div>
                        <div class="progress-value">60%</div>
                    </div>
                    
                    <div class="project-card">
                        <div class="project-header">
                            <h3>Project B</h3>
                            <span class="badge">Starting</span>
                        </div>
                        <div class="project-progress">
                            <div class="progress-bar blue" style="width: 20%"></div>
                        </div>
                        <div class="progress-value">20%</div>
                    </div>
                </div>
            </div>
            
            <!-- Contacts Section -->
            <div class="card">
                <div class="card-header">
                    <h2>Client Contacts</h2>
                    <div class="dropdown card-actions">
                        <i class="fas fa-ellipsis-v"></i>
                        <div class="dropdown-content">
                            <a href="#"><i class="fas fa-filter"></i> Filter</a>
                            <a href="#"><i class="fas fa-eye"></i> View All</a>
                            <a href="#"><i class="fas fa-plus"></i> Add New</a>
                        </div>
                    </div>
                </div>
                
                <div class="contacts-grid">
                    <div class="contact-card">
                        <img src="https://via.placeholder.com/70" alt="Client 1">
                        <h4>Client 1</h4>
                        <p>client1@example.com</p>
                        <div class="contact-actions">
                            <a href="#"><i class="fas fa-envelope"></i></a>
                            <a href="#"><i class="fas fa-phone"></i></a>
                            <a href="#"><i class="fas fa-user-edit"></i></a>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <img src="https://via.placeholder.com/70" alt="Client 2">
                        <h4>Client 2</h4>
                        <p>client2@example.com</p>
                        <div class="contact-actions">
                            <a href="#"><i class="fas fa-envelope"></i></a>
                            <a href="#"><i class="fas fa-phone"></i></a>
                            <a href="#"><i class="fas fa-user-edit"></i></a>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <img src="https://via.placeholder.com/70" alt="Client 3">
                        <h4>Client 3</h4>
                        <p>client3@example.com</p>
                        <div class="contact-actions">
                            <a href="#"><i class="fas fa-envelope"></i></a>
                            <a href="#"><i class="fas fa-phone"></i></a>
                            <a href="#"><i class="fas fa-user-edit"></i></a>
                        </div>
                    </div>
                    
                    <div class="contact-card">
                        <img src="https://via.placeholder.com/70" alt="Client 4">
                        <h4>Client 4</h4>
                        <p>client4@example.com</p>
                        <div class="contact-actions">
                            <a href="#"><i class="fas fa-envelope"></i></a>
                            <a href="#"><i class="fas fa-phone"></i></a>
                            <a href="#"><i class="fas fa-user-edit"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const overlay = document.querySelector('.overlay');
            
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('pushed');
                overlay.classList.toggle('active');
            });
            
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('active');
                mainContent.classList.remove('pushed');
                overlay.classList.remove('active');
            });
            
            // Submenu Toggle
            const menuItems = document.querySelectorAll('.sidebar-menu ul li a');
            
            menuItems.forEach(item => {
                if (item.nextElementSibling && item.nextElementSibling.classList.contains('submenu')) {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const submenu = this.nextElementSibling;
                        submenu.classList.toggle('show');
                        
                        // Toggle the arrow icon
                        const toggleIcon = this.querySelector('.toggle-btn i');
                        toggleIcon.classList.toggle('fa-chevron-down');
                        toggleIcon.classList.toggle('fa-chevron-up');
                    });
                }
            });
            
            // Close Alert Banner
            const closeBtn = document.querySelector('.alert-banner .close-btn');
            const alertBanner = document.querySelector('.alert-banner');
            
            closeBtn.addEventListener('click', function() {
                alertBanner.style.display = 'none';
            });
        });
    </script>
</body>
</html>