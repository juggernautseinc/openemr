<?php

use OpenEMR\Core\Header;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../globals.php");
?>


<html>
<head>
    <?php
    Header::setupHeader();
//        print_r($_SESSION);die;
    ?>

    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>
</head>
<body>
<div class="container-fluid mt-2">
    <div class="text-center text-uppercase"><h2><?php echo $_SESSION["username"]; ?></h2></div>

    <div class="row">
        <div class="col-md-2">
            <button class="btn btn-outline-secondary">Menu</button>
        </div>
    </div>

    <div class="row mt-2">

        <div class="col-8 offset-2">
            <div class="card">
                <div class="card-header">Profile Information</div>
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label class="col-md-2">
                                First Name
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" id="firstname" readonly value="<?php echo $_SESSION['f_name'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2">
                                Last Name
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" id="lname" readonly value="<?php echo $_SESSION['l_name'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2">
                                Email
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" id="email" readonly value="<?php echo $_SESSION['email'] ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2">
                                Phone
                            </label>
                            <div class="col-md-6">
                                <input class="form-control" id="phone" readonly value="<?php echo $_SESSION['phone'] ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
