<?php
class AppointmentModel extends DB
{
    public function getAllTime()
    {
        $qr = "SELECT * FROM timetype";
        $result = mysqli_query($this->con, $qr);
        $timeTypes = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $timeTypes[] = $row['TimeValue']; // Lưu từng mốc thời gian vào mảng
            }
        }
        return $timeTypes;
    }

    public function getPatientsAppointment($doctorUserId)
    {
        $patientList = [];
        $qr = "SELECT 
                u.Image AS ImagePatient,
                u.FullName AS NamePatient,
                u.Email AS EmailPatient,
                tt.TimeValue AS TimeValue,
                s.DateOfSchedule AS DateSchedule,
                a.PaymentStatus AS Payment,
                a.AppointmentStatus AS StatusAppointment
            FROM appointment a
            JOIN schedule s ON a.SCID = s.SCID
            JOIN timetype tt ON s.TTID = tt.TTID
            JOIN doctor d ON s.DoctorID = d.DoctorID
            JOIN user u ON a.UserID = u.UserID
            WHERE d.UserID = ?";

        $stmt = $this->con->prepare($qr);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $patientList[] = $row;
            }
        }

        return $patientList;
    }

    // List appointments with details for a doctor (by doctor user id)
    public function listDoctorAppointments(int $doctorUserId): array
    {
        $appointments = [];
        $sql = "SELECT a.APID AS AppointmentID, a.PaymentStatus, a.AppointmentStatus, a.Description,
                       u.UserID AS PatientUserID, u.FullName AS PatientName, u.Email AS PatientEmail, u.Image AS PatientImage,
                       s.SCID, s.DateOfSchedule, tt.TimeValue
                FROM appointment a
                JOIN schedule s ON a.SCID = s.SCID
                JOIN timetype tt ON s.TTID = tt.TTID
                JOIN doctor d ON s.DoctorID = d.DoctorID
                JOIN user u ON a.UserID = u.UserID
                WHERE d.UserID = ?
                ORDER BY s.DateOfSchedule DESC, tt.TimeValue DESC";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
        return $appointments;
    }

    // Update appointment status (0=pending, 1=accepted, 2=declined)
    public function updateAppointmentStatus(int $appointmentId, int $status): bool
    {
        // Begin transaction to keep appointment and schedule in sync
        $this->con->begin_transaction();
        try {
            // Update appointment status
            $stmt = $this->con->prepare("UPDATE appointment SET AppointmentStatus = ? WHERE APID = ?");
            $stmt->bind_param("ii", $status, $appointmentId);
            if (!$stmt->execute()) {
                $this->con->rollback();
                return false;
            }
            $stmt->close();

            // Get SCID for this appointment
            $stmtSel = $this->con->prepare("SELECT SCID FROM appointment WHERE APID = ?");
            $stmtSel->bind_param("i", $appointmentId);
            $stmtSel->execute();
            $res = $stmtSel->get_result();
            $row = $res->fetch_assoc();
            $stmtSel->close();
            if (!$row || !isset($row['SCID'])) {
                $this->con->rollback();
                return false;
            }
            $scid = (int)$row['SCID'];

            // Accept => mark schedule booked; Decline => free the slot
            $isBooked = ($status === 1) ? 1 : 0;
            $stmtUpdSc = $this->con->prepare("UPDATE schedule SET IsBooked = ? WHERE SCID = ?");
            $stmtUpdSc->bind_param("ii", $isBooked, $scid);
            if (!$stmtUpdSc->execute()) {
                $this->con->rollback();
                return false;
            }
            $stmtUpdSc->close();

            $this->con->commit();
            return true;
        } catch (Exception $e) {
            $this->con->rollback();
            return false;
        }
    }

    // Fetch info for emailing after status update
    public function getAppointmentEmailInfo(int $appointmentId): ?array
    {
        $sql = "SELECT a.APID, a.AppointmentStatus,
                       u.Email AS PatientEmail, u.FullName AS PatientName,
                       du.FullName AS DoctorName,
                       s.DateOfSchedule, tt.TimeValue
                FROM appointment a
                JOIN schedule s ON a.SCID = s.SCID
                JOIN timetype tt ON s.TTID = tt.TTID
                JOIN doctor d ON s.DoctorID = d.DoctorID
                JOIN user du ON d.UserID = du.UserID
                JOIN user u ON a.UserID = u.UserID
                WHERE a.APID = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $appointmentId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // ===== Dashboard stats for a doctor (by doctor user id)
    public function getDashboardStats(int $doctorUserId): array
    {
        $stats = [
            'todayAppointments' => 0,
            'totalPatients' => 0,
            'monthlyRevenue' => 0.0
        ];

        // Today appointments
        $sqlToday = "SELECT COUNT(*) AS cnt
                     FROM appointment a
                     JOIN schedule s ON a.SCID = s.SCID
                     JOIN doctor d ON s.DoctorID = d.DoctorID
                     WHERE d.UserID = ? AND s.DateOfSchedule = CURDATE()";
        $stmt = $this->con->prepare($sqlToday);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) { $stats['todayAppointments'] = (int)$row['cnt']; }
        $stmt->close();

        // Total unique patients (all time)
        $sqlPatients = "SELECT COUNT(DISTINCT a.UserID) AS cnt
                        FROM appointment a
                        JOIN schedule s ON a.SCID = s.SCID
                        JOIN doctor d ON s.DoctorID = d.DoctorID
                        WHERE d.UserID = ?";
        $stmt = $this->con->prepare($sqlPatients);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) { $stats['totalPatients'] = (int)$row['cnt']; }
        $stmt->close();

        // Monthly revenue (accepted appointments this month * DoctorFees)
        $sqlRevenue = "SELECT COALESCE(SUM(d.DoctorFees), 0) AS revenue
                       FROM appointment a
                       JOIN schedule s ON a.SCID = s.SCID
                       JOIN doctor d ON s.DoctorID = d.DoctorID
                       WHERE d.UserID = ?
                         AND a.AppointmentStatus = 1
                         AND MONTH(s.DateOfSchedule) = MONTH(CURDATE())
                         AND YEAR(s.DateOfSchedule) = YEAR(CURDATE())";
        $stmt = $this->con->prepare($sqlRevenue);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) { $stats['monthlyRevenue'] = (float)$row['revenue']; }
        $stmt->close();

        return $stats;
    }

    // Today's appointments detailed list for dashboard
    public function listTodaysAppointments(int $doctorUserId): array
    {
        $rows = [];
        $sql = "SELECT a.APID AS AppointmentID,
                       a.AppointmentStatus,
                       u.FullName AS PatientName,
                       u.Image AS PatientImage,
                       s.DateOfSchedule,
                       tt.TimeValue
                FROM appointment a
                JOIN schedule s ON a.SCID = s.SCID
                JOIN timetype tt ON s.TTID = tt.TTID
                JOIN doctor d ON s.DoctorID = d.DoctorID
                JOIN user u ON a.UserID = u.UserID
                WHERE d.UserID = ?
                  AND s.DateOfSchedule = CURDATE()
                ORDER BY tt.TimeValue ASC";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function createAppointment($doctorId, $patientName, $appointmentDate, $timeType)
    {
        $stmt = $this->con->prepare("SELECT TTID FROM timetype WHERE TimeValue = ?");
        $stmt->bind_param("s", $timeType);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $timeTypeId = $row['TTID']; // Lấy time_type_id
            $stmt = $this->con->prepare("SELECT SCID FROM schedule WHERE DoctorID = ? AND TTID = ?");
            $stmt->bind_param("ii", $doctorId, $timeTypeId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $scid = $row['SCID'];

                // Lưu lịch hẹn
                $qr = "INSERT INTO appointment (UserID, SCID, DateOfSchedule, TimeType, PaymentStatus, AppointmentStatus)
                    VALUES (?, ?, ?, ?, 0,0)";

                // Thực hiện truy vấn lưu lịch hẹn
                $stmt = $this->con->prepare($qr);
                $stmt->bind_param("isss", $patientId, $scid, $appointmentDate, $timeType);

                // Thực hiện truy vấn
                if ($stmt->execute()) {
                    return true; // Lưu thành công
                }
            }
        }

        return false; // Lưu không thành công
    }

    // ===== Doctor Schedule Management =====
    private function getDoctorIdByUserId($doctorUserId)
    {
        $stmt = $this->con->prepare("SELECT DoctorID FROM doctor WHERE UserID = ?");
        $stmt->bind_param("i", $doctorUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return (int)$row['DoctorID'];
        }
        return null;
    }

    public function listSchedulesByDoctorUser(int $doctorUserId): array
    {
        $doctorId = $this->getDoctorIdByUserId($doctorUserId);
        if ($doctorId === null) return [];

        $schedules = [];
        $sql = "SELECT s.SCID, s.DateOfSchedule, tt.TimeValue, s.IsBooked
                FROM schedule s
                JOIN timetype tt ON s.TTID = tt.TTID
                WHERE s.DoctorID = ?
                ORDER BY s.DateOfSchedule ASC, tt.TimeValue ASC";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("i", $doctorId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $date = $row['DateOfSchedule'];
            if (!isset($schedules[$date])) {
                $schedules[$date] = [
                    'date' => $date,
                    'slots' => []
                ];
            }
            // Convert TimeValue 'h:i A' to 24h 'HH:MM' for UI
            $time24 = date('H:i', DateTime::createFromFormat('h:i A', $row['TimeValue'])->getTimestamp());
            $schedules[$date]['slots'][] = [
                'scid' => (int)$row['SCID'],
                'time24' => $time24,
                'isBooked' => (int)$row['IsBooked']
            ];
        }
        return array_values($schedules);
    }

    public function addScheduleByDoctorUser(int $doctorUserId, string $date, array $timeSlots24): bool
    {
        $doctorId = $this->getDoctorIdByUserId($doctorUserId);
        if ($doctorId === null) return false;

        $this->con->begin_transaction();
        try {
            foreach ($timeSlots24 as $time24) {
                // Convert 'HH:MM' to 'h:i A'
                $time12 = strtoupper(date('h:i A', strtotime($time24)));
                $stmtTT = $this->con->prepare("SELECT TTID FROM timetype WHERE TimeValue = ?");
                $stmtTT->bind_param("s", $time12);
                $stmtTT->execute();
                $resTT = $stmtTT->get_result();
                if (!$rowTT = $resTT->fetch_assoc()) {
                    // Skip unknown time
                    continue;
                }
                $ttid = (int)$rowTT['TTID'];
                $stmtTT->close();

                // Avoid duplicates
                $stmtCheck = $this->con->prepare("SELECT SCID FROM schedule WHERE DoctorID = ? AND DateOfSchedule = ? AND TTID = ?");
                $stmtCheck->bind_param("isi", $doctorId, $date, $ttid);
                $stmtCheck->execute();
                $resCheck = $stmtCheck->get_result();
                if ($resCheck->num_rows > 0) {
                    $stmtCheck->close();
                    continue;
                }
                $stmtCheck->close();

                $stmtIns = $this->con->prepare("INSERT INTO schedule (DoctorID, DateOfSchedule, TTID, IsBooked) VALUES (?, ?, ?, 0)");
                $stmtIns->bind_param("isi", $doctorId, $date, $ttid);
                $stmtIns->execute();
                $stmtIns->close();
            }
            $this->con->commit();
            return true;
        } catch (Exception $e) {
            $this->con->rollback();
            return false;
        }
    }

    public function deleteScheduleByDoctorUserAndDate(int $doctorUserId, string $date): bool
    {
        $doctorId = $this->getDoctorIdByUserId($doctorUserId);
        if ($doctorId === null) return false;
        $stmt = $this->con->prepare("DELETE FROM schedule WHERE DoctorID = ? AND DateOfSchedule = ?");
        $stmt->bind_param("is", $doctorId, $date);
        return $stmt->execute();
    }

    // Admin methods
    public function getTotalAppointmentsCount() {
        $sql = "SELECT COUNT(*) as total FROM appointment";
        $result = mysqli_query($this->con, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            return (int)$row['total'];
        }
        return 0;
    }

    public function getTodayAppointmentsCount() {
        $sql = "SELECT COUNT(*) as total 
                FROM appointment a
                JOIN schedule s ON a.SCID = s.SCID
                WHERE s.DateOfSchedule = CURDATE()";
        $result = mysqli_query($this->con, $sql);
        if ($row = mysqli_fetch_assoc($result)) {
            return (int)$row['total'];
        }
        return 0;
    }

    public function getAllAppointmentsForAdmin() {
        $sql = "SELECT 
                    a.APID AS AppointmentID,
                    u.FullName AS PatientName,
                    u.Email AS PatientEmail,
                    u.PhoneNumber AS PatientPhone,
                    du.FullName AS DoctorName,
                    sp.SPName AS Specialty,
                    sch.DateOfSchedule AS AppointmentDate,
                    tt.TimeValue AS AppointmentTime,
                    CASE 
                        WHEN a.AppointmentStatus = 0 THEN 'Pending'
                        WHEN a.AppointmentStatus = 1 THEN 'Accepted'
                        WHEN a.AppointmentStatus = 2 THEN 'Declined'
                        ELSE 'Unknown'
                    END AS Status,
                    CASE 
                        WHEN a.PaymentStatus = 0 THEN 'Unpaid'
                        WHEN a.PaymentStatus = 1 THEN 'Paid'
                        ELSE 'Unknown'
                    END AS PaymentStatus,
                    a.Description
                FROM appointment a
                JOIN schedule sch ON a.SCID = sch.SCID
                JOIN timetype tt ON sch.TTID = tt.TTID
                JOIN doctor d ON sch.DoctorID = d.DoctorID
                JOIN user du ON d.UserID = du.UserID
                JOIN specilist sp ON d.SPID = sp.SPID
                JOIN user u ON a.UserID = u.UserID
                ORDER BY sch.DateOfSchedule DESC, tt.TimeValue DESC";
        return mysqli_query($this->con, $sql);
    }
}
