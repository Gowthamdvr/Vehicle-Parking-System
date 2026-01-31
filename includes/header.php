<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase - Vehicle Parking System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/car/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/car/index.php">
                <i class="fas fa-parking me-2"></i>ParkEase
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/car/index.php">Home</a>
                    </li>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/car/admin/dashboard.php">Admin Panel</a></li>
                        <li class="nav-item"><a class="nav-link text-warning" href="/car/admin/logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['staff_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/car/staff/dashboard.php">Staff Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link text-warning" href="/car/staff/logout.php">Logout</a></li>
                    <?php elseif (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/car/user/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/car/user/select_slot.php">Book Slot</a></li>
                        <li class="nav-item"><a class="nav-link text-warning" href="/car/user/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/car/user/login.php">User Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="/car/staff/login.php">Staff Login</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold text-info" href="/car/admin/login.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
