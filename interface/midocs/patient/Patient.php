<?php
$ignoreAuth = true;
require_once("../globals.php");

use OpenEMR\Common\Auth\AuthHash;
use OpenEMR\Common\Csrf\CsrfUtils;

class Patient
{

    public static function insertPatient($data)
    {
        $csrf = $data['csrf_token'];
        $f_name = $data["f_name"];
        $lname = $data["l_name"];
        $sex = $data["sex"];
        $dob = date("Y-m-d", strtotime($data['dob']));
        $email = $data["email"];
        $phone = $data["number"];
        $username = $data["username"];
        $password = $data["password"];
        $preffered_contact = $data["preferred_contact"];
        $d['error'] = true;

        $sql = sqlStatement("SELECT pid FROM patient_data ORDER BY id DESC LIMIT 1");
        $patient_data = sqlFetchArray($sql);
        $pid = 1 + $patient_data['pid'];

        $hash = new AuthHash('auth');
        $password = $hash->passwordHash($password);

//    validate
        $validate = self::validate($username, $data["password"]);
        if ($validate) {
            $verify = CsrfUtils::verifyCsrfToken($csrf);
            if ($verify) {
                //    insert users
                $insert_users = sqlStatement("INSERT INTO users (`username`,`password`,`authorized`,`fname`,`lname`,`email`,`email_direct`,`which_user`)VALUES ('$username', 'patientUser', 1, '$f_name', '$lname', '$email', '$email', 1)");
                //        insert into patient data
                if ($insert_users) {
                    if ($preffered_contact == "email") {
                        $insert_patient_data = sqlStatement("INSERT INTO patient_data (`fname`, `lname`, `sex`, `DOB`, `email`, `email_direct`,`phone_cell` ,`pubpid`, `pid`,`allow_patient_portal`, `hipaa_allowemail`) values('$f_name','$lname', '$sex', '$dob', '$email', '$email', '$phone', '$pid' ,'$pid', 'YES', 'YES')");
                    } else {
                        $insert_patient_data = sqlStatement("INSERT INTO patient_data (`fname`, `lname`, `sex`, `DOB`, `email`, `email_direct`,`phone_cell`, `pubpid`, `pid`,`allow_patient_portal`, `hipaa_allowsms`) values('$f_name','$lname', '$sex', '$dob', '$email', '$email', '$phone','$pid', '$pid', 'YES', 'YES')");
                    }
                    if ($insert_patient_data) {
//            insert into patient_access_onsite
                        sqlStatement("INSERT INTO patient_access_onsite (`pid`,`portal_username`, `portal_pwd`, `portal_pwd_status`, `portal_login_username`)VALUES ('$pid', '$username', '$password', 1, '$username')");
                        $d['error'] = false;
                        $d['pid'] = $pid;
                        $d['username'] = $username;
                    }
                }
            }
        }
        return $d;
    }

    public static function updateCredentials($id){
        $data = self::getPatientDataById($id,1);
        $username = $data['portal_username'];
        $newPassword = $username."#123";
        $hash = new AuthHash('auth');
        $password = $hash->passwordHash($newPassword);

//        update password in patient_access_onsite
        $update = sqlStatement("update patient_access_onsite set portal_pwd = '$password' where pid = $id");
        if($update){
            $newData['username'] = $username;
            $newData['newPassword'] = $newPassword;
            return $newData;
        }

        return false;

    }

    public function validate($username, $password)
    {
        $query = "select id, portal_pwd from patient_access_onsite where portal_username = '$username'";

        $data = sqlFetchArray(sqlStatement($query));

        if (!empty($data)) {
            if (AuthHash::passwordVerify($password, $data['portal_pwd'])) {
                return false;
            }

            return true;
        }

        return true;

    }

    public static function getPatientData($id)
    {
        $query = "select fname,lname,pid,providerID,email from patient_data where pid = $id";

        $data = sqlFetchArray(sqlStatement($query));
        return $data;
    }

    public static function getPatientDataById($id,$type = 0)
    {
        if($type == 0){
            $query = "select fname,lname,pid,providerID,email from patient_data where id = $id";
        }else{
            $query = "select * from patient_access_onsite where pid = $id";
        }

        $data = sqlFetchArray(sqlStatement($query));
        return $data;
    }

    public static function getDataByUsername($username){
        $data = sqlFetchArray(sqlStatement("select * from patient_access_onsite where portal_username='$username'"));
        return $data;
    }

    public static function sendMail($data, $user = "provider")
    {
        $csrf = $data['csrf_token'];
        $fname = $data['fname'];
        $lname = $data['lname'];
        $sex = $data['sex'];
        $dob = $data['dob'];
        $username = $_SESSION['authUser'];
        $id = $_SESSION['authUserID'];
        $organization = $_SESSION['organization'];

        $providerTitle = "toProvider";
        $patientTitle = "toPatient";
        $requesterTitle = "toRequester";

        $currentTime = time();
        $secondsToAdd = 1 * (60 * 60);
        $providerTime = $currentTime + $secondsToAdd;

        $secondsToAdd = 24 * (60 * 60);
        $patientTime = $currentTime + $secondsToAdd;

        $query = "SELECT * FROM patient_data WHERE `fname` = '$fname' AND `lname` = '$lname' AND `dob` = '$dob' AND `sex` = '$sex'";
        $data = sqlFetchArray(sqlStatement($query));
        $verify = CsrfUtils::verifyCsrfToken($csrf);
        if($verify){
            if (!empty($data)) {
                $patientId = $data['id'];

                if ($user == "provider") {
//            get provider data

                    $providerData = sqlFetchArray(sqlStatement("SELECT ppid,qualification FROM procedure_providers WHERE login = '$username'"));
                    $userData = sqlFetchArray(sqlStatement("SELECT fname,lname,email FROM users WHERE id = $id AND which_user = 2"));

                    if ($providerData && $userData) {
                        //            store data in email_response
                        $proivderEmailId = sqlInsert("INSERT INTO email_response (`provider_id`,`patient_id`,`title`,`date`) VALUES ($providerData[ppid],$patientId,'$providerTitle','$providerTime') ");
                        $patientEmailId = sqlInsert("INSERT INTO email_response (`provider_id`,`patient_id`,`title`,`date`) VALUES ($providerData[ppid],$patientId,'$patientTitle','$patientTime') ");
//                    send message
                        $send2 = EmailResponse::sendRequest(2, $data['email'], $patientEmailId, $userData['fname'], $userData['lname'], $providerData['qualification']);
                        $send3 = EmailResponse::sendRequest(3, $userData['email'], $proivderEmailId, $fname, $lname);
                        if ($send2 && $send3) {
                            return true;
                        }
                    }
                } else {
//              hipaa pdf form
                    $hipaaForm = "hipaa_form.pdf";

//                get requester data
                    $requesterUserInfo = Other::getUserInfo($username);
                    $requesterData = Other::getDateByUsername($username);
                    if ($requesterUserInfo) {
                        //            store data in email_response
                        $requesterEmailId = sqlInsert("INSERT INTO email_response (`requester_id`,`patient_id`,`title`,`date`) VALUES ($requesterData[id],$patientId,'$requesterTitle','$providerTime') ");
                        $patientEmailId = sqlInsert("INSERT INTO email_response (`requester_id`,`patient_id`,`title`,`date`) VALUES ($requesterData[id],$patientId,'$patientTitle','$patientTime') ");
//                    send message
                        $send4 = EmailResponse::sendRequest(4, $data['email'], $patientEmailId, $requesterUserInfo['fname'], $requesterUserInfo['lname'], "", $organization);
                        $send7 = EmailResponse::sendRequest(7, $requesterUserInfo['email'], $requesterEmailId, $data['fname'], $data['lname']);

                        if ($send4 && $send7) {
                            return true;
                        }
                    }
                }
            }
        }


        return false;
    }

    public function createPpa($id, $providerId)
    {
        $update = sqlStatement("update patient_data set providerID = $providerId where id = $id");
        if ($update) {
            return true;
        }

        return false;
    }

    public static function verifyCsrfToken($token){
        if(CsrfUtils::verifyCsrfToken($token)){
            return true;
        }

        return false;
    }
}
