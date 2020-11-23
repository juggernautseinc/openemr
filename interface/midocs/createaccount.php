<?php


/**
 * Login screen.
 *
 * @package OpenEMR
 * @link      http://www.open-emr.org
 * @author  Rod Roark <rod@sunsetsystems.com>
 * @author  Brady Miller <brady.g.miller@gmail.com>
 * @author  Kevin Yeh <kevin.y@integralemr.com>
 * @author  Scott Wakefield <scott.wakefield@gmail.com>
 * @author  ViCarePlus <visolve_emr@visolve.com>
 * @author  Julia Longtin <julialongtin@diasp.org>
 * @author  cfapress
 * @author  markleeds
 * @author  Tyler Wrenn <tyler@tylerwrenn.com>
 * @copyright Copyright (c) 2019 Brady Miller <brady.g.miller@gmail.com>
 * @copyright Copyright (c) 2020 Tyler Wrenn <tyler@tylerwrenn.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

use OpenEMR\Core\Header;
use OpenEMR\Services\FacilityService;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../globals.php");

// mdsupport - Add 'App' functionality for user interfaces without standard menu and frames
// If this script is called with app parameter, validate it without showing other apps.
//
// Build a list of valid entries

$emr_app = array();


// This code allows configurable positioning in the login page
$loginrow = "row login-row align-items-center m-5";

if ($GLOBALS['login_page_layout'] == 'left') {
    $logoarea = "col-md-6 login-bg-left py-3 px-5 py-md-login order-1 order-md-2";
    $formarea = "col-md-6 p-5 login-area-left order-2 order-md-1";
} else if ($GLOBALS['login_page_layout'] == 'right') {
    $logoarea = "col-md-6 login-bg-right py-3 px-5 py-md-login order-1 order-md-1";
    $formarea = "col-md-6 p-5 login-area-right order-2 order-md-2";
} else {
    $logoarea = "col-12 login-bg-center py-3 px-5 order-1";
    $formarea = "col-12 p-5 login-area-center order-2";
    $loginrow = "row login-row";
}
?>


<html>
<head>
    <?php Header::setupHeader(); ?>

    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>
</head>
<body class="login">
<form method="POST" id="midocs_login_form" autocomplete="off"
      action="../main/main_screen.php?auth=login&site=<?php echo attr($_SESSION['site_id']); ?>" target="_top"
      name="midocs_login_form">
    <div class="<?php echo $loginrow; ?>">

        <div class="<?php echo $formarea; ?>">
            <h4>Im a (n):</h4>
            <div class="form-check">
                <input class="form-check-input position-static" name="who" type="radio" id="radio" value="patient"
                       aria-label="...">
                <label for="authUser" class="text-right"><?php echo xlt('Patient'); ?></label>
            </div>
            <div class="form-check">
                <input class="form-check-input position-static" name="who" type="radio" id="radio" value="provider"
                       aria-label="...">
                <label for="authUser" class="text-right"><?php echo xlt('Provider'); ?></label>
            </div>
            <div class="form-check">
                <input class="form-check-input position-static" name="who" type="radio" id="radio" value="other"
                       aria-label="...">
                <label for="authUser"
                       class="text-right"><?php echo xlt('Other (Caregiver,healthcare proxy,...)'); ?></label>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" id="createnext" class="btn btn-login" disabled><i
                                class="fa fa-check"></i>&nbsp;&nbsp;<?php echo xlt('Next'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="<?php echo $logoarea; ?>">
            <div class="text-center login-title-label">
                <?php if ($GLOBALS['show_label_login']) { ?>
                    <?php echo text($openemr_name); ?>
                <?php } ?>
            </div>
            <h5 class="text-center text-light"><?php echo xlt('Create a MiDocs Account'); ?></h5>
            </p>
        </div>
    </div>
</form>
<script>
    $(function () {
        // make enable the next button.
        $("#midocs_login_form input[name='who']").click(function(){
            $('#createnext').attr('disabled',false);
        })
        $('#createnext').click(function () {
            //redirect to the selected page..
            var value = $("#midocs_login_form input[name='who']:checked").val();
            if(value){
                if(value == 'patient') location.href = './createpatient.php';
                if(value == 'provider') location.href = './createprovider.php';
                if(value == 'other') location.href = './createother.php';
            }
        })
    })
</script>
</body>
</html>
