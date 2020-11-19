<?php
session_start();

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
use OpenEMR\Common\Csrf\CsrfUtils;

$ignoreAuth = true;
// Set $sessionAllowWrite to true to prevent session concurrency issues during authorization related code
$sessionAllowWrite = true;
require_once("../globals.php");

// mdsupport - Add 'App' functionality for user interfaces without standard menu and frames
// If this script is called with app parameter, validate it without showing other apps.
//
// Build a list of valid entries

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = substr($url, 0, strpos($url, "interface"));

$emr_app = array();
$rs = sqlStatement(
    "SELECT option_id, title,is_default FROM list_options
        WHERE list_id=? and activity=1 ORDER BY seq, option_id",
    array('apps')
);
if (sqlNumRows($rs)) {
    while ($app = sqlFetchArray($rs)) {
        $app_req = explode('?', trim($app['title']));
        if (!file_exists('../' . $app_req[0])) {
            continue;
        }

        $emr_app [trim($app ['option_id'])] = trim($app ['title']);
        if ($app ['is_default']) {
            $emr_app_def = $app ['option_id'];
        }
    }
}

$div_app = '';
if (count($emr_app)) {
    // Standard app must exist
    $std_app = 'main/main_screen.php';
    if (!in_array($std_app, $emr_app)) {
        $emr_app['*OpenEMR'] = $std_app;
    }

    if (isset($_REQUEST['app']) && $emr_app[$_REQUEST['app']]) {
        $div_app = sprintf('<input type="hidden" name="appChoice" value="%s">', attr($_REQUEST['app']));
    } else {
        foreach ($emr_app as $opt_disp => $opt_value) {
            $opt_htm .= sprintf(
                '<option value="%s" %s>%s</option>\n',
                attr($opt_disp),
                ($opt_disp == $opt_default ? 'selected="selected"' : ''),
                text(xl_list_label($opt_disp))
            );
        }

        $div_app = sprintf(
            '
<div id="divApp" class="form-group">
	<label for="appChoice" class="text-right">%s:</label>
    <div>
        <select class="form-control" id="selApp" name="appChoice" size="1">%s</select>
    </div>
</div>',
            xlt('App'),
            $opt_htm
        );
    }
}


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
    <?php CsrfUtils::setupCsrfKey(); ?>
    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>

    <script>
        var registrationTranslations = <?php echo json_encode(array(
            'title' => xla('OpenEMR Product Registration'),
            'pleaseProvideValidEmail' => xla('Please provide a valid email address'),
            'success' => xla('Success'),
            'registeredSuccess' => xla('Your installation of OpenEMR has been registered'),
            'submit' => xla('Submit'),
            'noThanks' => xla('No Thanks'),
            'registeredEmail' => xla('Registered email'),
            'registeredId' => xla('Registered id'),
            'genericError' => xla('Error. Try again later'),
            'closeTooltip' => ''
        )); ?>;

        var registrationConstants = <?php echo json_encode(array(
            'webroot' => $GLOBALS['webroot']
        )); ?>;
    </script>

    <script
        src="<?php echo $webroot ?>/interface/product_registration/product_registration_service.js?v=<?php echo $v_js_includes; ?>"></script>
    <script
        src="<?php echo $webroot ?>/interface/product_registration/product_registration_controller.js?v=<?php echo $v_js_includes; ?>"></script>

    <script>
        $(function () {
            init();

            var productRegistrationController = new ProductRegistrationController();
            productRegistrationController.getProductRegistrationStatus(function (err, data) {
                if (err) {
                    return;
                }

                if (data.statusAsString === 'UNREGISTERED') {
                    productRegistrationController.showProductRegistrationModal();
                }
            });
        });

        function init() {
            $("#authUser").focus();
        }

        function transmit_form(element) {
            // disable submit button to insert a notification of working
            element.disabled = true;
            // nothing fancy. mainly for mobile.
            element.innerHTML = '<i class="fa fa-sync fa-spin"></i> ' + jsText(<?php echo xlj("Authenticating"); ?>);
            <?php if (!empty($GLOBALS['restore_sessions'])) { ?>
            // Delete the session cookie by setting its expiration date in the past.
            // This forces the server to create a new session ID.
            var olddate = new Date();
            olddate.setFullYear(olddate.getFullYear() - 1);
            <?php if (version_compare(phpversion(), '7.3.0', '>=')) { ?>
            // Using the SameSite setting when using php version 7.3.0 or above, which avoids browser warnings when cookie is not 'secure' and SameSite is not set to anything
            document.cookie = <?php echo json_encode(urlencode(session_name())); ?> +'=' + <?php echo json_encode(urlencode(session_id())); ?> +'; path=<?php echo($web_root ? $web_root : '/');?>; expires=' + olddate.toGMTString() + '; SameSite=Strict';
            <?php } else { ?>
            document.cookie = <?php echo json_encode(urlencode(session_name())); ?> +'=' + <?php echo json_encode(urlencode(session_id())); ?> +'; path=<?php echo($web_root ? $web_root : '/');?>; expires=' + olddate.toGMTString();
            <?php } ?>
            <?php } ?>
            document.forms[0].submit();
        }
    </script>
</head>
<body class="login">
<form id="reset-form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label class="badge text-danger label-error" id="error-label"
                       style="line-height:16px;width:0px;"></label>
                <label class="badge text-success label-error" id="success-label"
                       style="line-height:16px;width:0px;"></label>
            </div>
            <div class="form-group">
                <label for="f_name" class="text-right"><?php echo xlt('First Name:'); ?></label>
                <input type="text" class="form-control" name="fname"/>
                <input type="hidden" class="form-control" name="form" value="reset-form"/>
                <input type="hidden" name="csrf_token" value="<?php echo CsrfUtils::collectCsrfToken(); ?>">
                <label class="badge text-danger error-label mt-2" id="fname"></label>
            </div>
            <div class="form-group">
                <label for="l_name" class="text-right"><?php echo xlt('Last Name:'); ?></label>
                <input type="text" class="form-control" name="lname"
                />
                <label class="badge text-danger error-label mt-2" id="lname"></label>
            </div>
            <div class="form-group">
                <label for="contect" class="text-right"><?php echo xlt('Your email or cell phone number:'); ?></label>
                <div class="row">
                    <div class="col-3">
                        <select id="select-contact" class="custom-select" name="contact">
                            <option value="cell">Cell</option>
                            <option value="email">E-mail</option>
                        </select>
                    </div>
                    <div class="col">
                        <input id="cell" type="text" class="form-control" name="cell"/>
                        <input id="email" type="text" class="form-control d-none" name="email"/>

                        <label class="badge text-danger error-label mt-2" id="email-phone"></label>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-right">
                        <button id="reset-btn" type="submit" class="btn btn-login"><i
                                class="fa fa-check"></i>&nbsp;&nbsp;<?php echo xlt('Submit'); ?></button>
                    </div>
                </div>
            </div>
            <div class="mt-2 text-center">
                <!--                <p>New login credentials will be sent to the preferred contact on file.</p>-->
            </div>
        </div>
        <div class="<?php echo $logoarea; ?>">
            <div class="text-center login-title-label">
                <?php if ($GLOBALS['show_label_login']) { ?>
                    <?php echo text($openemr_name); ?>
                <?php } ?>
            </div>
            <h5 class="text-center text-light"><?php echo xlt('To reset your Username and/or Password please enter:'); ?></h5>
            </p>
        </div>
    </div>
</form>
<script>
    $("#select-contact").change(function () {
        var val = $(this).val();
        $("#" + val).removeClass("d-none");
        if (val == "email") {
            $("#cell").addClass("d-none");
            $("#cell").val("");
        } else {
            $("#email").addClass("d-none");
            $("#email").val("");
        }
    })

    $("#reset-form").submit(function (r) {
        r.preventDefault();
        var btn = $("#reset-btn");
        btn.html("<i class='fa fa-check'></i> Submit");
        $(".label-error").text("");
        var error = false;

        $("#reset-form input").each(function (i) {
            var name = $(this).attr('name');
            if ($(this).val() == "" && name !== "email" && name !== "cell") {
                $("#" + name).fadeIn().text("This field is empty");
                error = true;

                setTimeout(function () {
                    $("#" + name).fadeOut();
                }, 3000)
            }
        })

        if ($("input[name='email']").val() == "" && $("input[name='cell']").val() == "") {
            error = true;

            $("#email-phone").fadeIn().text("This field can not be empty");

            setTimeout(function () {
                $("#email-phone").fadeOut();
            }, 3000)
        }

        if (!error) {
            btn.text("Loading..");
            // var data = $(this).serialize();
            var data = {
                "form": "reset-form",
                "fname": $("#reset-form input[name='fname']").val(),
                "lname": $("#reset-form input[name='fname']").val(),
                "cell": $("#reset-form input[name='cell']").val(),
                "email": $("#reset-form input[name='email']").val(),
                "csrf_token": $("#reset-form input[name='csrf_token']").val(),
            }

            ajPost("midocsController.php", data, function (r) {
                if (r.available == 0) {
                    // console.log("you have to create a new account");
                    $("#error-label").fadeIn().html("We cannot locate your account. If you are sure you have a MiDocs account please give us a call.<br>" +
                        "Otherwise you can create a new account. <br>" +
                        "<a href='createaccount.php' class='btn btn-outline-primary btn-sm mt-3'>Create New Account</a>");
                } else {
                    $("#success-label").fadeIn().text("We'll email your details in short while.");

                    setTimeout(function () {
                        location.href = "<?php echo $url?>";
                    }, 3000)
                }

                btn.text("Submit");
            })
        }
    })

    function ajPost(url, data, callBack) {
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (r) {
                callBack(r);
            }
        })
    }
</script>
</body>
</html>
