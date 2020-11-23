<?php
//set_time_limit(1000);
$ignoreAuth = true;
require_once("../globals.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use OpenEMR\Common\Crypto\CryptoGen;

$email = 'aiyashahmed96@gmail.com';
$user = 'ayyash@imarasoft.net';
$password = 'Ayyash_123!';
$port =   465;
$secure = 'ssl';
$host = 'mail.imarasoft.net';
$refId = "0092";
$body = "
                <table cellpadding='0' cellspacing='2'>
                <tr>
                <td align='center'><font size='4'><strong>Test mail</strong></font></td>
                </tr>
                <tr>
                <td align='center'><p>hello aiyash this is an test mail</p>
                </td>
                </tr>
                <tr>
                <td>Email</td><td>$email</td>
                </tr>
                </table>";

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = false;
    $mail->isSMTP();
    $mail->IsHTML(true);
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $user;
    $cryptoGen = new CryptoGen();
    $mail->Password = $password;
    $mail->SMTPSecure = $secure;
    $mail->Port = $port;

    $mail->setFrom('billing@mindfultransitions.com', 'Billing Coordinator');
//    $mail->addReplyTo('billing@mindfultransitions.com', 'Billing Coordinator');
//    $mail->addBCC('john.jalbert@mindfultransitions.com', 'John Jalbert');
    $mail->addAddress($email, 'Client');
    //$mail->addEmbeddedImage('/images/receipt-logo.png', 'receipt-logo', 'receipt-logo.png');
    $mail->Subject = 'Mindful Transitions, LLC Transaction Receipt - Reference Number '. $refId;
    $mail->Body = $body;
    $mail->send();
    echo '<br><br>Message Sent. Please check email for results';
}
catch (Exception $e)
{
    echo "Message could not be sent";
    echo "<pre>";
    echo "Mailer error: " . $mail->ErrorInfo;

}
?>
