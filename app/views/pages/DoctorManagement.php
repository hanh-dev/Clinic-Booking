<!DOCTYPE html>
<html lang="en">
<head>
<base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Management</title>

    <link rel="stylesheet" href="public/css/DoctorManagement.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="content-admin">
            <div class="body-content-admin">
                <div class="content-body-admin">
                    <div class="table-display-admin">
                        <div class="tbl-content-admin">

                            <div class="flex-title-button">
                                <p class="tbl-title-admin">Doctor <span>Management</span></p>

                                <a href="./Admin/addDoctor" class="btn-add-doctor">
                                    <i class="fa-solid fa-user-plus"></i> Add Doctor
                                </a>
                            </div>

                            <?php if (!empty($data['doctor'])): ?>
                                <table id="doctorTable" class="tbl-admin table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Specialty</th>
                                            <th>Fees</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $counter = 1; ?>
                                        <?php foreach ($data["doctor"] as $doc): ?>

                                            <?php 
                                                $status = (int)$doc["AccountStatus"];
                                                $statusBadge = $status == 1 
                                                    ? '<span class="badge badge-danger">Locked</span>'
                                                    : '<span class="badge badge-success">Active</span>';

                                                $lockBtn = $status == 1 
                                                    ? '<button class="btn-action btn-unlock" onclick="toggleLock('.$doc['UserID'].', 0)"><i class="fa-solid fa-unlock"></i></button>'
                                                    : '<button class="btn-action btn-lock" onclick="toggleLock('.$doc['UserID'].', 1)"><i class="fa-solid fa-lock"></i></button>';
                                            ?>

                                            <tr>
                                                <td><?= $counter++ ?></td>
                                                <td><?= htmlspecialchars($doc['FullName']) ?></td>
                                                <td><?= htmlspecialchars($doc['Email']) ?></td>
                                                <td><?= htmlspecialchars($doc['PhoneNumber'] ?? 'N/A') ?></td>
                                                <td><span class="badge badge-primary"><?= htmlspecialchars($doc['Specialty']) ?></span></td>
                                                <td>$<?= number_format($doc['DoctorFees'] ?? 0, 2) ?></td>
                                                <td><?= $statusBadge ?></td>

                                                <td>
                                                    <div class="action-buttons">

                                                        <?= $lockBtn ?>

                                                        <a href="javascript:void(0)"
                                                            class="btn-action btn-edit"
                                                            onclick='openEditDoctorModal(<?= json_encode($doc) ?>)'>
                                                            <i class="fa-solid fa-edit"></i>
                                                        </a>

                                                    </div>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                            <?php else: ?>

                                <div class='nondata'>
                                    <i class='fa-regular fa-face-frown-open'></i>No data available!
                                </div>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ------------ EDIT POPUP MODAL -------------- -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-header">
            <h5>Edit Doctor</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <form id="editDoctorForm">
              <input type="hidden" id="doctorId">

              <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="doctorName" class="form-control">
              </div>

              <div class="form-group">
                <label>Email</label>
                <input type="email" id="doctorEmail" class="form-control">
              </div>

              <div class="form-group">
                <label>Phone</label>
                <input type="text" id="doctorPhone" class="form-control">
              </div>

              <div class="form-group">
                <label>Specialty</label>
                <input type="text" id="doctorSpecialty" class="form-control">
              </div>

              <div class="form-group">
                <label>Fees</label>
                <input type="number" id="doctorFees" min="0" class="form-control">
              </div>

            </form>
          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button class="btn btn-primary" onclick="submitEditDoctor()">Save Changes</button>
          </div>

        </div>
      </div>
    </div>



    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#doctorTable').DataTable({
                "pageLength": 10,
                "order": [[0, "asc"]],
                "columnDefs": [{ "orderable": false, "targets": 7 }],
            });
        });

        function openEditDoctorModal(doc) {
            $("#doctorId").val(doc.DoctorID);
            $("#doctorName").val(doc.FullName);
            $("#doctorEmail").val(doc.Email);
            $("#doctorPhone").val(doc.PhoneNumber);
            $("#doctorSpecialty").val(doc.Specialty);
            $("#doctorFees").val(doc.DoctorFees);

            $("#editDoctorModal").modal("show");
        }

        function submitEditDoctor() {
            $.ajax({
                url: './Admin/updateDoctorAjax',
                type: 'POST',
                data: {
                    id: $("#doctorId").val(),
                    name: $("#doctorName").val(),
                    email: $("#doctorEmail").val(),
                    phone: $("#doctorPhone").val(),
                    specialty: $("#doctorSpecialty").val(),
                    fees: $("#doctorFees").val(),
                },
                dataType: 'json',

                success: function(res) {
                    alert(res.message);
                    if (res.success) location.reload();
                },
                error: function() {
                    alert("Update failed. Please try again.");
                }
            });
        }

        function toggleLock(userID, status) {
            if (!confirm(status === 1 ? "Lock this account?" : "Unlock this account?")) return;

            $.ajax({
                url: './Admin/toggleDoctorLock',
                type: 'POST',
                data: { userID, status },
                dataType: 'json',
                success: function(resp) {
                    alert(resp.message);
                    if (resp.success) location.reload();
                }
            });
        }
    </script>

</body>
</html>
