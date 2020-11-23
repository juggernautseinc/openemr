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
<form method="POST" id="midocs_login_form" autocomplete="off"
      action="../main/main_screen.php?auth=login&site=<?php echo attr($_SESSION['site_id']); ?>" target="_top"
      name="midocs_login_form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <input type='hidden' name='new_login_session_management' value='1'/>

            <?php
            // collect default language id
            $res2 = sqlStatement("select * from lang_languages where lang_description = ?", array($GLOBALS['language_default']));
            for ($iter = 0; $row = sqlFetchArray($res2); $iter++) {
                $result2[$iter] = $row;
            }

            if (count($result2) == 1) {
                $defaultLangID = $result2[0]["lang_id"];
                $defaultLangName = $result2[0]["lang_description"];
            } else {
                //default to english if any problems
                $defaultLangID = 1;
                $defaultLangName = "English";
            }

            // set session variable to default so login information appears in default language
            $_SESSION['language_choice'] = $defaultLangID;
            // collect languages if showing language menu
            if ($GLOBALS['language_menu_login']) {
                // sorting order of language titles depends on language translation options.
                $mainLangID = empty($_SESSION['language_choice']) ? '1' : $_SESSION['language_choice'];
                // Use and sort by the translated language name.
                $sql = "SELECT ll.lang_id, " .
                    "IF(LENGTH(ld.definition),ld.definition,ll.lang_description) AS trans_lang_description, " .
                    "ll.lang_description " .
                    "FROM lang_languages AS ll " .
                    "LEFT JOIN lang_constants AS lc ON lc.constant_name = ll.lang_description " .
                    "LEFT JOIN lang_definitions AS ld ON ld.cons_id = lc.cons_id AND " .
                    "ld.lang_id = ? " .
                    "ORDER BY IF(LENGTH(ld.definition),ld.definition,ll.lang_description), ll.lang_id";
                $res3 = SqlStatement($sql, array($mainLangID));

                for ($iter = 0; $row = sqlFetchArray($res3); $iter++) {
                    $result3[$iter] = $row;
                }

                if (count($result3) == 1) {
                    //default to english if only return one language
                    echo "<input type='hidden' name='languageChoice' value='1' />\n";
                }
            } else {
                echo "<input type='hidden' name='languageChoice' value='" . attr($defaultLangID) . "' />\n";
            }

            if ($GLOBALS['login_into_facility']) {
                $facilityService = new FacilityService();
                $facilities = $facilityService->getAllFacility();
                $facilitySelected = ($GLOBALS['set_facility_cookie'] && isset($_COOKIE['pc_facility'])) ? $_COOKIE['pc_facility'] : null;
            }
            ?>
            <?php if (isset($_SESSION['relogin']) && ($_SESSION['relogin'] == 1)) { // Begin relogin dialog ?>
                <div class="alert alert-info m-1 font-weight-bold">
                    <?php echo xlt('Password security has recently been upgraded.') . '&nbsp;&nbsp;' . xlt('Please login again.'); ?>
                </div>
                <?php unset($_SESSION['relogin']);
            }
            if (isset($_SESSION['loginfailure']) && ($_SESSION['loginfailure'] == 1)) { // Begin login failure block ?>
                <div class="alert alert-danger login-failure m-1">
                    <?php echo xlt('Invalid username or password');
                    //                    print_r($_SESSION); ?>
                </div>
            <?php } // End login failure block ?>
            <div class="form-group">
                <label for="authUser" class="text-right"><?php echo xlt('Username:'); ?></label>
                <input type="text" class="form-control" id="authUser" name="authUser"
                       placeholder="<?php echo xla('Username:'); ?>"/>
            </div>
            <div class="form-group">
                <label for="clearPass" class="text-right"><?php echo xlt('Password:'); ?></label>
                <input type="password" class="form-control" id="clearPass" name="clearPass"
                       placeholder="<?php echo xla('Password:'); ?>"/>
            </div>
            <div class="form-group text-center mt-1">
                <a href="./reset.php"><?php echo xlt('FORGOT Username / Password'); ?></a>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                        <a href="createaccount.php" type="button" class="btn btn-info"><i
                                class="fa fa-user-plus"></i>&nbsp;&nbsp;<?php echo xlt('Create Account'); ?></a>
                    </div>
                    <div class="col-6 text-right">
                        <button type="submit" class="btn btn-login" onClick="transmit_form(this)"><i
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
            <h2 class="text-center text-light"><?php echo xlt('Mi Docs'); ?></h2>
            </p>
        </div>
    </div>
</form>
</body>
</html>
