<?php
/**
 * Default Login Process Page
 *
 * This page controls access to the default login process. It can be accessed in both POST and AJAX.
 *
 */

$LoginForm = new Form("login_process");
$LoginForm->add(new Input("email", array(
	"type" => "email",
	"name" => '<i class="fa fa-envelope-o"></i>',
	"label_id" => "email_label",
	"label_class" => "btn",
	"placeholder" => "Email",
	"autofocus" => true,
	"required" => true
)));
$LoginForm->add(new Input("password", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password_label",
	"label_class" => "btn",
	"placeholder" => "Password"
)));
$LoginForm->add(new Input("password2", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password2_label",
	"label_class" => "btn hidden",
	"input_class" => "hidden",
	"placeholder" => "Verify Password"
)));
$LoginForm->add(new Input("forgot_password", array(
	"type" => "checkbox",
	"name" => "Forgot Password?",
	"label_id" => "forgot_password_label",
)));
$LoginForm->add(new Button("submit_btn", array(
	"name" => ROOP_PATH,
	"value" => "login",
	"btn_name" => "Login"
)));

$LoginFormAjax = new Form("login_process", "post", true);
$LoginFormAjax->add(new Input("email", array(
	"type" => "email",
	"name" => '<i class="fa fa-envelope-o"></i>',
	"label_id" => "email_label",
	"label_class" => "btn",
	"placeholder" => "Email",
	"autofocus" => true,
	"required" => true
)));
$LoginFormAjax->add(new Input("password", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password_label",
	"label_class" => "btn",
	"placeholder" => "Password"
)));
$LoginFormAjax->add(new Input("password2", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password2_label",
	"label_class" => "btn hidden",
	"input_class" => "hidden",
	"placeholder" => "Verify Password"
)));
$LoginFormAjax->add(new Input("forgot_password", array(
	"type" => "checkbox",
	"name" => "Forgot Password?",
	"label_id" => "forgot_password_label",
)));
$LoginFormAjax->add(new Button("submit_btn", array(
	"name" => ROOP_PATH,
	"value" => "login",
	"btn_name" => "Login"
)));

$PasswordResetForm = new Form("login_process");
$PasswordResetForm->add(new Input("password", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password_label",
	"label_class" => "btn",
	"placeholder" => "Enter New Password",
	"required" => true
)));
$PasswordResetForm->add(new Input("password2", array(
	"type" => "password",
	"name" => '<i class="fa fa-key"></i>',
	"label_id" => "password2_label",
	"label_class" => "btn",
	"placeholder" => "Verify New Password",
	"required" => true
)));
$PasswordResetForm->add(new Button("submit_btn", array(
	"name" => ROOP_PATH,
	"value" => "reset",
	"btn_name" => "Reset"
)));

$LogoutForm = new Form("login_process");
$LogoutForm->add(new Button("logout_btn", array(
	"name" => ROOP_PATH,
	"value" => "logout",
	"btn_name" => "Logout"
)));

function loginProcess()
{
	global $User;
	// User Auth Posts.
	if (isset($_POST[ROOP_PATH]) && $p = $_POST[ROOP_PATH]) {
		switch ($p) {
		case 'login':
			if ($User->authenticate($_POST['email'], $_POST['password']) == false) // Must be done before index.php is loaded (or before <head>
				$login_process = "error";
			break;
		case 'forgot_password':
			if (!($login_process = $User->forgotPassword($_POST['email'])))
				$login_process = "error";
			break;
		case 'signup':
			global $Option;
			if ($Option['can_signup']->get() == "true") {
				if ($_POST['password'] === $_POST['password2'])
					$User->signup($_POST); // Signup and Email Auth.
			}
			break;
		case 'logout':
			//	Logs out without prejudice.
			$User->logout();
			break;
		case 'reset':
			//  No Filtering because it's just a comparison.
				if (
					$_POST['password'] == $_POST['password2'] &&
					$dbToken = $User->authForgottenToken($_POST['token'])
				) {
					global $MySQL;
					$MySQL->update("forgot_pass_auth", array("has_used" => 1), array("token" => $dbToken['token']));

					/** Passwords are escaped and hashed inside this function. */
					$User->edit($dbToken['email'], array("password" => $_POST['password']));

					// Move away to prevent error being shown in case client hits the back button or logout.
					exit(header('Location: '. SITE_URL . 'forgot_password.php?success=true'));
				}
			break;
		}
	}
	if (isset($login_process))
		return $login_process;
}
$Post->add("loginProcess");
$Ajax->add("loginProcess");
