<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
namespace App\Phpmailer;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
//require 'vendor/autoload.php';

class CustomMailer {
	public static function send_mail($to,$subject,$body){
		if(SEND_MAIL){
			require_once base_path('app/Phpmailer/vendor/autoload.php');

			// Instantiation and passing `true` enables exceptions
			$mail = new PHPMailer(true);
			try {
				//Server settings
				//$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
				$mail->isSMTP();                                            // Send using SMTP
				$mail->Host       = 'rentalwheel.in';               // Set the SMTP server to send through
				$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
				$mail->Username   = 'noreply@rentalwheel.in';              // SMTP username
				$mail->Password   = 'pc45Ch}lB{,N';                         // SMTP password
				//$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
				$mail->Port       = 587 ;   
				
				//Recipients
				$mail->setFrom('noreply@rentalwheel.in', 'Rental Wheel');
				$mail->addAddress($to);

				// Attachments
				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments

				// Content
				$mail->isHTML(true);                                  // Set email format to HTML
				$mail->Subject = $subject;
				$mail->Body    = $body;

				$mail->send();
				//echo 'Message has been sent';
			} catch (\Exception $e) {
				//echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
		}
	}
}