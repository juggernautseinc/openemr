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

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$url = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = substr($url, 0, strpos($url, "interface"));

?>


<html>
<head>
    <?php Header::setupHeader(); ?>
    <?php CsrfUtils::setupCsrfKey(); ?>

    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>
</head>
<body class="login">
<form method="POST" id="create_provider_form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label class="badge text-danger error-label" style="line-height:16px;width:0px;"></label>
            </div>
            <div class="form-group">
                <div class="col-3 offset-9">
                    <select class="custom-select" name="qualification">
                        <option value="Degree">Degree</option>
                        <option value="MD">MD</option>
                        <option value="DO">DO</option>
                        <option value="NP">NP</option>
                        <option value="PA">PA</option>
                        <option value="other">Other:____</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="provider_f_name" class="col-sm-2">Provider First Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="fname">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_private_key'] ?>">
                    <label class="badge text-danger mt-1 error-field" id="fname"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="provide_l_name" class="col-sm-2">Provider Last Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="lname">
                    <label class="badge text-danger mt-1 error-field" id="lname"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="practice_name" class="col-sm-2">Practice Name</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="practice_name">
                    <label class="badge text-danger mt-1 error-field" id="practice_name"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="username" class="col-sm-2">Username</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="username">
                    <label class="badge text-danger mt-1 error-field" id="username"></label>
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-sm-2">Password</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" name="password">
                    <label class="badge text-danger mt-1 error-field" id="password"></label>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="hideShowPassword('show','password')"><i class="fa fa-eye"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="hideShowPassword('hide','password')"><i class="fa fa-eye-slash"></i></button>
                </div>
            </div>
            <div class="form-group row">
                <label for="con_password" class="col-sm-2">Confirm Password</label>
                <div class="col-sm-4">
                    <input type="password" class="form-control" name="con_password">
                    <label class="badge text-danger mt-1 error-field" id="con_password"></label>
                </div>
                <div class="col">
                    <button type="button" class="btn btn-outline-primary btn-sm"
                            onclick="hideShowPassword('show','con_password')"><i class="fa fa-eye"></i></button>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="hideShowPassword('hide','con_password')"><i class="fa fa-eye-slash"></i></button>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" id="createnext" class="btn btn-primary"><i
                                class="fa fa-arrow-circle-right"></i></button>
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
            <h5 class="text-center text-light"><?php echo xlt('Brace yourself. Health Care Done Right!  '); ?></h5>
            </p>
        </div>
    </div>
</form>
<form method="POST" id="provider_sumbit_form" class="d-none">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label class="badge text-danger error-label" style="line-height:16px;width:0px;"></label>
                <label id="success-label" class="badge text-success error-label" style="line-height:16px;width:0px;"></label>
            </div>
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
                        <button type="button" class="btn btn-success" id="prevBtn">
                            <i class="fa fa-arrow-circle-left"></i></button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn btn-login" id="submit_provider_btn"><i
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
    $("#createnext").click(function () {

        const btn = $(this);
        $(".error-field").text('');
        var error = false;
        $("#create_provider_form input").each(function (index) {
            var selector = $(this).attr("name");
            if ($(this).val() == "" && selector != "form") {
                $('#' + selector).fadeIn();
                $('#' + selector).text("This field can not be empty");
                error = true;

                setTimeout(function () {
                    $('#' + selector).fadeOut();
                }, 3000);
            }
        })

        if (!error) {
            btn.text('Loading..');
            var data = {
                "username": $("#create_provider_form input[name='username']").val(),
                "password": $("#create_provider_form input[name='password']").val(),
                "form": "check_provider"
            }
            ajPost('midocsController.php', data, function (r) {
                if (r.error == 1) {
                    $(".error-label").fadeIn().html("This username already exists.  If you would like to create a new MiDocs account, please choose a different username. <br> If you already have a MiDocs account, but forgot your username or password, please click Forgot Username/Password.</br>  If you already have an account, and know your username and password, please click here to Login");

                    setTimeout(function () {
                        $(".error-label").fadeOut();
                    }, 10000)
                } else {
                    //    no error
                    $("#create_provider_form").addClass('d-none');
                    $("#provider_sumbit_form").removeClass('d-none');
                }

                btn.html('<i class="fa fa-arrow-circle-right"></i>');
            });
        }
    })

    $("#prevBtn").click(function () {
        $("#create_provider_form").removeClass('d-none');
        $("#provider_sumbit_form").addClass('d-none').removeClass('d-block');
    })

    $("#provider_sumbit_form input[name='con_email']").keyup(function () {
        const con_email = $(this).val();
        const email = $("#provider_sumbit_form input[name='email']").val();
        const btn = $("#submit_provider_btn");
        const errorMsg = $("#con_email");

        btn.attr('disabled', false);
        errorMsg.text('');

        if (con_email !== email) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("email doesn't match");
        }
    });

    $("#submit_provider_btn").click(function () {
        var error = false;
        var btn = $(this);

        $("#provider_sumbit_form input").each(function(){
            var id = $(this).attr('name');

            if($(this).val() == ""){
                error = true;
                $("#"+id).fadeIn().text("This field can not be empty");

                setTimeout(function(){
                    $("#"+id).fadeOut();
                },3000)
            }
        })

        if(!error){
            var data = {
                "form": "create_provider",
                "username": $("#create_provider_form input[name='username']").val(),
                "password": $("#create_provider_form input[name='password']").val(),
                "fname": $("#create_provider_form input[name='fname']").val(),
                "lname": $("#create_provider_form input[name='lname']").val(),
                "csrf_token": $("#create_provider_form input[name='csrf_token']").val(),
                "practice_name": $("#create_provider_form input[name='practice_name']").val(),
                "qualification": $("#create_provider_form select[name='qualification']").val(),

                "npi": $("#provider_sumbit_form input[name='npi']").val(),
                "email": $("#provider_sumbit_form input[name='email']").val()
            }

            btn.text("Loading..");
            ajPost("midocsController.php", data, function (r) {
                if (r.error == 0) {
                    //    redirect to provider page
                    $("#success-label").fadeIn().text("Successfully created, Thank you for using Midocs");
                    setTimeout(function(){
                        location.href = "<?php echo $url?>";
                    },3000)
                } else {
                    $('.error-label').fadeIn();
                    $('.error-label').text("Something went wrong!");

                    setTimeout(function () {
                        $('.error-label').fadeOut();
                    }, 2000)
                }

                btn.text("Submit");
            })
        }
    })

    $("#create_provider_form input[name='con_password']").keyup(function () {
        const con_pass = $(this).val();
        const pass = $("#create_provider_form input[name='password']").val();
        const btn = $("#createnext");
        const errorMsg = $("#con_password");

        btn.attr('disabled', false);
        errorMsg.text("");

        if (con_pass !== pass) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("password doesn't match");
        }
    })

    function ajPost(url, data, callback) {
        console.log(data);
        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (r) {
                callback(r);
            }
        })
    }

    function hideShowPassword(evt, name) {
        if (evt == "hide") {
            //    hide password
            $("input[name=" + name + "]").attr('type', 'password');
        } else {
            //    show password
            $("input[name=" + name + "]").attr('type', 'text');
        }
    }
</script>
</body>
</html>
