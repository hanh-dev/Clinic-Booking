<?php
require_once './app/controllers/ProfileDoctor.php'; // Đảm bảo đường dẫn chính xác
require_once __DIR__ . '/../libs/SendEmailForDoctor.php';
require_once 'app/controllers/LogOut.php'; 
class Admin extends Controller
{
    public $UserModel;
    public $DoctorModel;
    public $AppointmentModel;

    function __construct()
    {
        $this->UserModel = $this->model("UserModel");
        $this->DoctorModel = $this->model("DoctorModel");
        $this->AppointmentModel = $this->model("AppointmentModel");
    }

    function show()
    {
        $this->dashboard();
    }

    function dashboard() {
        // Get statistics
        $totalUsers = $this->UserModel->getTotalUsersCount();
        $totalDoctors = $this->DoctorModel->getTotalDoctorsCount();
        $totalAppointments = $this->AppointmentModel->getTotalAppointmentsCount();
        $todayAppointments = $this->AppointmentModel->getTodayAppointmentsCount();
        
        $this->view("Admin", [
            "Page" => "Dashboard",
            "stats" => [
                "totalUsers" => $totalUsers,
                "totalDoctors" => $totalDoctors,
                "totalAppointments" => $totalAppointments,
                "todayAppointments" => $todayAppointments
            ]
        ]);
    }

    function appointmentSchedule() {
        $appointmentList = $this->AppointmentModel->getAllAppointmentsForAdmin();
        $appointmentArr = [];
        while ($appointmentResult = mysqli_fetch_assoc($appointmentList)) {
            $appointmentArr[] = $appointmentResult;
        };
        $this->view("Admin", [
            "Page" => "AppointmentSchedule",
            "appointments" => $appointmentArr
        ]);
    }

    function userManagement() {
        $Uselist = $this->UserModel->getUserInforforAdmin();
        $userArr = [];
        while ($UserListResult = mysqli_fetch_assoc($Uselist)) {
            $userArr[] = $UserListResult;
        };
        $this->view("Admin", [
            "Page" => "UserManagement",
            "user" => $userArr
        ]); 
    }

    function doctorManagement() {
        $Uselist = $this->DoctorModel->getDoctorInforforAdmin();
        $doctorArr = [];
        while ($UserListResult = mysqli_fetch_assoc($Uselist)) {
            $doctorArr[] = $UserListResult;
        };
        $this->view("Admin", [
            "Page" => "DoctorManagement" ,
            "doctor" => $doctorArr
        ]);    
    }

    function addDoctor() {
            $specialist = $this->DoctorModel->getAllSpecialtiesAdmin();
            $specialistArr = [];
            while ($specialistResult = mysqli_fetch_assoc($specialist)) {
                $specialistArr[] = $specialistResult;
            };
            $this->view("addDoctorAdmin", [
                "specialist" =>  $specialistArr,
            ]); 

        if (isset($_POST['addDoctorButton'])) {
            $fullName = $_POST["fullName"];
            $gender = $_POST["gender"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $birth = $_POST["birth"];
            $phoneNumber = $_POST["phoneNumber"];
            $address = $_POST["address"];
            $specialtyID = $_POST["specialty"];
            $fees = $_POST["fees"];


            $Uselist = $this->UserModel->getUserInforforAdmin();
            $checkInfor = false;
            while ($row = mysqli_fetch_assoc($Uselist)) {
                if ($row['Email'] == $email && $row['PhoneNumber'] == $phoneNumber) {
                    $checkInfor = true;
                    break;
                }
            }

            if ($checkInfor == false){
                $this->UserModel->InsertNewDoctor($fullName, $email, $password, $birth, $gender, $phoneNumber, $address);
                $result = $this->UserModel->GetUserInforLogIn($email);
                $row = mysqli_fetch_assoc($result);
                $userID =  $row['UserID'];
                $this->DoctorModel->insertDoctor($specialtyID, $fees, $userID);
                // Gửi email chào mừng
                $sendEmail = new SendEmailForDoctor();
                $sendEmail->SendEmail($email, $fullName, $password);
                $this->view("addDoctorAdmin", [
                    "success" => true,
                    "message" => "Doctor added successfully",
                    "page" => "DoctorManagement"
                ]);
                return;
            }
            else {
                $this->view("addDoctorAdmin", [
                    "success" => false,
                    "message" => "Email or phone number already exists, please use a different email or phone number."
                ]);
                return;
            }

        }
    }

    // User Management Actions
    function toggleUserLock() {
        if (isset($_POST['userID']) && isset($_POST['status'])) {
            $userID = $_POST['userID'];
            $status = $_POST['status'];
            $result = $this->UserModel->toggleUserLockStatus($userID, $status);
            if ($result) {
                echo json_encode(['success' => true, 'message' => $status == 1 ? 'Account locked successfully' : 'Account unlocked successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update account status']);
            }
        }
    }

    function resetUserPassword() {
        if (isset($_POST['userID'])) {
            $userID = $_POST['userID'];
            $result = $this->UserModel->resetUserPassword($userID);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Password reset successfully. New password: 12345678']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
            }
        }
    }

    // Doctor Management Actions
    function toggleDoctorLock() {
        if (isset($_POST['userID']) && isset($_POST['status'])) {
            $userID = $_POST['userID'];
            $status = $_POST['status'];
            $result = $this->DoctorModel->toggleDoctorLockStatus($userID, $status);
            if ($result) {
                echo json_encode(['success' => true, 'message' => $status == 1 ? 'Account locked successfully' : 'Account unlocked successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update account status']);
            }
        }
    }

    function updateDoctor() {
        if (isset($_POST['updateDoctorButton'])) {
            $doctorID = $_POST['doctorID'];
            $userID = $_POST['userID'];
            $fullName = $_POST['fullName'];
            $gender = $_POST['gender'];
            $birth = $_POST['birth'];
            $phoneNumber = $_POST['phoneNumber'];
            $address = $_POST['address'];
            $specialtyID = $_POST['specialty'];
            $fees = $_POST['fees'];

            // Update user profile
            $result1 = $this->DoctorModel->updateDoctorProfile($userID, $fullName, $gender, $birth, $phoneNumber, $address);
            // Update specialty
            $result2 = $this->DoctorModel->updateDoctorSpecialty($doctorID, $specialtyID);
            // Update fees
            $result3 = $this->DoctorModel->updateDoctorFees($doctorID, $fees);

            if ($result1 && $result2 && $result3) {
                header("Location: ./Admin/doctorManagement?success=1&message=Doctor+updated+successfully");
                exit;
            } else {
                header("Location: ./Admin/updateDoctor?id=" . $doctorID . "&error=1&message=Failed+to+update+doctor");
                exit;
            }
        } else {
            // Show edit form
            if (isset($_GET['id'])) {
                $doctorID = $_GET['id'];
                $doctorList = $this->DoctorModel->getDoctorInforforAdmin();
                $doctor = null;
                while ($row = mysqli_fetch_assoc($doctorList)) {
                    if ($row['DoctorID'] == $doctorID) {
                        $doctor = $row;
                        break;
                    }
                }
                if ($doctor) {
                    $specialist = $this->DoctorModel->getAllSpecialtiesAdmin();
                    $specialistArr = [];
                    while ($specialistResult = mysqli_fetch_assoc($specialist)) {
                        $specialistArr[] = $specialistResult;
                    }
                    $this->view("editDoctorAdmin", [
                        "doctor" => $doctor,
                        "specialist" => $specialistArr
                    ]);
                } else {
                    header("Location: ./Admin/doctorManagement?error=1&message=Doctor+not+found");
                    exit;
                }
            }
        }
    }

    function logOutAdmin() {
        $isLogOut = new LogOut();
        $isLogOut->LogOutAccount();
        if ( $isLogOut) {
            $this->view('master', [
                'Page' => 'MyAccount',
            ]); 
        }
        else {
            $this->userManagement();
        }
    }

    function updateDoctorAjax() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
            $id = $_POST["id"];
            $name = $_POST["name"];
            $email = $_POST["email"];
            $phone = $_POST["phone"];
            $specialty = $_POST["specialty"];
            $fees = $_POST["fees"];
    
            $doctorModel = $this->model("DoctorModel");
    
            $result = $doctorModel->updateDoctor($id, $name, $email, $phone, $specialty, $fees);
    
            if ($result) {
                echo json_encode(["success" => true, "message" => "Doctor updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Update failed"]);
            }
        }
    }
    
    
}
