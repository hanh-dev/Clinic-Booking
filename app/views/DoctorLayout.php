<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - MediCare</title>
    <style>
    /* Base Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background-color: #f9fafb;
        color: #374151;
    }

    .container {
        display: flex;
        min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 256px;
        background-color: #f0fdfd;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        transition: width 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .sidebar.collapsed {
        width: 64px;
    }

    /* Fixed sidebar collapsed state styling */
    .sidebar.collapsed .logo-text,
    .sidebar.collapsed .nav-item span {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    .sidebar:not(.collapsed) .logo-text,
    .sidebar:not(.collapsed) .nav-item span {
        opacity: 1;
        visibility: visible;
        transition: opacity 0.3s ease 0.1s, visibility 0.3s ease 0.1s;
    }

    .logo {
        padding: 1.3rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .logo-icon {
        width: 32px;
        height: 32px;
        background-color: #0cb8b6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        flex-shrink: 0;
    }

    .logo-text {
        font-size: 1.25rem;
        font-weight: 600;
        color: #0cb8b6;
        white-space: nowrap;
    }

    .nav {
        flex: 1;
        padding: 1rem 0;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
        color: #6b7280;
        text-decoration: none;
    }

    .nav-item:hover {
        background-color: #e6fffa;
        color: #0cb8b6;
    }

    .nav-item.active {
        background-color: #0cb8b6;
        color: white;
    }

    .nav-item i {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .nav-item span {
        white-space: nowrap;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .header {
        background-color: white;
        border-bottom: 1px solid #e5e7eb;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .toggle-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s;
    }

    .toggle-btn:hover {
        background-color: #f3f4f6;
    }

    .toggle-btn i {
        width: 20px;
        height: 20px;
        color: #6b7280;
    }

    .header-title h1 {
        font-size: 2rem;
        font-weight: 600;
        color: #1f2937;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .notification-btn {
        position: relative;
        background: none;
        border: none;
        padding: 0.5rem;
        border-radius: 6px;
        cursor: pointer;
        color: #6b7280;
    }

    .notification-btn:hover {
        background-color: #f3f4f6;
    }

    .notification-badge {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background-color: #0cb8b6;
        border-radius: 50%;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #0cb8b6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    /* Content Area */
    .content {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Cards */
    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: linear-gradient(135deg, #f0fdfd 0%, #e6fffa 100%);
        border: 1px solid rgba(12, 184, 182, 0.2);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(12, 184, 182, 0.15);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .stat-title {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }

    .stat-icon {
        color: #0cb8b6;
    }

    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #0cb8b6;
        margin-bottom: 4px;
    }

    .stat-change {
        font-size: 12px;
        color: #6b7280;
    }

    /* Buttons */
    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background-color: #0cb8b6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0891b2;
    }

    .btn-outline {
        background-color: transparent;
        color: #0cb8b6;
        border: 1px solid #0cb8b6;
    }

    .btn-outline:hover {
        background-color: #0cb8b6;
        color: white;
    }

    .btn-danger {
        background-color: #dc2626;
        color: white;
    }

    .btn-danger:hover {
        background-color: #b91c1c;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    /* Schedule Management */
    .schedule-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .schedule-header h2 {
        font-size: 24px;
        font-weight: bold;
        color: #1f2937;
    }

    .schedule-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .schedule-item {
        display: flex;
        align-items: center;
        justify-content: between;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        transition: all 0.2s ease;
    }

    .schedule-item:hover {
        background-color: #f9fafb;
    }

    .schedule-info {
        flex: 1;
    }

    .schedule-info h4 {
        font-size: 16px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .schedule-info p {
        font-size: 14px;
        color: #6b7280;
    }

    .schedule-actions {
        display: flex;
        gap: 8px;
    }

    .break-info {
        font-size: 12px;
        color: #0cb8b6;
        background-color: #f0fdfd;
        padding: 4px 8px;
        border-radius: 4px;
        margin-top: 4px;
        display: inline-block;
    }

    .no-data {
        text-align: center;
        color: #6b7280;
        font-style: italic;
        padding: 40px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
    }

    .modal-close {
        background: none;
        border: none;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-close:hover {
        background-color: #f3f4f6;
    }

    .modal-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #374151;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #0cb8b6;
        box-shadow: 0 0 0 3px rgba(12, 184, 182, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }

    /* Time Slots Grid */
    .time-slots-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 8px;
    }

    .time-slot {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: #f9fafb;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
        min-width: 80px;
        text-align: center;
    }

    .time-slot:hover {
        border-color: #0cb8b6;
        background: #f0fdfc;
    }

    .time-slot.selected {
        background: #1e3a8a;
        color: white;
        border-color: #1e3a8a;
    }

    .time-slot.selected:hover {
        background: #1e40af;
        border-color: #1e40af;
    }
    .time-slot.disabled {
        background: #e5e7eb;
        color: #9ca3af;
        border-color: #d1d5db;
        cursor: not-allowed;
        text-decoration: line-through;
    }

    /* Appointments */
    .appointment-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        border-radius: 8px;
        background-color: rgba(240, 253, 253, 0.5);
        margin-bottom: 12px;
        transition: background-color 0.2s ease;
    }

    .appointment-item:hover {
        background-color: #f0fdfd;
    }

    .appointment-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .appointment-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #0cb8b6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    .appointment-info h4 {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .appointment-info p {
        font-size: 14px;
        color: #6b7280;
    }

    .appointment-right {
        text-align: right;
    }

    .appointment-time {
        font-weight: 500;
        color: #1f2937;
        margin-bottom: 4px;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
        background-color: #f0fdfd;
        color: #0cb8b6;
        border: 1px solid rgba(12, 184, 182, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 64px;
        }

        /* Fixed mobile responsive behavior */
        .sidebar .logo-text,
        .sidebar .nav-item span {
            opacity: 0;
            visibility: hidden;
        }
    }

    .hidden {
        display: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar" id="sidebar">
            <div class="logo">
                <div class="logo-icon">D</div>
                <div class="logo-text">Doctor Portal</div>
            </div>

            <nav class="nav">
                <a href="#" class="nav-item active" onclick="switchTab('dashboard')">
                    <!-- Replaced lucide icons with simple text symbols -->
                    <span style="width: 20px; text-align: center;">📊</span>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('appointments')">
                    <span style="width: 20px; text-align: center;">📅</span>
                    <span>Appointments</span>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('patients')">
                    <span style="width: 20px; text-align: center;">👥</span>
                    <span>Patients</span>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('schedule')">
                    <span style="width: 20px; text-align: center;">⏰</span>
                    <span>Schedule</span>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('analytics')">
                    <span style="width: 20px; text-align: center;">📈</span>
                    <span>Analytics</span>
                </a>
                <a href="#" class="nav-item" onclick="switchTab('settings')">
                    <span style="width: 20px; text-align: center;">⚙️</span>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="header-content">
                    <div class="header-left">
                        <button class="toggle-btn" onclick="toggleSidebar()">
                            <!-- Replaced lucide icon with simple arrow -->
                            <span id="toggleIcon">◀</span>
                        </button>
                        <div class="header-title">
                            <h1 id="pageTitle">Dashboard</h1>
                        </div>
                    </div>
                    <div class="header-right">
                        <button class="notification-btn">
                            <span class="notification-badge"></span>
                        </button>
                        <div class="avatar">DS</div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <main class="content">
                <div class="content-wrapper">
                    <!-- Dashboard Tab -->
                    <div id="dashboard" class="tab-content active">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Today's Appointments</span>
                                    <span class="stat-icon">📅</span>
                                </div>
                                <div class="stat-value" data-key="today">0</div>
                                <div class="stat-change">+2 from yesterday</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Total Patients</span>
                                    <span class="stat-icon">👥</span>
                                </div>
                                <div class="stat-value" data-key="patients">0</div>
                                <div class="stat-change">+15 this week</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Monthly Revenue</span>
                                    <span class="stat-icon">$</span>
                                </div>
                                <div class="stat-value" data-key="revenue">$0</div>
                                <div class="stat-change">+8% from last month</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-header">
                                    <span class="stat-title">Patient Rating</span>
                                    <span class="stat-icon">⭐</span>
                                </div>
                                <div class="stat-value">4.9</div>
                                <div class="stat-change">Based on 156 reviews</div>
                            </div>
                        </div>

                        <div class="card">
                            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <span style="color: #0cb8b6;">📅</span>
                                Today's Appointments
                            </h3>
                            <div id="appointmentsList"></div>
                        </div>
                    </div>

                    <!-- Schedule Tab -->
                    <div id="schedule" class="tab-content">
                        <div class="schedule-header">
                            <h2>Schedule Management</h2>
                            <button class="btn btn-primary" onclick="openModal('addScheduleModal')">
                                <span style="color: #0cb8b6;">➕</span>
                                Add Schedule
                            </button>
                        </div>

                        <div class="card">
                            <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <span style="color: #0cb8b6;">⏰</span>
                                Your Schedules
                            </h3>
                            <div class="schedule-list" id="scheduleList">
                                <p class="no-data">No schedules available. Add your first schedule!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Other tabs (simplified for brevity) -->
                    <div id="appointments" class="tab-content">
                        <div class="card">
                            <h2>Appointments</h2>
                            <div id="appointmentsContainer" class="schedule-list"></div>
                        </div>
                    </div>

                    <div id="patients" class="tab-content">
                        <div class="card">
                            <h2>Patients</h2>
                            <p>Patient management coming soon...</p>
                        </div>
                    </div>

                    <div id="analytics" class="tab-content">
                        <div class="card">
                            <h2>Analytics</h2>
                            <p>Analytics dashboard coming soon...</p>
                        </div>
                    </div>

                    <div id="settings" class="tab-content">
                        <div class="card">
                            <h2>Settings</h2>
                            <p>Settings panel coming soon...</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal" id="addScheduleModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Schedule</h3>
                <button class="modal-close" onclick="closeModal('addScheduleModal')">
                    <span style="color: #6b7280;">✖️</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addScheduleForm">
                    <div class="form-group">
                        <label for="scheduleDate">Date</label>
                        <input type="date" id="scheduleDate" name="scheduleDate" required>
                    </div>

                    <div class="form-group">
                        <label>Available Time Slots</label>
                        <div class="time-slots-grid" id="timeSlots">
                            <!-- Time slots will be generated by JavaScript -->
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline"
                            onclick="closeModal('addScheduleModal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Detail Modal -->
    <div class="modal" id="scheduleDetailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detailModalTitle">Schedule detail</h3>
                <button class="modal-close" onclick="closeModal('scheduleDetailModal')">
                    <span style="color: #6b7280;">✖️</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detailSlots" class="time-slots-grid"></div>
            </div>
        </div>
    </div>

    <script>
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const BASE = (function(){
            const p = window.location.pathname;
            const m1 = p.indexOf('/PHP_CLINIC/Clinic-Booking/');
            if (m1 !== -1) return '/PHP_CLINIC/Clinic-Booking/';
            const m2 = p.indexOf('/Clinic-Booking/');
            if (m2 !== -1) return '/Clinic-Booking/';
            // fallback relative root
            return '/';
        })();
        const api = (path) => BASE + 'index.php?url=' + path;
        console.log('Doctor Dashboard loaded');
        // Load schedules into Schedule tab
        fetch(api('DoctorLayout/apiListSchedules'))
            .then(r => r.json())
            .then(data => {
                if (data && Array.isArray(data.schedules)) {
                    renderSchedules(data.schedules);
                }
            })
            .catch(() => {});

        // Load appointments
        fetch(api('DoctorLayout/apiListAppointments'))
            .then(r => {
                if (!r.ok) { throw new Error('HTTP '+r.status); }
                return r.json();
            })
            .then(data => {
                renderAppointments(data.appointments || []);
            })
            .catch(err => {
                console.error('Failed to load appointments', err);
                renderAppointments([]);
            });

        // Load dashboard stats
        fetch(api('DoctorLayout/apiDashboardStats'))
            .then(r=>r.json())
            .then(data => {
                const s = data.stats || {};
                const today = document.querySelector('#dashboard .stat-value[data-key="today"]');
                const patients = document.querySelector('#dashboard .stat-value[data-key="patients"]');
                const revenue = document.querySelector('#dashboard .stat-value[data-key="revenue"]');
                if (today) today.textContent = (s.todayAppointments||0);
                if (patients) patients.textContent = (s.totalPatients||0);
                if (revenue) revenue.textContent = '$' + (Number(s.monthlyRevenue||0).toLocaleString());

                const list = document.getElementById('appointmentsList');
                const items = (data.today || []).map(t => {
                    const initials = (t.PatientName||'?').split(' ').map(p=>p[0]||'').join('').substring(0,2).toUpperCase();
                    const statusMap = {0:'Pending',1:'Accepted',2:'Declined'};
                    return `
                        <div class="appointment-item">
                            <div class="appointment-left">
                                <div class="appointment-avatar">${initials}</div>
                                <div class="appointment-info">
                                    <h4>${t.PatientName||'Patient'}</h4>
                                    <p>Today</p>
                                </div>
                            </div>
                            <div class="appointment-right">
                                <div class="appointment-time">${t.TimeValue||''}</div>
                                <span class="badge">${statusMap[t.AppointmentStatus]||'Pending'}</span>
                            </div>
                        </div>
                    `;
                }).join('');
                list.innerHTML = items || '<p class="no-data">No appointments today.</p>';
            })
            .catch(()=>{});

        const addForm = document.getElementById('addScheduleForm');
        addForm.addEventListener('submit', function(event){ addSchedule(event, BASE); });
    });

    // Global variables
    let currentTab = 'dashboard';
    let sidebarCollapsed = false;

    // Tab switching
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName).classList.add('active');

        // Add active class to clicked nav item
        event.target.classList.add('active');

        // Update page title
        const titles = {
            'dashboard': 'Dashboard',
            'appointments': 'Appointments',
            'patients': 'Patients',
            'schedule': 'Schedule',
            'analytics': 'Analytics',
            'settings': 'Settings'
        };
        document.getElementById('pageTitle').textContent = titles[tabName];

        currentTab = tabName;
    }

    // Sidebar toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggleIcon = document.getElementById('toggleIcon');

        sidebarCollapsed = !sidebarCollapsed;

        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            toggleIcon.textContent = '▶';
        } else {
            sidebar.classList.remove('collapsed');
            toggleIcon.textContent = '◀';
        }
    }

    // Modal functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Generate time slots for add schedule modal
        if (modalId === 'addScheduleModal') {
            generateTimeSlots();
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Time slot functions
    function generateTimeSlots() {
        const timeSlotsContainer = document.getElementById('timeSlots');
        const slots = [];

        // Generate 30-minute slots from 10:00 AM to 5:00 PM
        for (let hour = 10; hour < 17; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const time24 = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                const time12 = formatTime12Hour(time24);
                slots.push({
                    time24,
                    time12
                });
            }
        }

        timeSlotsContainer.innerHTML = slots.map(slot =>
            `<button type="button" class="time-slot" data-time="${slot.time24}" onclick="toggleTimeSlot(this)">
                    ${slot.time12}
                </button>`
        ).join('');
    }

    function formatTime12Hour(time24) {
        const [hour, minute] = time24.split(':');
        const hour12 = hour % 12 || 12;
        const ampm = hour < 12 ? 'am' : 'pm';
        return `${hour12}:${minute} ${ampm}`;
    }

    function toggleTimeSlot(button) {
        button.classList.toggle('selected');
    }

    function getSelectedTimeSlots() {
        const selectedSlots = document.querySelectorAll('.time-slot.selected');
        return Array.from(selectedSlots).map(slot => slot.dataset.time);
    }

    // Persist selection (per date) locally so it shows next time opening
    function getLocalSelectedSlots(dateStr) {
        try {
            const all = JSON.parse(localStorage.getItem('doctorSelectedSlots') || '{}');
            return Array.isArray(all[dateStr]) ? all[dateStr] : [];
        } catch (_) { return []; }
    }

    function setLocalSelectedSlots(dateStr, slotsArray) {
        try {
            const all = JSON.parse(localStorage.getItem('doctorSelectedSlots') || '{}');
            all[dateStr] = Array.from(new Set(slotsArray));
            localStorage.setItem('doctorSelectedSlots', JSON.stringify(all));
        } catch (_) {}
    }

    // Schedule management
    function addSchedule(event, BASE) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const selectedSlots = getSelectedTimeSlots();

        if (selectedSlots.length === 0) {
            alert('Please select at least one time slot.');
            return;
        }

        const payload = {
            date: formData.get('scheduleDate'),
            slots: selectedSlots
        };

        const api = (path) => BASE + 'index.php?url=' + path;
        fetch(api('DoctorLayout/apiAddSchedule'), {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        }).then(r => r.json())
          .then(resp => {
            if (resp && resp.success) {
                event.target.reset();
                closeModal('addScheduleModal');
                // Reload schedules
                return fetch(api('DoctorLayout/apiListSchedules')).then(r=>r.json());
            }
            throw new Error('Failed to add schedule');
          })
          .then(data => {
            if (data && Array.isArray(data.schedules)) {
                renderSchedules(data.schedules);
            }
          })
          .catch(() => alert('Error adding schedule'));
    }

    function renderSchedules(schedules) {
        const scheduleList = document.getElementById('scheduleList');
        if (!schedules || schedules.length === 0) {
            scheduleList.innerHTML = '<p class="no-data">No schedules available. Add your first schedule!</p>';
            return;
        }
        // Filter out days with zero available (all slots booked)
        const filtered = schedules.filter(s => (s.slots||[]).some(slot => !slot.isBooked));
        if (filtered.length === 0) {
            scheduleList.innerHTML = '<p class="no-data">No schedules available. Add your first schedule!</p>';
            return;
        }
        scheduleList.innerHTML = filtered.map(schedule => {
                const available = (schedule.slots||[]).filter(s=>!s.isBooked);
                const availableText = available.map(s=>formatTime12Hour(s.time24)).join(', ');
                return `
                <div class="schedule-item">
                    <div class="schedule-info">
                        <h4>${new Date(schedule.date).toLocaleDateString()}</h4>
                        <p>${available.length} slots available</p>
                        <div class="break-info">${availableText}</div>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-outline btn-sm" onclick="viewScheduleSlotsByDate('${schedule.date}')">View</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteScheduleByDate('${schedule.date}')">Delete</button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function viewScheduleSlotsByDate(dateStr) {
        const BASE = (function(){
            const p = window.location.pathname;
            const m1 = p.indexOf('/PHP_CLINIC/Clinic-Booking/');
            if (m1 !== -1) return '/PHP_CLINIC/Clinic-Booking/';
            const m2 = p.indexOf('/Clinic-Booking/');
            if (m2 !== -1) return '/Clinic-Booking/';
            return '/';
        })();
        const api = (path) => BASE + 'index.php?url=' + path;
        fetch(api('DoctorLayout/apiListSchedules')).then(r=>r.json()).then(data => {
            const schedules = data.schedules || [];
            const schedule = schedules.find(s => s.date === dateStr);
            if (!schedule) { alert('No schedule for this date'); return; }
            document.getElementById('detailModalTitle').textContent = 'Schedule on ' + new Date(schedule.date).toLocaleDateString();
            const container = document.getElementById('detailSlots');
            container.innerHTML = '';
            const persisted = getLocalSelectedSlots(schedule.date);
            (schedule.slots || []).forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                const baseClass = 'time-slot' + (slot.isBooked ? ' disabled' : '');
                const isPersistedSelected = !slot.isBooked && persisted.includes(slot.time24);
                btn.className = baseClass + (isPersistedSelected ? ' selected' : '');
                btn.textContent = formatTime12Hour(slot.time24);
                btn.dataset.time = slot.time24;
                if (slot.isBooked) {
                    btn.disabled = true;
                } else {
                    btn.addEventListener('click', function(){
                        btn.classList.toggle('selected');
                        const nowSelected = Array.from(container.querySelectorAll('.time-slot.selected')).map(b=>b.dataset.time);
                        setLocalSelectedSlots(schedule.date, nowSelected);
                    });
                }
                container.appendChild(btn);
            });
            openModal('scheduleDetailModal');
        });
    }

    function editSchedule(scheduleId) {
        alert('Edit functionality coming soon!');
    }

    function deleteScheduleByDate(dateStr) {
        const BASE = (function(){
            const p = window.location.pathname;
            const m1 = p.indexOf('/PHP_CLINIC/Clinic-Booking/');
            if (m1 !== -1) return '/PHP_CLINIC/Clinic-Booking/';
            const m2 = p.indexOf('/Clinic-Booking/');
            if (m2 !== -1) return '/Clinic-Booking/';
            return '/';
        })();
        const api = (path) => BASE + 'index.php?url=' + path;
        if (!confirm('Are you sure you want to delete this schedule?')) return;
        fetch(api('DoctorLayout/apiDeleteSchedule'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ date: dateStr })
        }).then(r => r.json())
          .then(resp => {
            if (resp && resp.success) {
                return fetch(api('DoctorLayout/apiListSchedules')).then(r=>r.json());
            }
            throw new Error('Delete failed');
          })
          .then(data => {
            renderSchedules(data.schedules || []);
          })
          .catch(() => alert('Error deleting schedule'));
    }

    function renderAppointments(appts){
        const el = document.getElementById('appointmentsContainer');
        if (!appts || appts.length === 0) {
            el.innerHTML = '<p class="no-data">No appointments.</p>';
            return;
        }
        el.innerHTML = appts.map(a => {
            const dateText = new Date(a.DateOfSchedule).toLocaleDateString();
            const statusMap = {0: 'Pending', 1: 'Accepted', 2: 'Declined'};
            const status = statusMap[a.AppointmentStatus] ?? 'Unknown';
            const canAct = a.AppointmentStatus == 0;
            return `
                <div class="appointment-item">
                    <div class="appointment-left">
                        <div class="appointment-avatar">${(a.PatientName||'?').split(' ').map(p=>p[0]||'').join('').substring(0,2).toUpperCase()}</div>
                        <div class="appointment-info">
                            <h4>${a.PatientName || 'Patient'}</h4>
                            <p>${dateText} • ${a.TimeValue || ''}</p>
                            <p class="badge">${status}</p>
                        </div>
                    </div>
                    <div class="appointment-right">
                        ${canAct ? `
                        <button class="btn btn-primary btn-sm" onclick="updateApptStatus(${a.AppointmentID},1)">Accept</button>
                        <button class="btn btn-danger btn-sm" onclick="updateApptStatus(${a.AppointmentID},2)">Decline</button>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    function updateApptStatus(id, status){
        const BASE = (function(){
            const p = window.location.pathname;
            const m1 = p.indexOf('/PHP_CLINIC/Clinic-Booking/');
            if (m1 !== -1) return '/PHP_CLINIC/Clinic-Booking/';
            const m2 = p.indexOf('/Clinic-Booking/');
            if (m2 !== -1) return '/Clinic-Booking/';
            return '/';
        })();
        const api = (path) => BASE + 'index.php?url=' + path;
        fetch(api('DoctorLayout/apiUpdateAppointmentStatus'), {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ appointmentId: id, status })
        }).then(r=>r.json()).then(resp => {
            if (resp && resp.success) {
                return fetch(api('DoctorLayout/apiListAppointments')).then(r=>r.json());
            }
            throw new Error('Update failed');
        }).then(data => {
            renderAppointments(data.appointments || []);
            // Also refresh schedules so accepted slots disappear for patients
            const BASE = (function(){
                const p = window.location.pathname; if (p.indexOf('/PHP_CLINIC/Clinic-Booking/')!==-1) return '/PHP_CLINIC/Clinic-Booking/'; if (p.indexOf('/Clinic-Booking/')!==-1) return '/Clinic-Booking/'; return '/';
            })();
            const api = (path) => BASE + 'index.php?url=' + path;
            fetch(api('DoctorLayout/apiListSchedules')).then(r=>r.json()).then(d=>{ renderSchedules(d.schedules||[]); }).catch(()=>{});
        }).catch(()=> alert('Error updating status'));
    }
    </script>
</body>

</html>