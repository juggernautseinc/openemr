<?php
$ignoreAuth = true;
require_once("../globals.php");

use OpenEMR\Common\Email\EmailCustomerReceipt;

class EmailResponse
{
    public static function toWhom($id){
        $emailResponse = sqlFetchArray(sqlStatement("SELECT provider_id,requester_id,patient_id,title,date FROM email_response WHERE id = $id"));
        return $emailResponse;
    }

    public static function allow($user,$id){
//            check time
        $allowTime = self::checkAllowTime($id);
        if($allowTime){
//                allow
            $allow = sqlStatement("update email_response set allow = 1 where id = $id");
            if($allow){
                return true;
            }
        }

        return false;
    }

    private function checkAllowTime($id){
        $currTime = time();
        $enteredTime = sqlFetchArray(sqlStatement("select date from email_response where id = $id"));
        if($currTime < $enteredTime['date']){
            return true;
        }

        return false;
    }



    public static function sendRequest($message, $email, $emailId, $fname, $lname, $qualification = "", $organization = "")
    {
        $date = date("l jS \of F Y");
        $mail = new EmailCustomerReceipt();

        if($message == 2){
//            provider sends email to patient.
            $body = $mail->patientAccessBody($emailId, $fname, $lname, $qualification, $date);
            $subject = "Allow Access.";
            $send = $mail->sendMessage($body, $email, $subject);
            if ($send) {
                return true;
            }
        }else if($message == 3){
//            proivder sends email to his contact.
            $body = $mail->providerAccessBody($emailId, $fname, $lname, $qualification, $date);
            $subject = "Allow Access.";
            $send = $mail->sendMessage($body, $email,$subject);
            if ($send) {
                return true;
            }
        }else if($message == 4){
//            requester sends email to patient.
            $body = $mail->patientAccessBody1($emailId, $fname, $lname, $organization, $date);
            $subject = "Allow Access.";
            $send = $mail->sendMessage($body, $email, $subject);
            if ($send) {
                return true;
            }

        }else if($message == 7){
//            requester send email to his contact.
            $body = $mail->requesterAccessBody($emailId, $fname, $lname, $date);
            $subject = "Allow Access.";
            $send = $mail->sendMessage($body, $email,$subject);
            if ($send) {
                return true;
            }
        }

        return false;
    }

    public static function patientAllowed($email,$fname,$lname,$qulification){
//        message 12
        $mail = new EmailCustomerReceipt();
        $body = $mail->patientAllowedBody($fname,$lname,$qulification);
        $subject = "Confirmation Message";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }

        echo "something went wrong";
    }

    public static function providerAllowed($email){
//        message 14
        $mail = new EmailCustomerReceipt();
        $body = $mail->providerAllowedBody();
        $subject = "Confirmation Message";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }

        echo "something went wrong";
    }

    public static function allowedMessage($patientId,$providerUserId,$qualification){
//        message 18
        $patientsData = sqlFetchArray(sqlStatement("select fname, lname, email from patient_data where id = $patientId"));
        $providerData = sqlFetchArray(sqlStatement("select fname, lname, email from users where id = $providerUserId"));

        if($patientsData && $providerData){
            $mail = new EmailCustomerReceipt();
            $body = $mail->allowedMessage($providerData,$patientsData,$qualification);
            $subject = "Successfully Accessed";
            $sendToProvider = $mail->sendMessage($body,$providerData['email'], $subject);
            $sendToPatient = $mail->sendMessage($body,$patientsData['email'], $subject);

            if($sendToProvider && $sendToPatient){
                return true;
            }

        }
    }

    public static function checkAllow($user,$provider_id,$patient_id){
        if($user == "provider"){
            $check = sqlFetchArray(sqlStatement("select allow from email_response where provider_id = $provider_id and patient_id = $patient_id and title = 'toProvider' and allow = 1"));
            if($check){
                return true;
            }
        }else{
            $check = sqlFetchArray(sqlStatement("select allow from email_response where provider_id = $provider_id and patient_id = $patient_id and title = 'toPatient' and allow = 1"));
            if($check){
                return true;
            }
        }

        return false;
    }

    public static function requestPatient($userData,$qualification,$email,$user = "provider"){
        $mail = new EmailCustomerReceipt();
        if($user == "provider"){
            //        message 19
            $body = $mail->providerRequestBody($userData,$qualification);
            $subject = "Provider Request";
            $send = $mail->sendMessage($body,$email, $subject);
            if($send){
                return true;
            }
        }else{
            //        message 16
            $body = $mail->requesterRequestBody($userData);
            $subject = "Requester Request";
            $send = $mail->sendMessage($body,$email, $subject);
            if($send){
                return true;
            }
        }
    }

    public function patientConfirmation($email,$fname,$lname,$contact){
//        send to requester from patient (message 5)
        $mail = new EmailCustomerReceipt();
        $body = $mail->requesterConfirmationBody($fname,$lname,$contact);
        $subject = "Confirmation From Patient.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public function requesterAccessConfirmation($email,$fname,$lname){
//        to patient (message 10)
        $mail = new EmailCustomerReceipt();
        $body = $mail->patientConfirmationBody($fname,$lname);
        $subject = "Confirmation From MiDocs.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public function hipaaMessage($email){
//        to patient (message 10)
        $file = "hipaa_form.pdf";
        $mail = new EmailCustomerReceipt();
        $body = $mail->hipaaMessageBody();
        $subject = "HIPAA Document.";
        $send = $mail->sendMessage($body,$email, $subject,$file);
        if($send){
            return true;
        }
    }

    public function patientEmailCancelation($email_id,$user){
//        $mail = new EmailCustomerReceipt();
//        $subject = "Request Cancelation.";
        $cancel = sqlStatement("update email_response set allow = 0 where id = $email_id");
        $emailData = sqlFetchArray(sqlStatement("select * from email_response where id = $email_id"));
        if($cancel){
            $patientData = Patient::getPatientDataById($emailData['patient_id']);
            if($user == "requester"){
                $requesterData = Other::getData($emailData['requester_id']);
//                send cancelation message to requester (message 6)
                $send6 = self::message6($requesterData['email']);

//                send message to patient (message 11)
                $send11 = self::message11($requesterData['fname'],$requesterData['lname'],$patientData['email']);

                if($send6 && $send11){
                    return true;
                }
            }else if($user == "requester-2"){
//                  send message to requester (message 9)
                $requesterData = Other::getData($emailData['requester_id']);
                $send9 = self::message9($requesterData['email']);
                if($send9){
                    return true;
                }
            }else if($user == "provider"){
//                send message 13
                $providerData = Provider::getProvider($emailData['provider_id']);
                $username = $providerData['login'];
                $qualification = $providerData['qualification'];
                $userInfo = Provider::getUserInfo($username);

                $send13 = self::message13($userInfo['fname'],$userInfo['lname'],$patientData['email'],$qualification);
                if($send13){
                    return true;
                }
            }else{
//                send message 15
                $providerData = Provider::getProvider($emailData['provider_id']);
                $username = $providerData['login'];
                $userInfo = Provider::getUserInfo($username);

                $send15 = self::message15($userInfo['email']);

                if($send15){
                    return true;
                }
            }
        }

        return false;
    }

    public static function message11($fname,$lname,$pateintEmail){
//        send message 11
        $mail = new EmailCustomerReceipt();
        $subject = "Request Cancelation.";
        $body = $mail->patientCancelationBody($fname,$lname);
        $send = $mail->sendMessage($body,$pateintEmail, $subject);
        if($send){
            return true;
        }
    }

    public static function message6($email){
//        send message 6
        $mail = new EmailCustomerReceipt();
        $subject = "Request Cancelation.";
        $body = $mail->requesterCancelationBody(1);
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function message9($email){
//        send message 9
        $mail = new EmailCustomerReceipt();
        $subject = "Request Cancelation.";
        $body = $mail->requesterCancelationBody(2);
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function message13($fname,$lname,$patientEmail,$qualification,$type = 2){
        $mail = new EmailCustomerReceipt();
        $subject = "Request Cancelation.";
        $body = $mail->patientCancelationBody($fname,$lname,$qualification,2);
        $send = $mail->sendMessage($body,$patientEmail, $subject);
        if($send){
            return true;
        }
    }

    public static function message15($email){
//        message 15 (to provider)
        $mail = new EmailCustomerReceipt();
        $subject = "Request Cancelation.";
        $body = $mail->providerCancelationBody();
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function requesterConfirmation($email){
//        to requester (message 8)
        $mail = new EmailCustomerReceipt();
        $body = $mail->requesterConfirmation($email);
        $subject = "Confirmation.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function userResetConfirmation($email,$emailId){
//        to user (message 1)
        $mail = new EmailCustomerReceipt();
        $body = $mail->resetConfirmationBody($emailId);
        $subject = "Reset Password Confirmation.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function sendNewPatientCredentials($data,$email){
        $mail = new EmailCustomerReceipt();
        $body = $mail->newPatientCredentialsBody($data);
        $subject = "New Credentials.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }

    public static function resetCancelaion($email){
//        message 17
        $mail = new EmailCustomerReceipt();
        $body = $mail->resetCancelationBody();
        $subject = "Credentials Reset Canceled.";
        $send = $mail->sendMessage($body,$email, $subject);
        if($send){
            return true;
        }
    }
}
