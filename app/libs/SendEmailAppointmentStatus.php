<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/src/SMTP.php';

    class SendEmailAppointmentStatus {
        function __construct() {}

        public function Send($toEmail, $toName, $doctorName, $date, $time, $statusText) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'holykimsa05@gmail.com';
                $mail->Password = 'ocfo jdqr ujsu wzqs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('holykimsa05@gmail.com', 'ExtraClinic');
                $mail->addAddress($toEmail, $toName);

                $mail->isHTML(true);
                $mail->Subject = "[Appointment $statusText] ExtraClinic";
                $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
                $safeDoc = htmlspecialchars($doctorName, ENT_QUOTES, 'UTF-8');
                $safeDate = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
                $safeTime = htmlspecialchars($time, ENT_QUOTES, 'UTF-8');
                $safeStatus = htmlspecialchars(ucfirst($statusText), ENT_QUOTES, 'UTF-8');

                $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                        <title>Appointment $safeStatus</title>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; background-color: #f4f4f4; padding: 20px; }
                            .email-content { background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 0 auto; width: 80%; max-width: 600px; }
                            .email-header { text-align: center; font-size: 24px; font-weight: bold; color: #1d2d5e; }
                            .email-header span { color: #0cb8b6; }
                            .highlight { color: #1d2d5e; font-weight: bold; }
                            .email-body { font-size: 16px; line-height: 1.6; color: #333; }
                            .email-footer { text-align: center; font-size: 14px; color: #777; margin-top: 20px; }
                            .badge { display: inline-block; padding: 4px 10px; border-radius: 12px; background: #f0fdfd; color:#0cb8b6; border:1px solid rgba(12,184,182,0.2); }
                        </style>
                    </head>
                    <body>
                        <div class='email-content'>
                            <p class='email-header'>Appointment <span>$safeStatus</span></p>
                            <div class='email-body'>
                                <p>Dear <span class='highlight'>$safeName</span>,</p>
                                <p>Your appointment has been <span class='badge'>$safeStatus</span>.</p>
                                <p><strong>Doctor:</strong> Dr. $safeDoc<br/>
                                   <strong>Date:</strong> $safeDate<br/>
                                   <strong>Time:</strong> $safeTime
                                </p>
                                <p>If you have any questions, please reply to this email.</p>
                                <p>Thank you,<br/>ExtraClinic Team</p>
                            </div>
                            <div class='email-footer'>
                                <p>&copy; 2024 ExtraClinic. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                $mail->send();
                return true;
            } catch (Exception $e) {
                return false;
            }
        }
    }
?>


