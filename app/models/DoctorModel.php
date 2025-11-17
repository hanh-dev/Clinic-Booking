<?php

use Stripe\Billing\Alert;

    class DoctorModel extends DB {

        // Lấy danh sách bác sĩ
        public function GetDoctor() {
            $doctorList = [];
            $sql = "SELECT * FROM doctor";
            $result = $this->con->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $doctorList[] = $row;
                }
            }
            return $doctorList;
        }

        // Lấy danh sách chuyên khoa
        public function Specialities() {
            $docSpeList = [];
            $sql = "SELECT * FROM specilist";
            $result = $this->con->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $docSpeList[] = $row;
                }
            }
            return $docSpeList;
        }

        // Lấy tất cả bác sĩ với thông tin người dùng và chuyên khoa
        public function getAllDoctor() {
            $docList = [];
            $sql = "SELECT * 
                    FROM User u
                    INNER JOIN Doctor d ON d.UserID = u.UserID
                    INNER JOIN Specilist s ON s.SPID = d.SPID";
            $result = $this->con->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $docList[] = $row;
                }
            }
            return $docList;
        }

        // Lấy danh sách bác sĩ theo chuyên khoa
        public function getDoctorSpecilist($specilistdetail) {
            $docList = [];
            $sql = "SELECT * 
                    FROM User u
                    INNER JOIN Doctor d ON d.UserID = u.UserID
                    INNER JOIN Specilist s ON s.SPID = d.SPID
                    WHERE s.SPName = '$specilistdetail'";
            $result = $this->con->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $docList[] = $row;
                }
            }
            return $docList;
        }

        // Lấy thông tin chi tiết bác sĩ theo ID
        public function getDoctorDetail($id) {
            $sql = "SELECT * 
                    FROM User u
                    INNER JOIN Doctor d ON d.UserID = u.UserID
                    INNER JOIN Specilist s ON s.SPID = d.SPID
                    WHERE d.DoctorID = '$id'";
            $result = $this->con->query($sql);
            if ($result) {
                if ($row = $result->fetch_assoc()) {
                    return $row;
                }
            }
            return null;
        }

        public function getDoctorRelate($id) {
            $doctorList = [];
        
            // Lấy SPName của bác sĩ hiện tại
            $sql_specilist = "SELECT s.SPName FROM Specilist s
                              INNER JOIN Doctor d ON d.SPID = s.SPID
                              WHERE d.DoctorID = ?";
            $stmt = $this->con->prepare($sql_specilist);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result_specilist = $stmt->get_result();
        
            if ($result_specilist->num_rows > 0) {
                $row_specilist = $result_specilist->fetch_assoc();
                $specilist = $row_specilist['SPName'];
        
                // Lấy danh sách bác sĩ liên quan
                $sql = "SELECT * FROM User u
                INNER JOIN Doctor d ON d.UserID = u.UserID
                INNER JOIN Specilist s ON s.SPID = d.SPID
                WHERE DoctorID != ? AND s.SPName = ?";
                $stmt = $this->con->prepare($sql);
                $stmt->bind_param('is', $id, $specilist);
                $stmt->execute();
                $result = $stmt->get_result();
        
                while ($row = $result->fetch_assoc()) {
                    $doctorList[] = $row;
                }
            }
            $stmt->close();
            return $doctorList;
        }
        // $slots = [
    // '2024-12-05' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
    // '2024-12-06' => ['08:00', '09:00', '11:00', '13:00'],
    // '2024-12-07' => ['10:00', '12:00', '14:00'],
        
        // Lấy timevalue
        public function getTimeValue($id) {
            $timevalue = [];

            $sql = "SELECT TimeValue FROM TimeType t
                    INNER JOIN Schedule s ON t.TTID = s.TTID
                    WHERE s.DoctorID = ?";
            
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("i", $id); 
            $stmt->execute();

            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $timevalue[] = strtolower($row['TimeValue']);
            }
            $stmt->close();
            return $timevalue;
        }

        public function getIsBooked($id) {
            $timevalue = [];
            $sql = "SELECT TimeValue FROM TimeType t
                    INNER JOIN Schedule s ON s.TTID = t.TTID
                    WHERE s.DoctorID = ? AND s.IsBooked = 0";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                $timevalue [] = strtolower($row['TimeValue']);
            }

            $stmt->close();
            return $timevalue;
        }

        public function getDays() {
            $days = [];
            $currentDay = new DateTime();
            for($i = 0; $i < 7; $i++) {
                $day = clone $currentDay;
                $day->modify("+$i day");
                $days[] = $day->format('Y-m-d');
            }
            return $days;
        }

        //Get days of schedule
        public function getDayOfSchedule($id) {
            $days = [];
            $sql = "SELECT DateOfSchedule FROM Schedule
            WHERE DoctorID = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            while($row = $result->fetch_assoc()) {
                if(!in_array($row['DateOfSchedule'], $days)) {
                    $days[] = $row['DateOfSchedule'];
                }
            }
            return $days;
        }

        public function getSlots($id) {
            $slots = [];
            $days = $this->getDayOfSchedule($id);
            for($i = 0; $i < count($days); $i++) {
                $day = $days[$i];
                $timeSlots = $this->getTimeSlots($id,$day);
                if(!empty($timeSlots)) {
                    $slots[$day] = $timeSlots;
                }

            }

            return $slots;
        }

        public function getTimeSlots($id, $day) {
            $timeSlots = [];
            $sql = "SELECT TimeValue FROM TimeType t
                    INNER JOIN Schedule s ON s.TTID = t.TTID
                    WHERE s.DoctorID = ? AND s.DateOfSchedule = ? AND s.IsBooked = 0";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("is", $id, $day); 
            $stmt->execute();
            // Lấy kết quả
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $timeSlots[] = strtolower($row['TimeValue']);
            }
            $stmt->close();
            return $timeSlots;
        }

        public function getFees($id) {
            $sql = "SELECT DoctorFees FROM Doctor where DoctorID = '$id'";
            $result = $this->con->query($sql);
            $row = $result->fetch_assoc();
            return $row['DoctorFees'];
        }
        
        public function createAppointment() {
            $con = $this->con;
            if(!isset($_SESSION['appointmentData'])) {
                return false;
            }
            
            // Lấy dữ liệu từ session
            $time = $_SESSION['appointmentData']['idSlot'] ?? '';
            $doctorID = $_SESSION['appointmentData']['doctorId'] ?? '';
            $des = $_SESSION['appointmentData']['description'] ?? '';
            // Lấy từ SESSION
            $userID = $_SESSION['id-user'];
            
            // Format thời gian
            if(!$time) {
                return false;
            }
            $formattedTime = DateTime::createFromFormat('g:i A', $time)->format('h:i A');        
            // Tìm TTID từ bảng TimeType
            $sqlTime = "SELECT TTID FROM TimeType WHERE TimeValue = ?";
            $stmt2 = $con->prepare($sqlTime);
            $stmt2->bind_param("s", $formattedTime);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if ($result2->num_rows === 0) {
                return;
            }
        
            $row2 = $result2->fetch_assoc();
            $TTID = $row2['TTID'];
            $stmt2->close();
        
            // Tìm SCID từ bảng Schedule
            $sql1 = "SELECT SCID FROM Schedule WHERE DoctorID = ? AND TTID = ?";
            $stmt1 = $con->prepare($sql1);
            $stmt1->bind_param("ii", $doctorID, $TTID);
            $stmt1->execute();
            $result1 = $stmt1->get_result();
        
            if ($result1->num_rows === 0) {
                return;
            }
        
            $row1 = $result1->fetch_assoc();
            $SCID = $row1['SCID'];
            $stmt1->close();
        
            // Cập nhật isBooked trong Schedule
            $update = "UPDATE Schedule SET isBooked = 1 WHERE SCID = ?";
            $stmtUpdate = $con->prepare($update);
            $stmtUpdate->bind_param("i", $SCID);
            if (!$stmtUpdate->execute()) {
                return;
            }
            $stmtUpdate->close();
        
            // Kiểm tra SCID trong bảng Appointment
            $find = "SELECT SCID FROM Appointment WHERE SCID = ?";
            $stmtFind = $con->prepare($find);
            $stmtFind->bind_param("i", $SCID);
            $stmtFind->execute();
            $resultFind = $stmtFind->get_result();
        
            if ($resultFind->num_rows > 0) {
                return;
            }
            $stmtFind->close();
        
            // Chèn dữ liệu vào bảng Appointment
            $sqlInsert = "INSERT INTO Appointment (UserID, PaymentStatus, AppointmentStatus, Description, SCID) 
                          VALUES (?, 1, 0, ?, ?)";
            $stmtInsert = $con->prepare($sqlInsert);
            $stmtInsert->bind_param("isi", $userID, $des, $SCID);
            $stmtInsert->execute();
            $stmtInsert->close();
        }

        // Admin methods
        public function getDoctorInforforAdmin() {
            $sql = "SELECT u.*, d.DoctorID, d.DoctorFees, d.SPID, s.SPName as Specialty,
                    COALESCE(u.Status, 0) as AccountStatus
                    FROM User u
                    INNER JOIN Doctor d ON d.UserID = u.UserID
                    INNER JOIN Specilist s ON s.SPID = d.SPID
                    WHERE u.RoleID = '2'
                    ORDER BY d.DoctorID";
            return mysqli_query($this->con, $sql);
        }

        public function getAllSpecialtiesAdmin() {
            $sql = "SELECT * FROM specilist ORDER BY SPName";
            return mysqli_query($this->con, $sql);
        }

        public function insertDoctor($specialtyID, $fees, $userID) {
            $sql = "INSERT INTO doctor (SPID, DoctorFees, UserID) VALUES ('$specialtyID', '$fees', '$userID')";
            return mysqli_query($this->con, $sql);
        }

        public function getTotalDoctorsCount() {
            $sql = "SELECT COUNT(*) as total FROM doctor";
            $result = mysqli_query($this->con, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                return (int)$row['total'];
            }
            return 0;
        }

        // Lock/Unlock doctor account
        public function toggleDoctorLockStatus($userID, $status) {
            $sql = "UPDATE user SET Status = '$status' WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        // Update doctor specialty
        public function updateDoctorSpecialty($doctorID, $specialtyID) {
            $sql = "UPDATE doctor SET SPID = '$specialtyID' WHERE DoctorID = '$doctorID'";
            return mysqli_query($this->con, $sql);
        }

        // Update doctor fees
        public function updateDoctorFees($doctorID, $fees) {
            $sql = "UPDATE doctor SET DoctorFees = '$fees' WHERE DoctorID = '$doctorID'";
            return mysqli_query($this->con, $sql);
        }

        // Update doctor profile (user info)
        public function updateDoctorProfile($userID, $fullName, $gender, $birth, $phoneNumber, $address) {
            $sql = "UPDATE user 
                    SET FullName = '$fullName', Gender = '$gender', DOB = '$birth', 
                        PhoneNumber = '$phoneNumber', Address = '$address'
                    WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        // Get doctor status
        public function getDoctorStatus($userID) {
            $sql = "SELECT Status FROM user WHERE UserID = '$userID'";
            $result = mysqli_query($this->con, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                return isset($row['Status']) ? (int)$row['Status'] : 0;
            }
            return 0;
        }
        public function updateDoctor($id, $name, $email, $phone, $specialty, $fees)
        {
            $sql = "UPDATE doctors 
                    SET FullName = ?, Email = ?, PhoneNumber = ?, Specialty = ?, DoctorFees = ?
                    WHERE DoctorID = ?";
        
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ssssdi", $name, $email, $phone, $specialty, $fees, $id);
        
            return $stmt->execute();
        }
        

    }
?>
