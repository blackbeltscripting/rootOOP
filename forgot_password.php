<?php
/**
 * Arty // Web Design and Programming
 *
 * Forgot Password Default Page
 *
 * @author Eric Ghiraldello <ericg@arty-web-design.com>
 */
// Loads all prereqs
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "load.php";

//	Logic Page
require_once THEME_PATH . "functions.php";

//  Processes all Posts before the view page.
$Results = $Post->process();
post_header();
if (!isset($_GET['success']) && !isset($_GET['check_email'])) {
	if ($dbToken = $User->authForgottenToken($_GET['t'])) {
		//  We are resetting password now.
		$PasswordResetForm->add(new Input("token", array(
			"type" => "hidden",
			"value" => $dbToken['token']
		)));
?>
		<h1>Reset Your Password</h1>
<?php
		echo $PasswordResetForm->html(true);
	} else {
		// Didn't find the token. Show forgot password form.
		$ForgotPass = new Form("login_process");
		$ForgotPass->add(new Input("email", array(
			"type" => "email",
			"name" => '<i class="fa fa-envelope-o"></i>',
			"label_id" => "email_label",
			"label_class" => "btn",
			"placeholder" => "Email",
			"autofocus" => true,
			"required" => true
		)));
		$ForgotPass->add(new Button("submit_btn", array(
			"name" => ROOP_PATH,
			"value" => "forgot_password",
			"btn_name" => "Retrieve Password"
		)));
?>
		<h1>Reset Your Password</h1>
		We did not find your token or your token has expired. Please try again.<br>
<?php
		echo $ForgotPass->html(true);
	}
} elseif (isset($_GET['success'])) {
?>
	<h1>Password Reset Successful!</h1>
	Please login with your new password.
<?php
} elseif (isset($_GET['check_email'])) {
?>
	<h1>Your password has been sent!</h1>
	Remember to check your SPAM folder as well! If you do not receive an email within 24 hours, try using the signup page!
<?php
}
post_footer();
