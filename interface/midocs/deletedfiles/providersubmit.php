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
<body class="login">
<form method="POST" id="provider_sumbit_form" autocomplete="off"
      target="_top"
      name="midocs_login_form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="form-group row">
                <label for="npi" class="col-sm-2">Individual NPI</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="npi">
                    <label class="badge text-danger mt-1 error-field" id="npi"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-2">E-mail</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="email">
                    <label class="badge text-danger mt-1 error-field" id="email"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="con_email" class="col-sm-2">Confirm E-mail</label>
                <div class="col-sm-4">
                    <input type="email" class="form-control" name="con_email">
                    <label class="badge text-danger mt-1 error-field" id="con_email"></label>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-login" id="create_provider_btn"><i
                                class="fa fa-check"></i>&nbsp;&nbsp;<?php echo xlt('Submit'); ?></button>
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
            <h3 class="text-center text-light"><?php echo xlt('Provider Registration!  '); ?></h3>
            </p>
        </div>
    </div>
</form>
<script>
    $("#create_provider_btn").click(function(){
        const btn = $(this);
        btn.text('Loading..');
        $(".error-field").text('');
        var error = false;
        $("#provider_sumbit_form input").each(function(index){
            if($(this).val() == "") {
                var selector = $(this).attr("name");
                $('#' + selector).text("This field can not be empty");
                error = true;
            }
        });
        btn.text('Submit');
        if(!error); // submit form..
    })
    $("#provider_sumbit_form input[name='con_email']").keyup(function(){
        const con_email = $(this).val();
        const email = $("#provider_sumbit_form input[name='email']").val();
        const btn = $("#create_provider_btn");
        const errorMsg = $("#con_email");

        btn.attr('disabled',false);
        errorMsg.text('');

        if(con_email !== email) {
            btn.attr('disabled',true);
            errorMsg.text("password doesn't match");
        }
    });
</script>
</body>
</html>
