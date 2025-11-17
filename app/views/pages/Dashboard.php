<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/Dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Dashboard Overview</h1>
            <p>Welcome to the Admin Dashboard</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card stat-card-users">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo isset($data['stats']['totalUsers']) ? $data['stats']['totalUsers'] : 0; ?></p>
                    <span class="stat-label">Registered Patients</span>
                </div>
            </div>

            <div class="stat-card stat-card-doctors">
                <div class="stat-icon">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Doctors</h3>
                    <p class="stat-number"><?php echo isset($data['stats']['totalDoctors']) ? $data['stats']['totalDoctors'] : 0; ?></p>
                    <span class="stat-label">Active Doctors</span>
                </div>
            </div>

            <div class="stat-card stat-card-appointments">
                <div class="stat-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Appointments</h3>
                    <p class="stat-number"><?php echo isset($data['stats']['totalAppointments']) ? $data['stats']['totalAppointments'] : 0; ?></p>
                    <span class="stat-label">All Time</span>
                </div>
            </div>

            <div class="stat-card stat-card-today">
                <div class="stat-icon">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
                <div class="stat-content">
                    <h3>Today's Appointments</h3>
                    <p class="stat-number"><?php echo isset($data['stats']['todayAppointments']) ? $data['stats']['todayAppointments'] : 0; ?></p>
                    <span class="stat-label">Scheduled Today</span>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="actions-grid">
                <a href="./Admin/userManagement" class="action-card">
                    <i class="fa-solid fa-user-large"></i>
                    <h3>Manage Users</h3>
                    <p>View and manage all registered users</p>
                </a>
                <a href="./Admin/doctorManagement" class="action-card">
                    <i class="fa-solid fa-user-doctor"></i>
                    <h3>Manage Doctors</h3>
                    <p>View and manage all doctors</p>
                </a>
                <a href="./Admin/appointmentSchedule" class="action-card">
                    <i class="fa-solid fa-calendar-plus"></i>
                    <h3>View Appointments</h3>
                    <p>View all appointment schedules</p>
                </a>
                <a href="./Admin/addDoctor" class="action-card">
                    <i class="fa-solid fa-user-plus"></i>
                    <h3>Add Doctor</h3>
                    <p>Add a new doctor to the system</p>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

