<?php
include('header.php');

use OpenEMR\Core\Header;
use OpenEMR\Services\FacilityService;
use OpenEMR\Common\Csrf\CsrfUtils;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../../globals.php");
?>

<html>
<head>
    <?php Header::setupHeader(); ?>
</head>
<body>
<div class="container">
    <div class="row" style="margin-top:100px;">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h5>This patient is not currently in our database. Please check your search criteria and search again, or contact the patient to have their data upload to MiDocs. </h5>

                    <h5>If your are sure this patient has MiDocs account, then please give us a call at 845-287-5678.</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="request.php?username=<?php echo $_SESSION['username'] ?>" class="btn btn-primary">Revise Search</a>
                        </div>
                        <div class="col-6 text-right">
                            <a href="contact.php?username=<?php echo $_SESSION['username'] ?>" class="btn btn-success">Contact Patient</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
