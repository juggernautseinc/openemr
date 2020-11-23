<?php
//session_start();
require_once("patient/Patient.php");
require_once("provider/Provider.php");
require_once("other/Other.php");
require_once("EmailResponse.php");

use OpenEMR\Common\Csrf\CsrfUtils;


// patient username password check if those are exist or not.
if (isset($_POST['form']) && $_POST['form'] == "patient_check") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $validate = new Patient();
    if (!$validate->validate($username, $password)) {
        $d['error'] = 1;
    } else {
        $d['error'] = 0;
    }

    echo json_encode($d);
}

// add new patient
if (isset($_POST['form']) && $_POST['form'] == "patient_create") {
    $data = Patient::insertPatient($_POST);
    if (empty($data["error"])) {
        $pid = $data["pid"];
        $username = $data["username"];

        $set = setSession($pid, $username);
        if ($set) {
            $d['error'] = 0;
            $d['username'] = $username;
        }
    } else {
        $d['error'] = 1;
    }

    echo json_encode($d);
}

// check provider user
if (isset($_POST['form']) && $_POST['form'] == "check_provider") {
    $d['error'] = 1;
    $username = $_POST['username'];
    $password = $_POST['password'];
    $check = Provider::checkUser($username, $password);
    if (!$check) {
        $d['error'] = 0;
    }

    echo json_encode($d);
}

// add new provider
if (isset($_POST['form']) && $_POST['form'] == "create_provider") {
    $d['error'] = 1;

    $add = Provider::add($_POST);
    if ($add) {
        $d['error'] = 0;
        $d['password'] = $_POST['password'];
        $d['authUser'] = $_POST['username'];
    }

    echo json_encode($d);
}

// provider request form
if (isset($_POST['form']) && $_POST['form'] == "provider-request-form") {
    $d['error'] = 0;
//    check if user is exists a mail will send else return false
    $check = Patient::sendMail($_POST);
    if (!$check) {
//        not exists
        $d['error'] = 1;
    }

    $_SESSION['patient_fname'] = $_POST['fname'];
    $_SESSION['patient_lname'] = $_POST['lname'];
    echo json_encode($d);
}

// other request form
if (isset($_POST['form']) && $_POST['form'] == "other-request-form") {
    $d['error'] = 0;
//    check if user is exists a mail will send else return false
    $check = Patient::sendMail($_POST, "requester");
    if (!$check) {
//        not exists
        $d['error'] = 1;
    }

    echo json_encode($d);
}

// patient contact form
if (isset($_POST['form']) && $_POST['form'] == "patient-contact-form") {
//    send mail
    $d['send'] = 0;
    $email = $_POST['email'];
    $username = $_SESSION['authUser'];
    $csrf_token = $_POST['csrf_token'];
    if(Patient::verifyCsrfToken($csrf_token)){
        $provider = Provider::getUserInfo($username);
        $qualification = Provider::getProviderByUsername($username);
        $send = EmailResponse::requestPatient($provider, $qualification['qualification'], $email);
        if ($send) {
            $d['send'] = 1;
        }
    }
    echo json_encode($d);
}

if (isset($_POST['form']) && $_POST['form'] == "patientRequester-contact-form") {
//    send mail
    $d['send'] = 0;
    $email = $_POST['email'];
    $username = $_SESSION['authUser'];
    $csrf_token = $_POST['csrf_token'];
    if(Patient::verifyCsrfToken($csrf_token)){
        $requesterData = Other::getUserInfo($username);
        $send = EmailResponse::requestPatient($requesterData, "", $email, "requester");
        if ($send) {
            $d['send'] = 1;
        }
    }
    echo json_encode($d);
}

// checkother username and password
if (isset($_POST['form']) && $_POST['form'] == "create-other-form" && isset($_POST['csrf_token'])) {
    $d['error'] = 0;
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = Other::checkUser($username, $password);
    if ($check) {
        $d['error'] = 1;
    }

    echo json_encode($d);
}

// add new other user
if (isset($_POST['form']) && $_POST['form'] == "other-submit") {
    $d['error'] = 1;
    $add = Other::add($_POST);
    if ($add) {
        $d['error'] = 0;
    }
    echo json_encode($d);
}
// reset form
if (isset($_POST['form']) && $_POST['form'] == "reset-form") {
    $d['available'] = 0;
//    if the user exists the request email will send to
    $send = Other::availableUser($_POST);
    if ($send) {
//        send email to the contact
        $d['available'] = 1;
    }

    echo json_encode($d);
}

if (isset($_GET['confirm_access']) && !isset($_GET['user'])) {
    $emailId = $_GET['confirm_access'];

    $responseData = EmailResponse::toWhom($emailId);
    $providerId = $responseData['provider_id'];
    $patientId = $responseData['patient_id'];

    $providerData = Provider::getProvider($providerId);
    $username = $providerData['login'];
    $qulification = $providerData['qualification'];

    $userInfo = Provider::getUserInfo($username);
    $fname = $userInfo['fname'];
    $lname = $userInfo['lname'];

    if ($responseData['title'] == "toProvider") {
//        provider allows
        $allow = EmailResponse::allow('provider', $emailId);
        $email = Provider::getUserInfo($username);
        $providerEmail = $email['email'];
        if ($allow) {
//            message 14
            $send = EmailResponse::providerAllowed($providerEmail);
            if ($send) {
//                here want to check the patient allowed or not
                $check = EmailResponse::checkAllow("patient", $providerId, $patientId);
            }

        } else {
//            send message 15
            $send = EmailResponse::message15($providerEmail);
            if ($send) {
//                close window
            }
        }
    } else {
//        patient allows
        $allow = EmailResponse::allow('patient', $emailId);
        $patient = Patient::getPatientDataById($patientId);
        $patientEmail = $patient['email'];
        if ($allow) {
            //            send message 12
            $send = EmailResponse::patientAllowed($patientEmail, $fname, $lname, $qulification);
            if ($send) {
                //                here want to check the provider allowed or not
                $check = EmailResponse::checkAllow("provider", $providerId, $patientId);
            }
        } else {
//            message 13
            $send = EmailResponse::message13($fname, $lname, $patientEmail, $qualification);
            if ($send) {
//                window close
            }
        }
    }

    if ($check) {
        //                create ppa
        $create = Patient::createPpa($patientId, $userInfo['id']);
        if ($create) {
            //                if allowed send message 18
            $sendAllowed = EmailResponse::allowedMessage($patientId, $userInfo['id'], $qulification);
            if ($sendAllowed) {
                echo "<script>window.close()</script>";
            }
        }
    } else {
        echo "<script>window.close()</script>";
    }

}

if (isset($_GET['user']) && $_GET['user'] == "requester" && $_GET['email_id']) {
    $emailId = $_GET['email_id'];
    $responseData = EmailResponse::toWhom($_GET['email_id']);
    $requesterId = $responseData['requester_id'];
    $patientId = $responseData['patient_id'];

    $requesterData = Other::getData($requesterId);
    $username = $requesterData['username'];
    $organization = $requesterData['organization'];

    $userInfo = Other::getUserInfo($username);
    $fname = $userInfo['fname'];
    $lname = $userInfo['lname'];

    $patientData = Patient::getPatientDataById($patientId);

    if ($responseData['title'] == "toRequester") {
//        provider allows
        $allow = EmailResponse::allow('requester', $emailId);
        if ($allow) {
//            message 8
            $send8 = EmailResponse::requesterConfirmation($requesterData['email']);
            if ($send8) {
                echo "<script>window.close()</script>";
            }
        } else {
//            message 9
            $send = EmailResponse::message9($requesterData['email']);
            if ($send) {
                echo "<script>window.close()</script>";
            }
        }
    } else {
//        patient allows
        $allow = EmailResponse::allow('patient', $emailId);
        if ($allow) {
//            message 5
            $send5 = EmailResponse::patientConfirmation($requesterData['email'], $patientData['fname'], $patientData['lname'], $requesterData['email']);
//            message 10
            $send10 = EmailResponse::requesterAccessConfirmation($patientData['email'], $requesterData['fname'], $requesterData['lname']);
//            message HIPAA
            $hipaaMsg = EmailResponse::hipaaMessage($patientData['email']);

            if ($send5 && $send10 && $hipaaMsg) {
                echo "<script>window.close()</script>";
            }
        } else {
//            message 6
            $send6 = EmailResponse::message6($requesterData['email']);
//            message 11
            $send11 = EmailResponse::message11($requesterData['fname'], $requesterData['lname'], $patientData['email']);
            if ($send6 && $send11) {
                echo "<script>window.close()</script>";
            }
        }
    }
}

// email access canceled by patient
if (isset($_GET['email_cancel']) && isset($_GET['user']) && $_GET['user'] == "patient" || $_GET['user'] == "provider") {
    $email_id = $_GET['email_cancel'];
    if (!isset($_GET['to'])) {
        $user = "requester";

        $send = EmailResponse::patientEmailCancelation($email_id, $user);
        if ($send) {
            echo "<script>window.close()</script>";
        }
    } else {
        $user = $_GET['to'];
        if ($user == "provider") {
            $send = EmailResponse::patientEmailCancelation($email_id, $user);
            if ($send) {
                echo "<script>window.close()</script>";
            }
        } else {
            $send = EmailResponse::patientEmailCancelation($email_id, $user);
            if ($send) {
                echo "<script>window.close()</script>";
            }
        }
    }
}


// email access canceled by requester
if (isset($_GET['email_cancel']) && isset($_GET['user']) && $_GET['user'] == "requester") {
    $email_id = $_GET['email_cancel'];
    $user = "requester-2";

    $send = EmailResponse::patientEmailCancelation($email_id, $user);
    if ($send) {
        echo "<script>window.close()</script>";
    }
}

// email reset username / password
if (isset($_GET['password_reset'])) {
    $emailId = $_GET['password_reset'];
    $toWhom = EmailResponse::toWhom($emailId);
    $currentTime = time();
    $sentTime = $toWhom['date'];
    if ($sentTime > $currentTime) {
        if ($toWhom['title'] == "toPatientReset") {
            $changeCredential = Patient::updateCredentials($toWhom['patient_id']);
            $patientData = Patient::getPatientData($toWhom['patient_id']);
            if ($changeCredential) {
//            send new credentials to the patient
                $sendNewCredentials = EmailResponse::sendNewPatientCredentials($changeCredential, $patientData['email']);
                if ($sendNewCredentials) {
                    echo "<script>window.close()</script>";
                }
            }
        } else if ($toWhom[title] == "toProviderReset") {
            $providerId = $toWhom['provider_id'];
            $changeCredentials = Provider::updateCredentials($providerId);
            if ($changeCredentials) {
                //            send new credentials to the provider
                $sendNewCredentials = EmailResponse::sendNewPatientCredentials($changeCredentials, $changeCredentials['email']);
                if ($sendNewCredentials) {
                    echo "<script>window.close()</script>";
                }
            }
        } else {
//        to requester
            $requesterId = $toWhom['requester_id'];
            $changeCredentials = Other::updateCredentials($requesterId);
            if ($changeCredentials) {
                //            send new credentials to the provider
                $sendNewCredentials = EmailResponse::sendNewPatientCredentials($changeCredentials, $changeCredentials['email']);
                if ($sendNewCredentials) {
                    echo "<script>window.close()</script>";
                }
            }
        }
    } else {
//        message 17
        if ($toWhom['title'] == "toPatientReset") {
            $patientData = Patient::getPatientData($toWhom['patient_id']);
            $send = EmailResponse::resetCancelaion($patientData['email']);
        } else if ($toWhom[title] == "toProviderReset") {
            $providerId = $toWhom['provider_id'];
            $providerData = Provider::getProvider($providerId);
            $username = $providerData['login'];
            $userInfo = Provider::getUserInfo($username);
            $send = EmailResponse::resetCancelaion($userInfo['email']);
        } else {
//        to requester
            $requesterId = $toWhom['requester_id'];
            $requesterData = Other::getData($requesterData);
            $send = EmailResponse::resetCancelaion($requesterData['email']);
        }

        if ($send) {
            echo "<script>window.close()</script>";
        }
    }
}

if (isset($_GET['cancel_reset'])) {
//    cancel reset credentials
    $emailId = $_GET['password_reset'];
    $toWhom = EmailResponse::toWhom($_GET['cancel_reset']);
    if ($toWhom['title'] == "toPatientReset") {
        $patientData = Patient::getPatientData($toWhom['patient_id']);
        $email = $patientData['email'];
        $send = EmailResponse::resetCancelaion($email);
    } else if ($toWhom['title'] == "toProviderReset") {
        $providerData = Provider::getProvider($toWhom['provider_id']);
        $username = $providerData['login'];
        $userInfo = Provider::getUserInfo($username);
        $email = $userInfo['email'];
        $send = EmailResponse::resetCancelaion($email);
    } else {
        $requesterData = Other::getData($toWhom['requester_id']);
        $email = $requesterData['email'];
        $send = EmailResponse::resetCancelaion($email);
    }

    if ($send) {
        echo "<script>window.close()</script>";
    }
}


function setSession($id, $username, $for = "patient")
{
    if ($for == "patient") {
        session_start();
        $data = Patient::getPatientData($id);
        $userData = $data;
        unset($_SESSION['password_update']);
        unset($_SESSION['itsme']);

        $_SESSION['portal_username'] = $username;
        $_SESSION['portal_login_username'] = $username;
        $_SESSION['pid'] = $userData['pid'];
        $_SESSION['patient_portal_onsite_two'] = 1;
        $_SESSION['providerName'] = "";
        $_SESSION['providerUName'] = "";
        $_SESSION['sessionUser'] = '-patient-';
        $_SESSION['providerId'] = $userData['providerID'] ? $userData['providerID'] : 'undefined';
        $_SESSION['ptName'] = $userData['fname'] . ' ' . $userData['lname'];
        $_SESSION['authUser'] = 'portal-user';
        // Set up the csrf private_key (for the paient portal)
        //  Note this key always remains private and never leaves server session. It is used to create
        //  the csrf tokens.
        CsrfUtils::setupCsrfKey();

        $myfile = fopen("../main/session/patients/$username.txt", "w");
        fwrite($myfile, session_encode() . "\n");
        if (fclose($myfile)) {
            return true;
        }
    } else if ($for == "provider") {
        $data = Provider::getProvider($id);
        $_SESSION['username'] = $data['login'];
        $_SESSION['practice'] = $data['name'];
        $myfile = fopen("../main/session/providers/$username.txt", "w");
        fwrite($myfile, session_encode() . "\n");
        if (fclose($myfile)) {
            return true;
        }
    } else {
        $data = Other::getData($id);
        $_SESSION['username'] = $username;
        $_SESSION['organization'] = $data['organization'];
        CsrfUtils::setupCsrfKey();
        $myfile = fopen("../main/session/other/$username.txt", "w");
        fwrite($myfile, session_encode() . "\n");
        if (fclose($myfile)) {
            return true;
        }
    }

    return false;
}
