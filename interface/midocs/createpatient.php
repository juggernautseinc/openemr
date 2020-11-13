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
?>

<html>
<head>
    <?php Header::setupHeader(); ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.standalone.min.css" rel="stylesheet"/>

    <?php CsrfUtils::setupCsrfKey(); ?>

    <title><?php echo text($openemr_name) . " " . xlt('Mi Docs'); ?></title>
</head>
<body class="login">
<body class="login">
<form method="POST" class="d-block" id="create_patient_form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="mb-4" style="font-size:15px;margin-left: -14px;">
                <label class="badge text-danger error-label" style="line-height:16px;width:0px;"></label>
            </div>
            <div class="form-group row">
                <label for="username" class="col-sm-2">Username</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="username">
                    <label class="badge text-danger mt-1 error-field" id="username"></label>
                    <input type="hidden" name="create_patient_form">
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
<form method="POST" class="d-none" id="submit_patient_form">
    <div class="<?php echo $loginrow; ?>">
        <div class="<?php echo $formarea; ?>">
            <div class="row">
                <div class="col-6">
                    <!--                    <div class="form-group row">-->
                    <!--                        <label for="username" class="col-sm-3">Username</label>-->
                    <!--                        <div class="col-sm-6">-->
                    <!--                            <input type="text" class="form-control" name="username">-->
                    <!--                            <label class="badge text-danger mt-1 error-field" id="username"></label>-->
                    <!--                            <input type="hidden" name="submit_patient_form">-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <div class="form-group row">
                        <label for="f_name" class="col-sm-3">Your first name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="f_name">
                            <label class="badge text-danger mt-1 error-field" id="f_name"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="l_name" class="col-sm-3">Your last name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="l_name">
                            <label class="badge text-danger mt-1 error-field" id="l_name"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sex" class="col-sm-3">Sex</label>
                        <div class="col-sm-6">
                            <select class="custom-select" name="sex">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dob" class="col-sm-3">Date of birth</label>
                        <div class="col-sm-6">
                            <input type='text' size='20' class='datepicker' name='dob'/>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <label for="email" class="col-sm-3">Email</label>
                        <div class="col-sm-6">
                            <input type="email" class="form-control" name="email">
                            <label class="badge text-danger mt-1 error-field" id="email"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="con_email" class="col-sm-3">Con-email</label>
                        <div class="col-sm-6">
                            <input type="email" class="form-control" name="con_email">
                            <label class="badge text-danger mt-1 error-field" id="con_email"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="number" class="col-sm-3">Cell number</label>
                        <div class="col-sm-6">
                            <input type="email" class="form-control" name="number">
                            <label class="badge text-danger mt-1 error-field" id="number"></label>
                        </div>
                    </div>
                    <p>(allows two fector authentication)</p>
                    <div class="form-group row">
                        <label for="dob" class="col-sm-4">Preferred method of contact</label>
                        <div class="col-sm-4">
                            <div class="row">
                                <label class="col-5">
                                    Phone :
                                </label>
                                <div class="col">
                                    <input type='radio' class="custom-checkbox" name='contact' value="phone"/>
                                    <input type="hidden" name="csrf_token"
                                           value="<?php echo $_SESSION['csrf_private_key'] ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-5">
                                    Email :
                                </label>
                                <div class="col">
                                    <input type='radio' class="custom-checkbox" name='contact' value="email"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mt-2">
                <div class="row">
                    <div class="col-6">
                        <button type="button" id="prevBtn" class="btn btn-success"><i
                                class="fa fa-arrow-circle-left"></i></button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" id="submit" class="btn btn-login"><?php echo xlt('Submit'); ?></button>
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
            <h5 class="text-center text-light">Please complete the following section <br> to create a MiDcos account
            </h5>
            </p>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.nl.min.js"></script>
<script>
    function hideShowPassword(evt, name) {
        if (evt == "hide") {
            //    hide password
            $("input[name=" + name + "]").attr('type', 'password');
        } else {
            //    show password
            $("input[name=" + name + "]").attr('type', 'text');
        }
    }

    $('.datepicker').datepicker({
        calendarWeeks: true,
        todayHighlight: true,
    });

    $("#createnext").click(function () {
        $(".error-field").text('');
        var error = false;
        var btn = $(this);


        $("#create_patient_form input").each(function (index) {
            var selector = $(this).attr("name");
            if ($(this).val() == "" && selector != "create_patient_form") {
                $('#' + selector).fadeIn();
                $('#' + selector).text("This field can not be empty");
                error = true;
                setTimeout(function () {
                    $('#' + selector).fadeOut();
                }, 2000);
            }
        })

        if (!error) {
            btn.text("Loading..");
            var data = {
                "form": "patient_check",
                "username": $("#create_patient_form input[name='username']").val(),
                "password": $("#create_patient_form input[name='password']").val(),
            }

            ajPost("midocsController.php", data, function (r) {
                if (r.error == 0) {
                    $("#create_patient_form").removeClass('d-block').addClass('d-none');
                    $("#submit_patient_form").removeClass('d-none');
                } else {
                    $(".error-label").fadeIn().html("This username already exists.  If you would like to create a new MiDocs account, please choose a different username. <br> If you already have a MiDocs account, but forgot your username or password, please click Forgot Username/Password.</br>  If you already have an account, and know your username and password, please click here to Login");

                    setTimeout(function () {
                        $(".error-label").fadeOut();
                    }, 10000)
                }
                btn.html("<i class='fa fa-arrow-circle-right'></i>");
            })

        }
    })

    $("#create_patient_form input[name='con_password']").keyup(function () {
        const con_pass = $(this).val();
        const pass = $("#create_patient_form input[name='password']").val();
        const btn = $("#createnext");
        const errorMsg = $("#con_password");

        btn.attr('disabled', false);
        errorMsg.text('');

        if (con_pass !== pass) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("password doesn't match");
        }
    })

    // form submit

    $('#submit_patient_form #prevBtn').click(function () {
        $("#create_patient_form").addClass('d-block').removeClass('d-none');
        $("#submit_patient_form").addClass('d-none');
    })



    $("#submit").click(function () {

        var btn = $(this);

        $(".error-field").text('');
        var error = false;

        $("#submit_patient_form input").each(function (index) {
            var selector = $(this).attr("name");
            if ($(this).val() == "" && selector != "submit_patient_form") {
                $('#' + selector).fadeIn();
                $('#' + selector).text("This field can not be empty");
                error = true;

                setTimeout(function () {
                    $('#' + selector).fadeOut();
                }, 3000);
            }
        })


        var data = {
            "form": "patient_create",
            "username": $("#create_patient_form input[name='username']").val(),
            "password": $("#create_patient_form input[name='password']").val(),
            // "username_2" : $("#submit_patient_form input[name='username']").val(),
            "f_name": $("#submit_patient_form input[name='f_name']").val(),
            "l_name": $("#submit_patient_form input[name='l_name']").val(),
            "sex": $("#submit_patient_form select[name='sex']").val(),
            "dob": $("#submit_patient_form input[name='dob']").val(),
            "number": $("#submit_patient_form input[name='number']").val(),
            "email": $("#submit_patient_form input[name='email']").val(),
            "preferred_contact": $("#submit_patient_form input[name='contact']:checked").val(),
            "csrf_token": $("#submit_patient_form input[name='csrf_token']").val(),
        };

        if (!error) {
            btn.text('Loading..');
            ajPost("midocsController.php", data, function (r) {
                if (r.error == 0) {
                    //    success
                    location.href = "../../portal/home.php?username=" + r.username;
                } else {
                    console.log("something went wrong");
                    //    something went wrong
                }
                btn.text('Submit');
            });
        }
    })

    $("#submit_patient_form input[name='con_email']").keyup(function () {
        const con_email = $(this).val();
        const email = $("#submit_patient_form input[name='email']").val();
        const btn = $("#submit");
        const errorMsg = $("#con_email");

        btn.attr('disabled', false);
        errorMsg.text('');

        if (con_email !== email) {
            btn.attr('disabled', true);
            errorMsg.fadeIn().text("Email doesn't match");
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
