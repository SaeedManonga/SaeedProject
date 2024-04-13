<?php include('db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

?>
<!-- Info boxes -->
<div class="col-12">
	<div class="card">
		<div class="card-body">
			Welcome! <b><?php echo $_SESSION['login_name'] ?>!</b>
		</div>
	</div>
</div>
<i>Make You Payment Here, Save or print receipt. A copy will be sent to your email.</i>
<div class="row col-8">

	<?php
	require_once 'vendor/autoload.php';

	if (isset($_POST['pay'])) {
		$ClientProperty = $_POST['id'];
		$ClientId = $_POST['clierntId'];
		$propertyUse = $_POST['propertyUse'];
		$PropertyId  = $_POST['PropertyId'];
		$Balance = $_POST['CurrentBalance'];
		$AmountPaid = $_POST['amountToPay'];

		$mobile = $_POST['mobile'];

		$pay = $conn->query("INSERT INTO property_revenu(`ClientId`,  `ClientProperty`, `PropertyUse`, `PropertyId`, `AmountPaid`, `Balance`)
		  VALUES ('$ClientId', '$ClientProperty', '$propertyUse', '$PropertyId',  '$AmountPaid', '$Balance')");
		$paymentId = $conn->insert_id;
		//$rs2 = $qry2->fetch_array();
		$get = $conn->query("SELECT property.AmountPaid, property.CurrentBalance, client.ClientName, client.Email, client.ClientID, client.id, property.Property FROM client LEFT JOIN property ON client.id = property.clientId WHERE client.id = '$ClientId' ");
		$result = $get->fetch_array();

		$newCurrentBalance = $result['CurrentBalance'] - $AmountPaid;
		$totalAmountPaid = $AmountPaid + $result['AmountPaid'];
		//echo $AmountPaid.' + '.$result['AmountPaid'].' = '.$totalAmountPaid;

		if ($result) {
			$update = $conn->query("UPDATE property SET AmountPaid = '$totalAmountPaid', CurrentBalance = $newCurrentBalance WHERE id = '$ClientProperty' ");
			/*Send SMS after payment is made
if($_POST['cardNum']==NULL){
    $MessageBird = new \MessageBird\Client('GskgXOv2RNtBJQvhDjXgnTqZO');
	$Message = new \MessageBird\Objects\Message();
	$Message->originator = '+260979909982';
	$Message->recipients = array(+260979909982);
	$Message->body = "Land & Property Rates Department. You have made a payment on a property Via Mobile Money. Mobile Num: ".$mobile.". Property Num: ".$PropertyId.". Amount Paid: MK ".$AmountPaid.". Receipt Num: ".$paymentId.". Ref: ".date('dmy-hmi').". Balance Due: MK ".$Balance.". Thankyou for your Payment";
    $MessageBird->messages->create($Message);  
}else{
	$MessageBird = new \MessageBird\Client('GskgXOv2RNtBJQvhDjXgnTqZO');
	$Message = new \MessageBird\Objects\Message();
	$Message->originator = '+260979909982';
	$Message->recipients = array(+260979909982);
	$Message->body = "Land & Property Rates Department. You have made a payment on a property Via Visa Card. Card Num: ".$_POST['cardNum'].". Property Num: ".$PropertyId.". Amount Paid: MK ".$AmountPaid.". Receipt Num: ".$paymentId.". Ref: ".date('dmy-hmi').". Balance Due: MK ".$Balance.". Thankyou for your Payment";
  
    $MessageBird->messages->create($Message);
}*/

			try {
				//Server settings
				// SMTP - Simple Mail Transfer Protocol - A feature used to send and receive emails.
				$mail = new PHPMailer(true);
				$mail->SMTPOptions = array(
					'ssl' => array(
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => false,
					)
				);
				$mail->isSMTP();                                            //Send using SMTP
				$mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
				$mail->SMTPAuth   = true;
				$mail->Username   = 'saeedmanonga@gmail.com';              //SMTP username
				$mail->Password   = 'thfzyevyqwvokvxn';                                     //Enable SMTP authentication
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
				$mail->Port       = 587;  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

				//Recipients
				$mail->setFrom('revenuedepartments@rcs.com', 'REVENUE DEPARTMENT');
				$mail->addAddress($result['Email']);

				//Content
				$mail->isHTML(true);                                  //Set email format to HTML
				$mail->Subject = 'REVENUE DEPARTMENT';
				$mail->Body    = '<h3 style="color:;">Thank you for you Payment! ' . $result['ClientName'] . ' - ' . $result['ClientID'] . '</h3>
	<div style="font-size:18px;"><p>You have made a payment for Property Num. ' . $PropertyId . '<p/>
		  PROPERTY DESC.: ' . $result['Property'] . ' <br/>
		  PROPERTY NUM.: ' . $PropertyId . '.<br/>
		  PAYMENT NUM.: ' . $paymentId . '.<br/>
		 
		  AMOUNT PAID.: MK ' . $AmountPaid . '<br/>
		  BALANCE DUE.: MK ' . $Balance . '<br/>
		  <br/>
		  <hr style="border-style:dotted; border-color: black;" />
			  <center>&copy;2021, </center>
		  <br/>
		  <i>
			  <center>You can print this receipt and present it as proof od payment. THANK YOU.</center>
		  </i>';
				$mail->send();
				echo '<span style="color:green;text-align:center; font-weight: bold;">Success!. A receipt has been Emailed.</span>';
			} catch (Exception $e) {
				echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
			}
		} else {
			echo '<span style="color:red;text-align:center; font-weight: bold;">System Error! Faile to Pay.</span>';
		}
	}
	if (isset($_POST['step2'])) {
		$proId = $SessionId;
		$qry = $conn->query("SELECT * FROM property  WHERE ClientId = '$proId' ");
		$rs = $qry->fetch_array();
		if ($_POST['paymethode'] == '1') {
			echo '
					  <span style="color:green;text-align:center; font-weight: bold;">Youre making payment Via Mobile Money.</span>
				  <div class="container-fluid">
					<form action="" id="make_payment" method="POST">
					  <input type="hidden" class="form-control form-control-sm" name="id" id="id" value="' . $rs['id'] . '" readonly>
					  <input type="hidden" class="form-control form-control-sm" name="clierntId" id="clierntId" value="' . $rs['ClientId'] . '" readonly>	
					  <input type="hidden" class="form-control form-control-sm" name="propertyUse" id="propertyUse" value="' . $rs['PropertyUse'] . '" readonly>
					  <input type="hidden" max-length="16" min-length="12" class="form-control form-control-sm" name="cardNum" id="cardNum" required placeholder="Enter Credit Number to use">
					  
					  <div class="form-group">
						<label for="mobile" class="control-label">Mobile Number (Use Correct mobile for SMS receipt.)</label>
						<i>Please include +265</i>
						<input type="number" max-length="16" min-length="12" class="form-control form-control-sm" name="mobile" id="mobile" required placeholder="Enter Mobile Account to Use">
					  </div>		  
  
						<label for="PropertyId" class="control-label">Property Number</label>
					  <input type="text" class="form-control form-control-sm" name="PropertyId" value="' . $rs['PropertyId'] . '" readonly>
					  <div id="msg" class="form-group"></div>
					  <div class="form-group">
						<label for="property" class="control-label">Property Description</label>
						<input type="text" class="form-control form-control-sm" name="property" id="property" value="' . $rs['Property'] . '" readonly>					  
					  </div>
					  <div class="form-group">
						<label for="Balance" class="control-label">Outstanding Balance(MK)</label>
						<input type="number" class="form-control form-control-sm" name="Balance" id="Balance" value="' . $rs['CurrentBalance'] . '" readonly>
					  </div>
					  <div class="form-group">
						<label for="amountToPay" class="control-label">Amout to pay(MK)</label>
						<input type="number" max-length="7" min-length="3" class="form-control form-control-sm" name="amountToPay" id="amountToPay" required onkeyup="reSum()" placeholder="Enter an amount to pay">
					  </div>
					  <div class="form-group">
						<label for="CurrentBalance" class="control-label">Current Balance(MK)</label>
						<input type="number" max-length="7" min-length="3" class="form-control form-control-sm" name="CurrentBalance" id="CurrentBalance" readonly>
					  </div>
					  <div class="card-footer border-top border-info">
						<div class="d-flex w-100 justify-content-center align-items-center">
						  <button type="submit" class="btn btn-flat  bg-gradient-primary mx-2" name="pay">PAY</button>
						  <a href="./index.php?page=step1&proId=' . $rs['PropertyId'] . '"><button type="button" class="btn btn-flat  bg-gradient-warning mx-2">BACK</button></a>
						</div>
					</form>
				  </div>';
		} else {
			echo '
					  <span style="color:green;text-align:center; font-weight: bold;">Youre making payment Via Visa Card.</span>
				  <div class="container-fluid">
					<form action="" id="make_payment" method="POST">
					  <input type="hidden" class="form-control form-control-sm" name="id" id="id" value="' . $rs['id'] . '" readonly>
					  <input type="hidden" class="form-control form-control-sm" name="clierntId" id="clierntId" value="' . $rs['ClientId'] . '" readonly>	
					  <input type="hidden" class="form-control form-control-sm" name="propertyUse" id="propertyUse" value="' . $rs['PropertyUse'] . '" readonly>
					  
					  <div class="form-group">
						<label for="amountToPay" class="control-label">Mobile Number (Use Correct mobile for SMS receipt.)</label>
						<i></i>
						<input type="number" max-length="16" min-length="12" class="form-control form-control-sm" name="mobile" id="mobile" required placeholder="Enter Mobile Number">
					  </div>
					  <div class="form-group">
						<label for="cardNum" class="control-label">Card Number</label>
						<input type="number" max-length="16" min-length="12" class="form-control form-control-sm" name="cardNum" id="cardNum" required placeholder="Enter Credit Number to use">
					  </div>
  
						<label for="PropertyId" class="control-label">Property Number</label>
					  <input type="text" class="form-control form-control-sm" name="PropertyId" value="' . $rs['PropertyId'] . '" readonly>
					  <div id="msg" class="form-group"></div>
					  <div class="form-group">
						<label for="property" class="control-label">Property Description</label>
						<input type="text" class="form-control form-control-sm" name="property" id="property" value="' . $rs['Property'] . '" readonly>					  
					  </div>
					  <div class="form-group">
						<label for="Balance" class="control-label">Outstanding Balance(MK)</label>
						<input type="number" class="form-control form-control-sm" name="Balance" id="Balance" value="' . $rs['CurrentBalance'] . '" readonly>
					  </div>
					  <div class="form-group">
						<label for="amountToPay" class="control-label">Amout to pay(MK)</label>
						<input type="number" max-length="7" min-length="3" class="form-control form-control-sm" name="amountToPay" id="amountToPay" required onkeyup="reSum()" placeholder="Enter an amount to pay">
					  </div>
					  <div class="form-group">
						<label for="CurrentBalance" class="control-label">Current Balance(MK)</label>
						<input type="number" max-length="7" min-length="3" class="form-control form-control-sm" name="CurrentBalance" id="CurrentBalance" readonly>
					  </div>
					  <div class="card-footer border-top border-info">
						<div class="d-flex w-100 justify-content-center align-items-center">
						  <button type="submit" class="btn btn-flat  bg-gradient-primary mx-2" name="pay"><i class="fa fa-thumbssss-up"></i> PAY</button>
						  <a href="./index.php?page=step1&proId=' . $rs['PropertyId'] . '"><button type="button" class="btn btn-flat  bg-gradient-warning mx-2"><i class="fa fa-angle-double-left"> </i> BACK</button></a>
						</div>
					</form>
				  </div>';
		}
	}

	?>
</div>
<script>
	function reSum() {
		var num1 = parseInt(document.getElementById("Balance").value);
		var num2 = parseInt(document.getElementById("amountToPay").value);
		document.getElementById("CurrentBalance").value = +num1 - num2;

	}
</script>