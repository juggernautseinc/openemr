<?php
$ignoreAuth = true;
require_once("../globals.php");

use OpenEMR\Common\Auth\AuthHash;
use OpenEMR\Common\Acl\AclExtended;
use OpenEMR\Events\User\UserCreatedEvent;
use OpenEMR\Common\Csrf\CsrfUtils;

class Provider
{


    public static function checkUser($username, $password)
    {
//        check username in users

        $query = "select username from users where username ='$username'";
        $data = sqlFetchArray(sqlStatement($query));
        $exist = 0;
        if(!empty($data)){
//            username exists
            $exist = 1;
        }

        return $exist;
    }

    public static function add($data)
    {
//        insert into users
//        insert into procedure_providers
        $csrf = $data['csrf_token'];
        $access_group[] = "Physicians";
        $username = $data['username'];
        $fname = $data['fname'];
        $lname = $data['lname'];
        $name = $data['practice_name'];
        $password = $data['password'];
        $qualification = $data['qualification'];
        $npi = $data['npi'];
        $email = $data['email'];
        $hash = new AuthHash('auth');
        $password = $hash->passwordHash($password);
        $verify = CsrfUtils::verifyCsrfToken($csrf);
        if($verify){
            $insertUsers = sqlInsert("INSERT INTO users (`username`,`password`,`authorized`,`fname`,`lname`,`email`,`email_direct`,`which_user`)VALUES ('$username', 'providerUser', 1, '$fname', '$lname','$email','$email', 2)");
            if ($insertUsers){
                $userId = $insertUsers;

//                insert into users_secure table
                $userSecure = sqlStatement("INSERT INTO users_secure (`id`,`username`,`password`) VALUES ($userId, '$username', '$password')");

                if($userSecure){
//                    insert into groups
                    $groups = sqlStatement("INSERT INTO groups (`name`,`user`) VALUES ('Default','$username')");

                    if($groups){
                        $midName = "";
                        $add = AclExtended::setUserAro($access_group,$username,$fname,$midName,$lname);
                        if($add){
                            $userCreatedEvent = new UserCreatedEvent($data);
                            $GLOBALS["kernel"]->getEventDispatcher()->dispatch(UserCreatedEvent::EVENT_HANDLE, $userCreatedEvent, 10);
                            sqlInsert("INSERT INTO procedure_providers (`name`,`npi`, `login`, `password`,`qualification`) VALUES ('$name',$npi,'$username','$password','$qualification') ");
                            return true;
                        }

                    }
                }
//                $inserProviders = sqlInsert("INSERT INTO procedure_providers (`name`,`npi`, `login`, `password`,`qualification`) VALUES ('$name',$npi,'$username','$password','$qualification') ");
//
//                if($inserProviders){
//                    $id = $inserProviders;
//                    $data['id'] = $id;
//                    return $data;
//                }
            }
        }

//        return false;
    }

    public static function getProvider($id){
        $data = sqlFetchArray(sqlStatement("SELECT * FROM procedure_providers WHERE ppid = $id "));
        return $data;
    }

    public static function getProviderByUsername($username){
        $data = sqlFetchArray(sqlStatement("SELECT * FROM procedure_providers WHERE login = '$username' "));
        return $data;
    }

    public static function getUserInfo($username){
        $data = sqlFetchArray(sqlStatement("select id, fname, lname, email from users where username = '$username' and which_user = 2"));
        return $data;
    }

    public static function updateCredentials($id){
        $providerData = self::getProvider($id);
        $username = $providerData['login'];
        $userInfo = self::getUserInfo($username);
        $userId = $userInfo['id'];
        $newPassword = $username."#123";
        $hash = new AuthHash('auth');
        $password = $hash->passwordHash($newPassword);

//        update password in users_secure and procedure_provider
        $updateSecure = sqlStatement("update users_secure set password = '$password' where id = $userId");
        $updateProvider = sqlStatement("update procedure_providers set password = '$password' where ppid = $id");
        if($updateProvider && $updateSecure){
            $data['username'] = $username;
            $data['newPassword'] = $newPassword;
            $data['email'] = $userInfo['email'];
            return $data;
        }

        return false;
    }
}
