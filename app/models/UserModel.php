
<?php 
    class UserModel extends DB {

        public function CheckSignUp() {
            $sql = "SELECT Email FROM user";
            return mysqli_query($this->con, $sql);
        }
        public function InsertNewUser($fullName, $email, $password) {
            $qr = "INSERT INTO user VALUES (NULL, '$fullName', '$email', '$password', NULL, NULL, NULL, NULL, NULL, '3')";
            $result = false;
            if (mysqli_query($this->con, $qr)) {
                $result = true;
            }
            return json_encode($result);
        }

        public function GetUserInforLogIn($email) {
            $sql = "SELECT * FROM user WHERE Email = '$email'";
            return mysqli_query($this->con, $sql);
        }

        public function getUserInfor($userId){
            $sql = "SELECT * FROM user WHERE UserID = '$userId'";
            return mysqli_query($this->con, $sql);
        }

        public function UpdateUserInfor($fullName, $gender, $birth, $address, $userID) {
            $sql = "UPDATE user
                    SET FullName = '$fullName', Gender = '$gender', DOB = '$birth', Address = '$address'
                    WHERE UserID = $userID;";
            return mysqli_query($this->con, $sql);
        }

        public function UpdateUserPassword($newPassword, $userID) {
            $sql = "UPDATE user
                    SET Password = '$newPassword'
                    WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        public function UpdateUserImage($imagepath, $userID) {
            $sql = "UPDATE user
                    SET Image = '$imagepath'
                    WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        // Admin methods
        public function getUserInforforAdmin() {
            $sql = "SELECT u.*, r.RoleName as Role, 
                    COALESCE(u.Status, 0) as AccountStatus
                    FROM user u 
                    LEFT JOIN role r ON u.RoleID = r.RoleID 
                    WHERE u.RoleID = '3' 
                    ORDER BY u.UserID";
            return mysqli_query($this->con, $sql);
        }

        public function getAllUsersForAdmin() {
            $sql = "SELECT u.*, r.RoleName as Role 
                    FROM user u 
                    LEFT JOIN role r ON u.RoleID = r.RoleID 
                    ORDER BY u.UserID";
            return mysqli_query($this->con, $sql);
        }

        public function InsertNewDoctor($fullName, $email, $password, $birth, $gender, $phoneNumber, $address) {
            $qr = "INSERT INTO user VALUES (NULL, '$fullName', '$email', '$password', '$birth', '$gender', '$phoneNumber', '$address', NULL, '2')";
            $result = false;
            if (mysqli_query($this->con, $qr)) {
                $result = true;
            }
            return $result;
        }

        public function getTotalUsersCount() {
            $sql = "SELECT COUNT(*) as total FROM user WHERE RoleID = '3'";
            $result = mysqli_query($this->con, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                return (int)$row['total'];
            }
            return 0;
        }

        // Lock/Unlock user account (Status: 0 = active, 1 = locked)
        public function toggleUserLockStatus($userID, $status) {
            $sql = "UPDATE user SET Status = '$status' WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        // Reset user password to default
        public function resetUserPassword($userID, $newPassword = '12345678') {
            $sql = "UPDATE user SET Password = '$newPassword' WHERE UserID = '$userID'";
            return mysqli_query($this->con, $sql);
        }

        // Get user status
        public function getUserStatus($userID) {
            $sql = "SELECT Status FROM user WHERE UserID = '$userID'";
            $result = mysqli_query($this->con, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                return isset($row['Status']) ? (int)$row['Status'] : 0;
            }
            return 0;
        }

    }
?>
