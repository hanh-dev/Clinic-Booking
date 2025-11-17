<!DOCTYPE html>
<html lang="en">
<head>
    <base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Schedule</title>
    <link rel="stylesheet" href="public/css/AppointmentSchedule.css">
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
                            <p class="tbl-title-admin">Appointment <span>Schedule</span></p>
                            <?php
                            if (!empty($data['appointments'])) {
                                echo '<table id="appointmentTable" class="tbl-admin table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Patient Name</th>
                                                <th>Patient Email</th>
                                                <th>Patient Phone</th>
                                                <th>Doctor Name</th>
                                                <th>Specialty</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Status</th>
                                                <th>Payment</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                $counter = 1;
                                for ($i = 0; $i < count($data['appointments']); $i++) {
                                    $statusClass = '';
                                    $status = $data['appointments'][$i]['Status'];
                                    if ($status == 'Pending') {
                                        $statusClass = 'badge-warning';
                                    } elseif ($status == 'Accepted') {
                                        $statusClass = 'badge-success';
                                    } elseif ($status == 'Declined') {
                                        $statusClass = 'badge-danger';
                                    }
                                    
                                    $paymentClass = $data['appointments'][$i]['PaymentStatus'] == 'Paid' ? 'badge-success' : 'badge-secondary';
                                    
                                    echo '<tr>
                                            <td>' . $counter++ . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['PatientName']) . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['PatientEmail']) . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['PatientPhone'] ?? 'N/A') . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['DoctorName']) . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['Specialty']) . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['AppointmentDate']) . '</td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['AppointmentTime']) . '</td>
                                            <td><span class="badge ' . $statusClass . '">' . htmlspecialchars($status) . '</span></td>
                                            <td><span class="badge ' . $paymentClass . '">' . htmlspecialchars($data['appointments'][$i]['PaymentStatus']) . '</span></td>
                                            <td>' . htmlspecialchars($data['appointments'][$i]['Description'] ?? 'N/A') . '</td>
                                        </tr>';
                                }
                                echo '</tbody></table>';
                            } else {
                                echo "<div class='nondata'><i class='fa-regular fa-face-frown-open'></i>No appointments available!</div>";
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
            $('#appointmentTable').DataTable({
                "pageLength": 10,
                "order": [[6, "desc"]], // Sort by date descending
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries to show",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                }
            });
        });
    </script>
</body>
</html>

