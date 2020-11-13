<?php


namespace OpenEMR\Common\Email;

//use PHPMailerOAuthGoogle;
use PHPMailer\PHPMailer\PHPMailer;
use OpenEMR\Common\Crypto\CryptoGen;

class EmailCustomerReceipt extends PHPMailer
{
    public $Mailer;
    public $SMTPSecure;
    public $SMTPAuth;
    public $Host;
    public $Username;
    public $Password;
    public $Port;
    public $CharSet;
    public $Url;

    public function __construct()
    {
        $this->Host = "mail.imarasoft.net";
        $this->Username = "ayyash@imarasoft.net";
        $this->SMTPAuth = "Ayyash_123!";
        $this->SMTPSecure = "ssl";
        $this->Port = 465;

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $url = $protocol.$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $this->Url = substr($url, 0, strpos($url, "interface"));

    }

    public function buildBody($header, $transdate, $refId, $card, $customerid, $customername, $rec_type, $email, $dos, $amount, $memo)
    {

        $body = "
                <table cellpadding='0' cellspacing='2'>
                <tr>
                <td align='center'><font size='4'><strong>Customer Receipt</strong></font></td>
                </tr>
                <tr>
                <td align='center'>Mindful Transitions, LLC<br> 2940 Johnson Ferry Rd<br> Suite B-127<br>
                Marietta, GA 30062<br>
                (678) 637-7166<br><br>
                </td>
                </tr>
                <tr>
                <td>Transaction Date</td><td>$transdate</td>
                </tr>
                <tr>
                <td>Reference Number</td><td>$refId</td>
                </tr>
                <tr>
                <td>Credit Card #</td><td>$card</td>
                </tr>
                <tr>
                <td>Chart ID</td><td>$customerid</td>
                </tr>
                <tr>
                <td>Name</td><td>$customername</td>
                </tr>
                <tr>
                <td>Receipt Preferences</td><td>$rec_type</td>
                </tr>
                <tr>
                <td>Email</td><td>$email</td>
                </tr>
                <tr>
                <td>Charge Type</td><td>$header</td>
                </tr>
                <tr>
                <td>Date of Service</td><td>$dos</td>
                </tr>
                <tr>
                <td>Total</td><td>$$amount</td>
                </tr>
                <tr>
                <td>Memo</td><td>$memo</td>
                </tr>
                </table>";
        return $body;
    }

    public function patientAccessBody($emailId, $fname, $lname, $qualification, $date)
    {

        $body = "
<html>
<head>
<style>
.btn{
display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    text-decoration-line: none;
    cursor: pointer;
}
.btn, .btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.btn-primary{
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary{
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
</head>
<body>
<div>
<strong> <span style='text-transform: capitalize'>$fname $lname</span> $qualification has requested access to your chart on $date. Do you allow access?</strong>
</div>
<div style='margin-top: 10px'>
<ui style='list-style: none; float: right'>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?confirm_access=$emailId' class='btn btn-primary btn-sm'>Yes</a>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=patient&to=provider&email_cancel=$emailId' class='btn btn-secondary btn-sm'>No</a>
</ui>
</div>
<div style='margin-top: 10px'>
<span>You have 24 Hours to respond.</span>
</div>
</body>
</html>
";
        return $body;
    }

    public function resetConfirmationBody($id){
        $body = "
<html>
<head>
<style>
.btn{
display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    text-decoration-line: none;
    cursor: pointer;
}
.btn, .btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.btn-primary{
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary{
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
</head>
<body>
<div>
<strong>You have requested a new username/password. Please confirm. </strong>
</div>
<div style='margin-top: 10px'>
<ui style='list-style: none; float: right'>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?password_reset=$id' class='btn btn-primary btn-sm'>Yes, I requested a new Username/Password</a>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?cancel_reset=$id' class='btn btn-secondary btn-sm'>No, I did NOT request a new Username/Password</a>
</ui>
<br>
<br>
<br>
<p>You have 1 hour to respond. If we do not hear from you, your Username/Password will NOT be reset.</p>
</div>
</body>
</html>
";
        return $body;
    }

    public function patientAccessBody1($emailId, $fname, $lname, $organization, $date)
    {

        $body = "
<html>
<head>
<style>
.btn{
display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    text-decoration-line: none;
    cursor: pointer;
}
.btn, .btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.btn-primary{
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary{
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
</head>
<body>
<div>
<strong> <span style='text-transform: capitalize'>$fname $lname </span> from <span style='text-transform: capitalize'>$organization</span> has requested access to your chart on $date. Do you authorize access?</strong>
</div>
<div style='margin-top: 10px'>
<ui style='list-style: none; float: right'>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=requester&email_id=$emailId' class='btn btn-primary btn-sm'>Yes</a>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=patient&email_cancel=$emailId' class='btn btn-secondary btn-sm'>No</a>
</ui>
</div>
<div style='margin-top: 10px'>
<span>You have 24 Hours to respond.</span>
</div>
</body>
</html>
";
        return $body;
    }

    public function providerAccessBody($emailId, $fname, $lname, $qualification, $date)
    {

        $body = "
<html>
<head>
<style>
.btn{
display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    text-decoration-line: none;
    cursor: pointer;
}
.btn, .btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.btn-primary{
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary{
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
</head>
<body>
<div>
<strong>You have requested access to <span style='text-transform: capitalize'>$fname $lname</span>’s chart on $date.  Please confirm this request.</strong>
</div>
<div style='margin-top: 10px'>
<ui style='list-style: none; float: right'>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?confirm_access=$emailId' class='btn btn-primary btn-sm'>Yes, I made this request.</a>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=provider&to=patient&email_cancel=$emailId' class='btn btn-secondary btn-sm'>No, I did not make this request.</a>
</ui>
</div>
<div style='margin-top: 10px'>
<span>Please respond within 1 hour.</span>
</div>
</body>
</html>
";
        return $body;
    }

    public function requesterAccessBody($emailId, $fname, $lname, $date)
    {

        $body = "
<html>
<head>
<style>
.btn{
display: inline-block;
    font-weight: 400;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    border: 1px solid transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    color: #fff;
    text-decoration-line: none;
    cursor: pointer;
}
.btn, .btn-sm {
    padding: .25rem .5rem;
    font-size: .875rem;
    line-height: 1.5;
    border-radius: .2rem;
}
.btn-primary{
    background-color: #007bff;
    border-color: #007bff;
}
.btn-secondary{
    background-color: #545b62;
    border-color: #4e555b;
}
</style>
</head>
<body>
<div>
<strong>You have requested access to <span style='text-transform: capitalize'>$fname $lname</span>’s chart on $date.  Please confirm.</strong>
</div>
<div style='margin-top: 10px'>
<ui style='list-style: none; float: right'>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=requester&email_id=$emailId' class='btn btn-primary btn-sm'>Yes, I made this request.</a>
<a style='color: #fff0ff' href='$this->Url/interface/midocs/midocsController.php?user=requester&email_cancel=$emailId' class='btn btn-secondary btn-sm'>No, I did not make this request.</a>
</ui>
</div>
<div style='margin-top: 10px'>
<span>You have one hour to respond.</span>
</div>
</body>
</html>
";
        return $body;
    }

    public function patientAllowedBody($fname,$lname,$qualification)
    {

        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Thank you for your response.  Once your provider confirms their request, access to your chart will be given to <span style='text-transform: capitalize; font-weight:600;'>$fname $lname, $qualification</span></p>
</body>
</html>
";
        return $body;
    }

    public function providerAllowedBody()
    {

        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Thank you for your response.  Once your patient grants authorization you will be able to access their chart through MiDocs.</p>
</body>
</html>
";
        return $body;
    }

    public function allowedMessage($provider,$patient,$qualification)
    {

        $body = "
<html>
<head>
</head>
<body>
<div>
<p>There is now an association between  <span style='text-transform: capitalize; font-weight:600;'>Provider: $provider[fname] $provider[lname], $qualification </span> and <span style='text-transform: capitalize; font-weight:600;'>Patient: $patient[fname] $patient[lname].</span></p>
</body>
</html>
";
        return $body;
    }

    public function providerRequestBody($provider,$qualification)
    {

        $body = "
<html>
<head>
</head>
<body>
<div>
<p><span style='text-transform: capitalize; font-weight:600;'>$provider[fname] $provider[lname], $qualification</span> wants to view your data on MiDocs, but your information is currently not available.  Sign up for MiDocs to easily share medical data on a secure platform with your healthcare providers and others of your choice.
<br>
<br>
To create a MiDocs account go to: <a href='$this->Url'>MiDocs</a>
<br>
<br>
If you already have a MiDocs account, please contact the provider above to confirm they have the correct spelling of your name and correct date of birth.
.</p>
</body>
</html>
";
        return $body;
    }

    public function requesterRequestBody($requester)
    {

        $body = "
<html>
<head>
</head>
<body>
<div>
<p><span style='text-transform: capitalize; font-weight:600;'>$requester[fname] $requester[lname]</span> wants to view your data on MiDocs, but your information is currently not available.  Sign up for MiDocs to easily share medical data on a secure platform with your healthcare providers and others of your choice.
<br>
<br>
To create a MiDocs account go to: <a href='$this->Url'>MiDocs</a>
<br>
<br>
If you already have a MiDocs account, please contact the provider above to confirm they have the correct spelling of your name and correct date of birth.
.</p>
</body>
</html>
";
        return $body;
    }

    public function requesterConfirmationBody($fname, $lname, $contact){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>A HIPAA release form has been sent to <span style='text-transform: capitalize; font-weight:600;'>$fname $lname</span> Once the signed release form is returned we will contact you at $contact.</p>
</body>
</html>
";
        return $body;
    }

    public function patientConfirmationBody($fname, $lname){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>We will send a HIPAA Form to your e-mail address on file.  Please complete this form and return it to us to allow release of your Protected Health Information to  <span style='text-transform: capitalize; font-weight:600;'>$fname $lname</span></p>
</body>
</html>
";
        return $body;
    }

    public function hipaaMessageBody(){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Attached is a Health Insurance Portability and Accountablity Act (HIPAA) form to release your Protected Health Information (PHI).  Please download this form, complete all sections and sign it.  Once signed, please save the form and return it as an attachment to HIPAA@countrymedicine.org.</p>
</body>
</html>
";
        return $body;
    }

    public function providerCancelationBody(){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Thank you for your response.  Access will NOT be granted at this time.</p>
</body>
</html>
";
        return $body;
    }

    public function requesterCancelationBody($type){
        if($type == 1){
            $message = "<p>The patient has denied this request, or time has elapsed.</p>";
        }else {
            $message = "<p>Thank you for your response.  Access will NOT be granted.</p>";
        }
        $body = "
<html>
<head>
</head>
<body>
<div>
$message
</body>
</html>
";
        return $body;
    }

    public function patientCancelationBody($fname, $lname,$qualification = "", $type = 1){
        $message = "<p>We will NOT release your information to  <span style='text-transform: capitalize; font-weight:600;'>$fname $lname</span>.  Thank you for your response.</p>";
        if($type == 2){
            $message = "<p>Thank you for your response.  Access to your chart will NOT be given to  <span style='text-transform: capitalize; font-weight:600;'>$fname $lname, $qualification</span></p>";
        }
        $body = "
<html>
<head>
</head>
<body>
<div>
$message
</body>
</html>
";
        return $body;
    }

    public function requesterConfirmation($email){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Thank you for your response.  Once we have the patient’s authorization on file we will contact you at $email.  Please allow at least 24 hours to grant access.</p>
</body>
</html>
";
        return $body;
    }

    public function newPatientCredentialsBody($data){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Username : $data[username]</p>
<br>
<p>Password : $data[newPassword]</p>
</body>
</html>
";
        return $body;
    }

    public function resetCancelationBody(){
        $body = "
<html>
<head>
</head>
<body>
<div>
<p>Thank you for your response, your UN/PW will NOT be re-set.</p>
</body>
</html>
";
        return $body;
    }


    /**
     * Send email to patient and a copy to the office
     * @param $body
     */
    public function sendReceipt($body, $email, $refId)
    {
        $mail = new PHPMailer(TRUE);
        try {
            $mail->SMTPDebug = 1;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $GLOBALS['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $GLOBALS['SMTP_USER'];
            $cryptoGen = new CryptoGen();
            $mail->Password = $cryptoGen->decryptStandard($GLOBALS['SMTP_PASS']);
            $mail->SMTPSecure = $GLOBALS['SMTP_SECURE'];
            $mail->Port = $GLOBALS['SMTP_PORT'];

            $mail->setFrom('billing@mindfultransitions.com', 'Billing Coordinator');
            $mail->addReplyTo('billing@mindfultransitions.com', 'Billing Coordinator');
            $mail->addBCC('john.jalbert@mindfultransitions.com', 'John Jalbert');
            $mail->addAddress($email, 'Client');
            //$mail->addEmbeddedImage('/images/receipt-logo.png', 'receipt-logo', 'receipt-logo.png');
            $mail->Subject = 'Mindful Transitions, LLC Transaction Receipt - Reference Number ' . $refId;
            $mail->Body = $body;

            $mail->send();
            return '<br><br>Message Sent. Please check email for results';
        } catch (Exception $e) {
            echo "Message could not be sent";
            echo "<pre>";
            echo "Mailer error: " . $mail->ErrorInfo;

        }
    }

    public function sendMessage($body, $email, $subject,$file = "")
    {
//        $email = "mahran996@gmail.com";
        $mail = new PHPMailer(TRUE);
        try {
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->IsHTML(true);
            $mail->Host = $this->Host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->Username;
            $cryptoGen = new CryptoGen();
            $mail->Password = $this->SMTPAuth;
            $mail->SMTPSecure = $this->SMTPSecure;
            $mail->Port = $this->Port;

            if($file !== ""){
                $mail->addAttachment($file,"HIPAA From.pdf");
            }
            $mail->setFrom($this->Username, 'MiDocs');
//            $mail->addReplyTo($email, 'MiDocs');
            $mail->addAddress($email, 'Client');
            $mail->Subject = $subject;
            $mail->Body = $body;

            if ($mail->send()) {
                return true;
            }
        } catch (Exception $e) {
            echo "Message could not be sent";
            echo "<pre>";
            echo "Mailer error: " . $mail->ErrorInfo;

        }
    }

}
