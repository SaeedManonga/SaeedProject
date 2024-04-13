<?php include('db_connect.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
<i>NOTE: Changes you make to your Account Details will be reviewed. An Email will be sent to you.</i>
<div class="row col-8">
  <?php

  $id = $_GET['data-id'];
  $qry = $conn->query("SELECT 
                  `client`.`id`
                  ,`client`.`ClientName`
                    , `client`.`IdentityType`
                    , `client`.`ClientID`
                    , `client`.`PostalAddress`
                    , `client`.`PhysicalAddress`
                    , `client`.`Mobile`
                    , `client`.`Email`
                    , `client`.`AdditionalInformation`
                  ,`client_type`.`ClientType`
                  FROM client LEFT JOIN `client_type` 
                        ON (`client_type`.`id` = `client`.`ClientType`) WHERE client.id = '$id' ");
  $rs = $qry->fetch_array();
  ?>
  <div class="container-fluid">
    <form action="" id="update_client" method="POST">
      <input type="hidden" name="id" value="<?php echo $rs['id'] ?>">
      <div id="msg" class="form-group"></div>
      <div class="form-group">
        <label for="ClientName" class="control-label">Client Name</label>
        <input type="text" class="form-control form-control-sm" name="ClientName" id="ClientName" value="<?php echo $rs['ClientName'] ?>" required>
      </div>
      <div class="form-group">
        <label for="IdentityType" class="control-label">Current Identity Type</label>
        <input type="text" class="form-control form-control-sm" name="" id="IdentityType" value="<?php if ($rs['IdentityType'] == '1') {
                                                                                                    echo 'NRC';
                                                                                                  } elseif ($rs['IdentityType'] == '2') {
                                                                                                    echo 'PASSPORT';
                                                                                                  } elseif ($rs['IdentityType'] == '3') {
                                                                                                    echo 'DRIVER\'\S LICENSE';
                                                                                                  } elseif ($rs['IdentityType'] == '4') {
                                                                                                    echo 'VOTER\'\S VOTER';
                                                                                                  } else {
                                                                                                    echo 'PACRA/COMPANY';
                                                                                                  } ?>" readonly>
      </div>
      <div class="form-group">
        <label for="ClientID" class="control-label">Client ID Number</label>
        <input type="text" class="form-control form-control-sm" name="ClientID" id="ClientID" value="<?php echo $rs['ClientID'] ?>" required>
      </div>
      <div class="form-group">
        <label for="Mobile" class="control-label">Mobile Number</label>
        <input type="text" class="form-control form-control-sm" name="Mobile" id="Mobile" value="<?php echo $rs['Mobile'] ?>" required>
      </div>
      <div class="form-group">
        <label for="Email" class="control-label">Client Email</label>
        <input type="text" class="form-control form-control-sm" name="Email" id="Email" value="<?php echo $rs['Email'] ?>" required>
      </div>
      <div class="form-group">
        <label for="AdditionalInformation" class="control-label">AdditionalInformation</label>
        <textarea type="text" class="form-control form-control-sm" name="AdditionalInformation" id="AdditionalInformation" required><?php echo $rs['AdditionalInformation'] ?></textarea>
      </div>
      <div class="card-footer border-top border-info">
        <div class="d-flex w-100 justify-content-center align-items-center">
          <button type="submit" class="btn btn-flat  bg-gradient-primary mx-2" name="update">Save</button>
        </div>
    </form>
  </div>

  <?php
  if (isset($_POST['update'])) {
    $NewClientIdentity  = $_POST['ClientID'];
    $NewMobileNumber = $_POST['Mobile'];
    $NewEmail = $_POST['Email'];
    $NewAdditionalInformation = $_POST['AdditionalInformation'];
    $NewClientName = $_POST['ClientName'];
    $ClientId = $_POST['id'];

    $qry2 = $conn->query("INSERT INTO update_requests(ClientId, NewClientIdentity, NewClientName, NewMobileNumber, NewEmail, NewAdditionalInformation) 
              VALUES('$ClientId', '$NewClientIdentity', '$NewClientName', '$NewMobileNumber', '$NewEmail', '$NewAdditionalInformation')");
    $stmt = $conn->prepare("UPDATE client SET ClientID = ?, ClientName = ?, Mobile = ?, Email = ?, AdditionalInformation = ? WHERE id = ?");
    $stmt->bind_param("ssssss", $NewClientIdentity, $NewClientName, $NewMobileNumber, $NewEmail, $NewAdditionalInformation, $SessionId);
    $success = $stmt->execute();
    $stmt->close();

    // Check if the update was successful
    if ($success) {
      echo '<span style="color:green;text-align:center;">Success! Your Details have been updated.</span>';
    } else {
      echo '<span style="color:red;text-align:center;">Failed to update your details. </span>';
    }

    $get = $conn->query("SELECT * FROM client WHERE id = '$id' ");
    $result = $get->fetch_array();

    $to = $result['Email'];

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
      $mail->Password   = 'thfzyevyqwvokvxn';                                     //Enable SMTP authentication
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
      $mail->Port       = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
      //Recipients
      $mail->setFrom('revenuedepartments@rcs.com', 'REVENUE DEPARTMENT');
      $mail->addAddress($to);
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'no reply';
      $mail->Body    = '<h3 style="color:;">Attention! ' . $result['ClientName'] . ' - ' . $result['ClientID'] . '</h3>
		<div style="font-size:18px;"><p>You have requested to update your account details to the following:<p/>
                Client Identity Number: ' . $NewClientIdentity . ' <br/>
                Client Name: ' . $NewClientName . '.<br/>
                Client Mobile: ' . $NewMobileNumber . '<br/>
                Client Email: ' . $NewEmail . '<br/> 
                Client Information: ' . $NewAdditionalInformation . '<br/>';
      '</div>
';
      $mail->send();


      $mail->clearAddresses();

      $mail->addAddress('saeedmanonga@gmail.com');
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = 'DETAILS UPDATE';
      $mail->Body    = '<h3 style="color:;">Attention! Admin</h3>
		<div style="font-size:18px;"><p>User ' . $result['ClientName'] . ' has updated their account details to the following:<p/>
                Client Identity Number: ' . $NewClientIdentity . ' <br/>
                Client Name: ' . $NewClientName . '.<br/>
                Client Mobile: ' . $NewMobileNumber . '<br/>
                Client Email: ' . $NewEmail . '<br/> 
                Client Information: ' . $NewAdditionalInformation . '<br/>';
      '</div>
';
      $mail->send();
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    // try {
    //   $get = $conn->query("SELECT * FROM client WHERE id = '$SessionId'");
    //   $result = $get->fetch_array();

    //   $to = $result['Email'];
    //   //Server settings
    //   // SMTP - Simple Mail Transfer Protocol - A feature used to send and receive emails.
    //   $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //   $mail->SMTPDebug = SMTP::DEBUG_OFF;                    //Enable verbose debug output
    //   $mail->SMTPOptions = array(
    //     'ssl' => array(
    //       'verify_peer' => false,
    //       'verify_peer_name' => false,
    //       'allow_self_signed' => true
    //     )
    //   );
    //   $mail->isSMTP();                                            //Send using SMTP
    //   $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
    //   $mail->SMTPAuth   = true;
    //   $mail->Username   = 'saeedmanonga@gmail.com';              //SMTP username
    //   $mail->Password   = 'thfzyevyqwvokvxn';                                     //Enable SMTP authentication
    //   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    //   $mail->Port       = 587; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    //   //Recipients
    //   $mail->setFrom('revenuedepartments@rcs.com', 'REVENUE DEPARTMENT');
    // } catch (Exception $e) {
    //   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    // }
  }
  ?>
</div>