<?php

use Leaf\Mail as Mail;
use Leaf\Mail\Mailer;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';

// configure
$from = 'contact@anas-aftis.com';
$sendTo = 'contact@anas-aftis.com';
$subject = 'New message, from ' . $_POST['name'];
$fields = array('name' => $_POST['name'], 'email' => $_POST['email'], 'message' => $_POST['message']); // array variable name => Text to appear in the email
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';

$body = <<<HTML
    <h1>Name: {$fields['name']}</h1>
    <h1>Email: {$fields['email']}</h1>
    <hr/>
    <p>{$fields['message']}</p>
HTML;

if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
    //your site recaptcha's secret key
    $secret = '6Lc56wUnAAAAAL0gnAzyuPTcsPgrgAfBANS6FyZx';

    //get verify response data
    $c = curl_init('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $verifyResponse = curl_exec($c);

    $responseData = json_decode($verifyResponse);
    if($responseData->success):
        try
        {
            Mailer::config([ 
                'debug' => false,
                'defaults' => [
                    'senderName' => $_POST['name'],
                    'recipientName' => 'Anas Aftis',
                ],
            ]);

            Mailer::connect([
                'host' => 'smtp.zoho.com',
                'port' => 465,
                'security' => PHPMailer::ENCRYPTION_SMTPS,
                'auth' => [
                    'username' => 'contact@anas-aftis.com',
                    'password' => 'a2bC3#Htx7$'
                ]
            ]);

            $sent = Mail::create([
                'senderEmail' => $from,
                'recipientEmail' => $sendTo,
                'subject' => $subject,
                'body' => $body,
                'isHTML' => true   
            ])
            ->send();

            $responseArray = array('type' => 'success', 'message' => $okMessage);
        }
        catch (Exception $e)
        {
            $responseArray = array('type' => 'danger', 'message' => $errorMessage);
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