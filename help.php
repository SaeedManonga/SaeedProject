<?php include('db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['send'])) {

	$clientId = $_POST['clientId'];
	$Query = $_POST['Query'];

	$send = $conn->query("INSERT INTO client_query(`ClientId`,  `Query`) VALUES ('$clientId', '$Query')");

	$mail = new PHPMailer(true);
	try {
		$get = $conn->query("SELECT * FROM client WHERE id = '$SessionId'");
		$result = $get->fetch_array();

		$to = $result['Email'];
		//Server settings
		// SMTP - Simple Mail Transfer Protocol - A feature used to send and receive emails.
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->SMTPDebug = SMTP::DEBUG_OFF;                    //Enable verbose debug output
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
		$mail->SMTPAuth   = true;
		$mail->Username   = 'saeedmanonga@gmail.com';              //SMTP username
		$mail->Password   = 'thfzyevyqwvokvxn';                                      //Enable SMTP authentication
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
		$mail->Port       = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
		//Recipients
		$mail->setFrom('vendor@revenue.mw', 'Vendor Revenue Malawi');
		$mail->addAddress($to);
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = 'no reply';
		$mail->Body    = '<h3 style="color:;">Hello! ' . $result['ClientName'] . ' - ' . $result['ClientID'] . '</h3>
		<div style="font-size:18px;"><p>Your Message has been sent succefully to the Admin.<p/>
		<h4>Your Message:</h4>
	   ' . $Query . '.<br/>
		<hr style="border-style:dotted; border-color: black;" />
			<center>&copy;2021, BLANTYRE CITY COUNCIL/REVENUE RATES DEPARTMENT</center>
		<br/>
		<i>
			<center>THANK YOU.</center>
		</i>	  </div>
';
		$mail->send();

		$mail->clearAddresses();

		$mail->addAddress('saeedmanonga@gmail.com');
		$mail->isHTML(true);                                  //Set email format to HTML
		$mail->Subject = 'MESSAGE FROM CLIENT';
		$mail->Body    = '<h3 style="color:;">Attention! Admin</h3>
		<p>User ' . $result['ClientName'] . ' has sent the following message:</p></br>
		' . $Query . '.<br/>
		<hr style="border-style:dotted; border-color: black;" />
			<center>&copy;2021, BLANTYRE CITY COUNCIL/REVENUE RATES DEPARTMENT</center>
		<br/>
		<i>
			<center>THANK YOU.</center>
  ';
		$mail->send();
	} catch (Exception $e) {
		echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
	}
	echo '<span style="color:green;text-align:center; font-weight: bold;">Success!. Your Message has been sent.</span>';
}



$qry = $conn->query("SELECT * FROM system_settings");
$rs = $qry->fetch_array();
?>
<!-- Info boxes -->
<div class="col-12">
	<div class="card">
		<div class="card-body">
			Welcome! <b><?php echo $_SESSION['login_name'] ?>!</b>
		</div>
	</div>
</div>
<i>Write your concerns, query or question, our team will respond. Call <b><?php echo '<a href="tel:' . $rs['contact'] . '">' . $rs['contact'] . '</a></b> or Email <a href="mailto:' . $rs['email'] . '"><b>' . $rs['email'] ?></b></a> for Help.</i>
<div class="row col-8">

	<?php


	// 	if ($send) {
	// 		$get = $conn->query("SELECT * FROM client WHERE id = '$SessionId'");
	// 		$result = $get->fetch_array();

	// 		$to = "nyirongoharris@gmail.com";
	// 		$subject = 'REVENUE DEPARTMENT';
	// 		$from = 'saeedmanonga@gmail.com';

	// 		// To send HTML mail, the Content-type header must be set
	// 		$headers  = 'MIME-Version: 1.0' . "\r\n";
	// 		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

	// 		// Create email headers
	// 		$headers .= 'From: ' . $from . "\r\n" .
	// 			'Reply-To: ' . $from . "\r\n" .
	// 			'X-Mailer: PHP/' . phpversion();

	// 		// Compose a simple HTML email message
	// 		$message = '<html><body style="">';
	// 		$message .= '<h3 style="color:;">Hello.! ' . $result['ClientName'] . ' - ' . $result['ClientID'] . '</h3>';
	// 		$message .= '<div style="font-size:18px;"><p>Your Message has been sent.<p/>
	//       <h4>YOUR MESSAGE.</h4>
	// 	  <hr style="border-style:dotted; border-color: black;" />
	// 	 ' . $Query . '.<br/>
	// 	  <hr style="border-style:dotted; border-color: black;" />
	// 		  <center>&copy;2021, </center>
	// 	  <br/>
	// 	  <i>
	// 		  <center>THANK YOU.</center>
	// 	  </i>';
	// 		$message .= '</div></body></html>';

	// 		// Sending email
	// 		if (mail($to, $subject, $message, $headers)) {
	// 			echo '<span style="color:green;text-align:center; font-weight: bold;">Success!. Your Message has been sent.</span>';
	// 		} else {
	// 			echo '<span style="color:red;text-align:center; font-weight: bold;">Error! Sending Email Failed</span>';
	// 		}
	// 	} else {
	// 		echo '<span style="color:red;text-align:center; font-weight: bold;">System Error! Faile to Pay.</span>';
	// 	}
	// }

	$qry = $conn->query("SELECT * FROM client  WHERE id = '$SessionId' ");
	$rs = $qry->fetch_array();
	echo '
                <div class="container-fluid">
                  <form action="" id="clientId" method="POST">
					<input type="hidden" class="form-control form-control-sm" name="clientId" id="clientId" value="' . $rs['ClientID'] . '" readonly>		  

                      <label for="PropertyId" class="control-label">Date</label>
                    <input type="text" class="form-control form-control-sm" name="date" value="' . date('d M, Y') . '" readonly>
                    <div id="msg" class="form-group"></div>
                    <div class="form-group">
                      <label for="Query" class="control-label">Your Message</label>
                      <textarea type="text" class="form-control form-control-sm" name="Query" id="Query" required></textarea>					  
                    </div>
                    <div class="card-footer border-top border-info">
                      <div class="d-flex w-100 justify-content-center align-items-center">
                        <button type="submit" class="btn btn-flat  bg-gradient-primary mx-2" name="send">SEND</button>
                      </div>
                  </form>
                </div>';
	?>
</div>