<?php
/**
 * Arty // Web Design and Programming
 *
 * Validate New User's Email
 *
 * @author Eric Ghiraldello <ericg@arty-web-design.com>
 */
// Loads all prereqs
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "load.php";

// Logic Page
require_once THEME_PATH . "functions.php";

// Processes all Posts before the view page.
$Results = $Post->process();
post_header();
if (isset($_GET['t'])) {
	if ($User->authEmail($_GET['t']) == true) {
?>
		<h1>Email Authentification Complete!</h1>
		You can now login.
<?php
	} else {
?>
		<h1>An Error has Ocurred!</h1>
		We could not authenticate your email.
<?php
	}
} else {
?>
	<h1>You're almost done!</h1>
	Please check your email to verify we've got your email right!
<?php
}
post_footer();
