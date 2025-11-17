<!DOCTYPE html>
<html lang="en">
<head>
<base href="/PHP_CLINIC/Clinic-Booking/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/addDoctorAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>
    <title>Edit Doctor</title>
</head>
<body>
    <div class="container-fluid">
        <div class="content-AD">
            <div class="icon-back">
                <a href="./Admin/doctorManagement">
                    <button> <i class="fa-solid fa-arrow-left"></i></button>
                </a>
            </div>
            <div class="body-content-AD">
                <div class="bd-form-AD">
                    <div class="logo-AD">
                        <img src="public/images/logoSU.png" alt="">
                    </div>
                    <div class="bd-form-content-AD">
                        <form class="form-content-AD" action="./Admin/updateDoctor" method="POST" onsubmit="return validateForm()">
                            <p class="text-title-form-AD">
                                Edit <span>Doctor</span> Profile
                            </p>
                            <?php 
                            if (isset($_GET['error']) && $_GET['error'] == 1 && isset($_GET['message'])): 
                            ?>
                                <div id="errorAlert" class="alert alert-danger text-center" role="alert">
                                    <?php echo urldecode($_GET['message']); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($data["message"])): ?>
                                <?php if (isset($data["success"]) && $data["success"]): ?>
                                    <div class="alert alert-success text-center" role="alert">
                                        <?php echo $data["message"]; ?>
                                    </div>
                                <?php else: ?>
                                    <div id="errorAlert" class="alert alert-danger text-center" role="alert">
                                        <?php echo $data["message"]; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (isset($data['doctor']) && $data['doctor']): ?>
                                <input type="hidden" name="doctorID" value="<?php echo htmlspecialchars($data['doctor']['DoctorID']); ?>">
                                <input type="hidden" name="userID" value="<?php echo htmlspecialchars($data['doctor']['UserID']); ?>">
                                <div class="input-content-AD">
                                    <div class="inputInforDoctor">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="fullName" placeholder="Doctor's full name" 
                                                value="<?php echo htmlspecialchars($data['doctor']['FullName']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" name="gender" required>
                                                <option value="" disabled>Doctor's gender</option>
                                                <option value="male" <?php echo ($data['doctor']['Gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="female" <?php echo ($data['doctor']['Gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                                <option value="other" <?php echo ($data['doctor']['Gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="date" class="form-control" name="birth" placeholder="Doctor's birth date" 
                                            value="<?php echo htmlspecialchars($data['doctor']['DOB'] ?? ''); ?>" required>
                                    </div>
                                    <div class="inputInforDoctor">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="phoneNumber" placeholder="Doctor's phone number" 
                                                value="<?php echo htmlspecialchars($data['doctor']['PhoneNumber'] ?? ''); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="address" placeholder="Doctor's address" 
                                                value="<?php echo htmlspecialchars($data['doctor']['Address'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="inputInforDoctor">
                                        <div class="form-group">
                                            <select class="form-control" name="specialty" required>
                                                <option value="" disabled>Doctor's specialty</option>
                                                <?php
                                                for ($i = 0; $i < count($data['specialist']); $i++) {
                                                    $specialty = $data['specialist'][$i];
                                                    $selected = ($specialty['SPID'] == $data['doctor']['SPID']) ? 'selected' : '';
                                                    echo '<option value="' . $specialty['SPID'] . '" ' . $selected . '>' . $specialty['SPName'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control" name="fees" placeholder="Doctor's consultation fee" 
                                                value="<?php echo htmlspecialchars($data['doctor']['DoctorFees'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <p>
                                        <button type="submit" id="submitButton" name="updateDoctorButton" class="btn btn-info">Update Doctor</button>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">Doctor not found!</div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

