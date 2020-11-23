<?php
$ignoreAuth = true;
require_once("../globals.php");

use OpenEMR\Common\Auth\AuthHash;
use OpenEMR\Common\Acl\AclExtended;
use OpenEMR\Events\User\UserCreatedEvent;
use OpenEMR\Common\Csrf\CsrfUtils;

class Other
{
//    check username and password exist
    public static function checkUser($username, $password)
    {
        $query = "SELECT username FROM users WHERE username = '$username'";
        $users = sqlFetchArray(sqlStatement($query));
        if(!empty($users)){
            return true;
        }
    }

    public static function add($data){
        $hash = new AuthHash('auth');
        $csrf = $data['csrf_token'];
        $verify = CsrfUtils::verifyCsrfToken($csrf);
        if($verify){
            $access_group[] = "Physicians";
            $username = $data['username'];
            $password = $hash->passwordHash($data['password']);
            $fname = $data['fname'];
            $lname = $data['lname'];
            $organization = $data['organization'];
            $email = $data['email'];
            $contact = $data['contact'];
            $preferred = $data['preferred_contact'];

            $insertUsers = sqlInsert("INSERT INTO users (`username`,`password`,`authorized`,`fname`,`lname`,`email`,`email_direct`,`which_user`)VALUES ('$username', 'otherUser', 1, '$fname', '$lname','$email','$email', 3)");
            if($insertUsers){
                $userId = $insertUsers;
                $userSecure = sqlStatement("INSERT INTO users_secure (`id`,`username`,`password`) VALUES ($userId, '$username', '$password')");

                if($userSecure){
                    $groups = sqlStatement("INSERT INTO groups (`name`,`user`) VALUES ('Default','$username')");
                    if($groups){
                        $midName = "";
                        $add = AclExtended::setUserAro($access_group,$username,$fname,$midName,$lname);
                        if($add){
                            $userCreatedEvent = new UserCreatedEvent($data);
                            $GLOBALS["kernel"]->getEventDispatcher()->dispatch(UserCreatedEvent::EVENT_HANDLE, $userCreatedEvent, 10);
                            sqlInsert("INSERT INTO other_users (`organization`,`fname`, `lname`, `username`, `password`, `email`, `contact`, `preferred_contact`) VALUES ('$organization','$fname', '$lname', '$username', '$password', '$email', '$contact', '$preferred')");
                            return true;
                        }

                    }
                }

//                $add = sqlInsert("INSERT INTO other_users (`organization`,`fname`, `lname`, `username`, `password`, `email`, `contact`, `preferred_contact`) VALUES ('$organization','$fname', '$lname', '$username', '$password', '$email', '$contact', '$preferred')");
//
//                if($add){
//                    $id = $add;
//                    $data['id'] = $id;
//                    return $data;
//                }
            }
        }
        return false;
    }

    public static function getData($id){
        $data = sqlFetchArray(sqlStatement("SELECT * FROM other_users WHERE id = $id"));
        return $data;
    }

    public static function getDateByUsername($username){
        $data = sqlFetchArray(sqlStatement("SELECT * FROM other_users WHERE username = '$username'"));
        return $data;
    }

    public static function availableUser($data){
//        print_r($data);die;
        $csrf = $data['csrf_token'];
        $verify = CsrfUtils::verifyCsrfToken($csrf);
        if($verify){
            if(!empty($data['email'])){
                $fname = $data['fname'];
                $lname = $data['lname'];
                $email = $data['email'];

                $available = sqlFetchArray(sqlStatement("SELECT id, which_user, email, username FROM users WHERE fname='$fname' AND lname = '$lname' AND email = '$email' "));
                if(!empty($available)){
//                send new credentials to the user
                    $send = self::existUser($available);
                    if($send){
                        return true;
                    }
                }
            }else{
                $fname = $data['fname'];
                $lname = $data['lname'];
                $phone = $data['cell'];
                $available = sqlFetchArray(sqlStatement("SELECT id, which_user, email, username FROM users WHERE fname='$fname' AND lname = '$lname' AND phone = '$phone' "));
                if(!empty($available)){
//                send new credentials to the user
                    $send = self::existUser($available);
                    if($send){
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function getUserInfo($username){
        $data = sqlFetchArray(sqlStatement("select id,fname,lname,email from users where which_user = 3 and username = '$username'"));
        return $data;
    }

    private function existUser($data){
        $email = $data['email'];
        $whichUser = $data['which_user'];
        $username = $data['username'];
        $currentTime = time();
        $secondsToAdd = 1 * (60 * 60);
        $time = $currentTime + $secondsToAdd;
        if($whichUser == 1){
//            patient
            $patientData = Patient::getDataByUsername($username);
            $patientId = $patientData['pid'];
            $title = "toPatientReset";
            $addEmail = sqlInsert("insert into email_response (`patient_id`, `title`, `date`) VALUES ($patientId, '$title', $time)");
            if($addEmail){
//                send email to patient
                $send = EmailResponse::userResetConfirmation($email,$addEmail);
            }
        }else if($whichUser == 2){
//            provider
            $providerData = Provider::getProviderByUsername($username);
            $providerId = $providerData['ppid'];
            $title = "toProviderReset";
            $addEmail = sqlInsert("insert into email_response (`provider_id`, `title`, `date`) VALUES ($providerId, '$title', $time)");
            if($addEmail){
//                send email to provider
                $send = EmailResponse::userResetConfirmation($email,$addEmail);
            }
        }else if($whichUser == 3){
//            requester
            $requesterData = self::getDateByUsername($username);
            $requesterId = $requesterData['id'];
            $title = "toRequesterReset";
            $addEmail = sqlInsert("insert into email_response (`requester_id`, `title`, `date`) VALUES ($requesterId, '$title', $time)");
            if($addEmail){
//                send email to provider
                $send = EmailResponse::userResetConfirmation($email,$addEmail);
            }
        }

        if($send){
            return true;
        }
    }

    public static function updateCredentials($id){
//        update password in other_users and users_secure
        $requesterData = self::getData($id);
        $username = $requesterData['username'];
        $userInfo = self::getUserInfo($username);
        $userId = $userInfo['id'];
        $newPassword = $username."#123";
        $hash = new AuthHash('auth');
        $password = $hash->passwordHash($newPassword);

        $updateUsersSecure = sqlStatement("update users_secure set password = '$password' where id = $userId");
        $updateOther = sqlStatement("update other_users set password = '$password' where username = '$username'");
        if($updateOther && $updateUsersSecure){
            $data['username'] = $username;
            $data['newPassword'] = $newPassword;
            $data['email'] = $userInfo['email'];

            return $data;
        }

        return false;
    }
}
