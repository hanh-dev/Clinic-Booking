<!DOCTYPE html>
<html lang="en">
<head>
<base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="public/css/UserManagement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="content-admin">
            <div class="body-content-admin">
                <div class="content-body-admin">
                    <div class="table-display-admin">
                        <div class="tbl-content-admin">
                            <p class="tbl-title-admin">User <span>Management</span></p>
                            <?php
                            if (!empty($data['user'])) {
                                echo '<table id="userTable" class="tbl-admin table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                $counter = 1;
                                for ($i = 0; $i < count($data['user']); $i++) {
                                    $status = isset($data['user'][$i]['AccountStatus']) ? (int)$data['user'][$i]['AccountStatus'] : 0;
                                    $statusBadge = $status == 1 ? '<span class="badge badge-danger">Locked</span>' : '<span class="badge badge-success">Active</span>';
                                    $lockBtn = $status == 1 
                                        ? '<button class="btn-action btn-unlock" onclick="toggleLock(' . $data['user'][$i]['UserID'] . ', 0)" title="Unlock Account"><i class="fa-solid fa-unlock"></i></button>'
                                        : '<button class="btn-action btn-lock" onclick="toggleLock(' . $data['user'][$i]['UserID'] . ', 1)" title="Lock Account"><i class="fa-solid fa-lock"></i></button>';
                                    
                                    echo '<tr>
                                            <td>' . $counter++ . '</td>
                                            <td>' . htmlspecialchars($data['user'][$i]['FullName']) . '</td>
                                            <td>' . htmlspecialchars($data['user'][$i]['Email']) . '</td>
                                            <td>' . htmlspecialchars($data['user'][$i]['PhoneNumber'] ?? 'N/A') . '</td>
                                            <td>' . $statusBadge . '</td>
                                            <td>
                                                <div class="action-buttons">
                                                    ' . $lockBtn . '
                                                    <button class="btn-action btn-reset" onclick="resetPassword(' . $data['user'][$i]['UserID'] . ')" title="Reset Password"><i class="fa-solid fa-key"></i></button>
                                                </div>
                                            </td>
                                        </tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo "<div class='nondata'><i class='fa-regular fa-face-frown-open'></i>No data available!</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [
                    { "orderable": false, "targets": 5 } // Disable sorting on Actions column
                ],
                "language": {
                    "search": "Search by name/email/phone:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries to show",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });
        });

        function toggleLock(userID, status) {
            if (confirm(status == 1 ? 'Are you sure you want to lock this account?' : 'Are you sure you want to unlock this account?')) {
                $.ajax({
                    url: './Admin/toggleUserLock',
                    method: 'POST',
                    data: { userID: userID, status: status },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        }

        function resetPassword(userID) {
            if (confirm('Are you sure you want to reset this user\'s password? The new password will be: 12345678')) {
                $.ajax({
                    url: './Admin/resetUserPassword',
                    method: 'POST',
                    data: { userID: userID },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            }
        }
    </script>
</body>
</html>