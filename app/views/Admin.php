<!DOCTYPE html>
<html lang="en">

<head>
    <base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/Admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <title>Document</title>
</head>

<body>
    <div class="contain">
        <!-- Sidebar -->
        <div id="nav-bar">
            <div class="nav-logo">
                <img id="logo" src="public/images/logo.png" alt="Logo">
            </div>
            <div class="nav-menu">
                <div class="nav-item">
                    <div class="icon-sidebar">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <a href="./Admin/show">Dashboard</a>
                </div>
                <div class="nav-item">
                    <div class="icon-sidebar">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </div>
                    <a href="./Admin/appointmentSchedule">Appointment Schedule</a>
                </div>
                <div class="nav-item">
                    <div class="icon-sidebar">
                        <i class="fa-solid fa-user-large"></i>
                    </div>
                    <a href="./Admin/userManagement">User Management</a>
                </div>
                <div class="nav-item">
                    <div class="icon-sidebar">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <a href="./Admin/doctorManagement">Doctor Management</a>
                </div>
            </div>
        </div>

        <!-- Nội dung chính -->
        <div class="content">
            <div class="header-admin">
                <div class="header-left">
                    <h3 style="color: #1d2d5e; font-weight: 600; margin: 0;">Admin Dashboard</h3>
                </div>
                <div class="header-right">
                    <div class="avatar-admin">
                        <img src="public/images/uploads/defaultAvatar.jpg" alt="Avatar Admin">
                    </div>
                    <form action="./Admin/logOutAdmin" method="POST" style="margin: 0;">
                        <button type="submit"> <i class="fa-solid fa-arrow-right-from-bracket"></i> Log out</button>
                    </form>
                </div>
            </div>
            <div class="page-content">
                <?php 
                    require_once './app/views/pages/'.$data['Page'].'.php';
                ?>
            </div>
        </div>
    </div>
</body>

</html>