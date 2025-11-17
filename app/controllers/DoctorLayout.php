<?php 


    class DoctorLayout extends Controller{
        public $AppoinmentModel;
        function __construct()
        {
            $this->AppoinmentModel = $this->model('AppointmentModel');;
        }

        function show(){
            $this->ProfileDoctor();
        }

        function ProfileDoctor(){
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); // Khởi động session nếu chưa bắt đầu
            }

            // Kiểm tra xem người dùng đã đăng nhập chưa
            if (!isset($_SESSION['user'])) {
                header('Location: ./app/controllers/SignIn.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
                exit();
            }

        // Lấy thông tin người dùng từ session
            $doctor = $_SESSION['user'];
            $this->view('DoctorLayout',[
                'Page'=> 'ProfileDoctor',
                'Doctor' => $doctor
            ]);
        }

        function Appointment(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start(); // Khởi động session nếu chưa bắt đầu
        }

        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['user'])) {
            header('Location: ./app/controllers/SignIn.php'); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
            exit();
        }

        // Lấy thông tin người dùng từ session
        $doctor = $_SESSION['user'];

        // Kiểm tra 
        $doctorId = $doctor['UserID'];
            $patients = $this->AppoinmentModel->listDoctorAppointments((int)$doctorId);

        $this->view('DoctorLayout', [
            'Page' => 'Appointment',
            'Patients' => $patients
        ]);
        }
        function MySchedule(){
            $timeTypes = $this->AppoinmentModel->getAllTime();
            $this->view('DoctorLayout',[
                'Page'=> 'MySchedule',
                'TimeType'=> $timeTypes
            ]);
        }


        function AddAppointment(){
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); // Khởi động session nếu chưa bắt đầu
            }
            if (!isset($_SESSION['user'])) {
                header('Location: ./app/controllers/SignIn.php'); // Chuyển hướng nếu chưa đăng nhập
                exit();
            }
            $doctor = $_SESSION['user'];
            $doctorId = $doctor['UserID'];
            $timeTypes = $this->AppoinmentModel->getAllTime();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Lấy dữ liệu từ form
                $patientName = $_POST['name-patient'];
                $appointmentDate = $_POST['date'];
                $timeType = $_POST['time_type']; // Lấy TimeType từ form
                var_dump($timeType);

                if ($this->AppoinmentModel->createAppointment($doctorId, $patientName, $appointmentDate, $timeType)) {
                    header('Location: ./app/controllers/DoctorLayout.php?method=Appointment');
                    exit();
                } else {
                    echo "Error in scheduling appointment.";
                }
            }
            $this->view('DoctorLayout',[
                'Page' => 'AddAppointment',
                'TimeType' => $timeTypes

            ]);
        }

		// ===== JSON APIs for Schedule Management =====
		function apiListSchedules(){
			header('Content-Type: application/json');
			if (session_status() === PHP_SESSION_NONE) { session_start(); }
			if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
			$doctor = $_SESSION['user'];
			$doctorUserId = $doctor['UserID'];
			$data = $this->AppoinmentModel->listSchedulesByDoctorUser((int)$doctorUserId);
			echo json_encode(['schedules'=>$data]);
		}

		function apiAddSchedule(){
			header('Content-Type: application/json');
			if (session_status() === PHP_SESSION_NONE) { session_start(); }
			if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
			$input = json_decode(file_get_contents('php://input'), true);
			$date = $input['date'] ?? '';
			$slots = $input['slots'] ?? [];
			if (!$date || !is_array($slots)) { http_response_code(400); echo json_encode(['error'=>'invalid_payload']); return; }
			$doctorUserId = (int)$_SESSION['user']['UserID'];
			$ok = $this->AppoinmentModel->addScheduleByDoctorUser($doctorUserId, $date, $slots);
			echo json_encode(['success'=>$ok]);
		}

		function apiDeleteSchedule(){
			header('Content-Type: application/json');
			if (session_status() === PHP_SESSION_NONE) { session_start(); }
			if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
			$input = json_decode(file_get_contents('php://input'), true);
			$date = $input['date'] ?? '';
			if (!$date) { http_response_code(400); echo json_encode(['error'=>'invalid_payload']); return; }
			$doctorUserId = (int)$_SESSION['user']['UserID'];
			$ok = $this->AppoinmentModel->deleteScheduleByDoctorUserAndDate($doctorUserId, $date);
			echo json_encode(['success'=>$ok]);
		}

        // ===== Appointments APIs =====
        function apiListAppointments(){
            header('Content-Type: application/json');
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
            $doctorUserId = (int)$_SESSION['user']['UserID'];
            $appts = $this->AppoinmentModel->listDoctorAppointments($doctorUserId);
            echo json_encode(['appointments'=>$appts]);
        }

        function apiUpdateAppointmentStatus(){
            header('Content-Type: application/json');
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
            $input = json_decode(file_get_contents('php://input'), true);
            $appointmentId = isset($input['appointmentId']) ? (int)$input['appointmentId'] : 0;
            $status = isset($input['status']) ? (int)$input['status'] : -1; // 1 accept, 2 decline
            if ($appointmentId <= 0 || ($status !== 1 && $status !== 2)) { http_response_code(400); echo json_encode(['error'=>'invalid_payload']); return; }
            $ok = $this->AppoinmentModel->updateAppointmentStatus($appointmentId, $status);

            if ($ok) {
                // Prepare and send email to patient using signup-like template
                require_once __DIR__ . '/../libs/SendEmailAppointmentStatus.php';
                $info = $this->AppoinmentModel->getAppointmentEmailInfo($appointmentId);
                if ($info) {
                    $email = $info['PatientEmail'];
                    $patientName = $info['PatientName'];
                    $doctorName = $info['DoctorName'];
                    $date = $info['DateOfSchedule'];
                    $time = $info['TimeValue'];
                    $statusText = $status === 1 ? 'accepted' : 'declined';
                    $mailer = new SendEmailAppointmentStatus();
                    $mailer->Send($email, $patientName, $doctorName, $date, $time, $statusText);
                }
            }

            echo json_encode(['success'=>$ok]);
        }

        // ===== Dashboard stats API =====
        function apiDashboardStats(){
            header('Content-Type: application/json');
            if (session_status() === PHP_SESSION_NONE) { session_start(); }
            if (!isset($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'unauthorized']); return; }
            $doctorUserId = (int)$_SESSION['user']['UserID'];
            $stats = $this->AppoinmentModel->getDashboardStats($doctorUserId);
            $todayList = $this->AppoinmentModel->listTodaysAppointments($doctorUserId);
            echo json_encode(['stats'=>$stats, 'today'=>$todayList]);
        }
	}