<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require './vendor/autoload.php';


// configure
// $from = 'Contact form contact@anas-aftis.com';
// $sendTo = 'contact@anas-aftis.com';
$subject = 'New message from contact form';
$fields = array('name' => $_POST['name'], 'email' => $_POST['email'], 'message' => $_POST['message']); // array variable name => Text to appear in the email
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';

// let's do the sending
if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
    //your site secret key
    $secret = '6Lc56wUnAAAAAL0gnAzyuPTcsPgrgAfBANS6FyZx';

    //get verify response data
    $c = curl_init('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $verifyResponse = curl_exec($c);

    $responseData = json_decode($verifyResponse);
    if($responseData->success):
        $mail = new PHPMailer(true);
        try
        {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtppro.zoho.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'contact@anas-aftis.com';
            $mail->Password   = 'a@bC3#Htx7$';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $mail->addAddress($_POST['email'], $_POST['name']);

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $_POST['message'];
            
            $mail->send();
            // echo 'Message has been sent';
            
            // mail($sendTo, $subject, $emailText, implode("\n", $headers));
            $responseArray = array('type' => 'success', 'message' => $okMessage);
        }
        catch (Exception $e)
        {
            $responseArray = array('type' => 'danger', 'message' => $errorMessage);
            echo $mail->ErrorInfo;
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
        }
        else {
            echo $responseArray['message'];
        }

    else:
        $errorMessage = 'Robot verification failed, please try again.';
        $responseArray = array('type' => 'danger', 'message' => $errorMessage);
        $encoded = json_encode($responseArray);

            header('Content-Type: application/json');

            echo $encoded;
    endif;
else:
    $errorMessage = 'Please click on the reCAPTCHA box.';
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
endif;