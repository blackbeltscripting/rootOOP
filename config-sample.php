<?php
/**
 * The base configurations of rootOOP.
 *
 * This file is used by the config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "config.php" and fill in the values.
 *
 * IMPORTANT: Certain files (like this one) MUST be placed one directory above rootOOP like so:
 *
 * -/public_html/index.php
 * -/public_html/ajax.php
 * -/public_html/config.php
 * -/public_html/themes/{your-theme}/functions.php
 * -/public-html/themes/{your-theme}/index.php
 * -/public_html/rootOOP/
 *
 * Where /index.php is the main page. You can simply type the following in /index.php:
 * <?php include_once 'rootOOP/index.php'; ?>
 *
 * Where /ajax.php is the ajax return page. You can simply type the following in /ajax.php:
 * <?php include_once 'rootOOP/ajax.php'; ?>
 *
 * Where /config.php (This File)
 *
 * Where /themes/{your-theme}/functions.php Your Theme's Model/Conroller (Look for $theme in this page.)
 *
 * Where /themes/{your-theme}/index.php Your Theme's View
 */

/** The timezone that your site should be set at */
date_default_timezone_set('America/Los_Angeles');


/** Name of the Theme you are developing (must be placed one directory above rootOOP) */
// TEMP:
$theme = "MyTheme";

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for rootOOP */
define('DB_NAME', $db_name);

/** MySQL database username */
define('DB_USER', $db_user);

/** MySQL database password */
define('DB_PASS', $db_pass);

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


/** Defines SQL Float Decimal Separator. (Currently only '.' | ',') */
define('SQL_FLOAT_DECIMAL_SEPARATOR', '.');

/**
 * Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
define('TABLE_PREFIX', 'rOOP_');

/**
 * Defines Lifetime of User Session. Value is in seconds. Default 24hrs.
 *
 * This displays UTC timezone in my browser. Bug perhaps? Otherwise seems to work perfectly fine.
 */
define('SESSION_LIFETIME', '3600*24');

/**
 * @todo BECAUSE THE ADMIN AREA IS NOT YET BUILT WE WILL BE PUTTING THINGS IN HERE THAT SHOULD BE CONTROLLED IN THE ADMIN SECTION.
 */

define('SITE_URL', "http://" . $_SERVER['HTTP_HOST'] ."/rootOOP/");

define('DEFAULT_THEME', $theme);
define('THEME_URL', SITE_URL . "themes/" . $theme . "/");

/** Removes the "www." portion for cookie/session handling */
if (substr($_SERVER['HTTP_HOST'], 0, 4) == 'www.') {
	$domain = substr($_SERVER['HTTP_HOST'], 4);
} else {
	$domain = $_SERVER['HTTP_HOST'];
}

/** Session hostname will be this hostname. */
define('SESSION_HOSTNAME', $domain);

/**
 * Session name as appears in element inspector.
 * Using site theme along with name. Name must not exceed maximum length.
 * @see http://forums.devnetwork.net/viewtopic.php?f=34&t=88685 Session Name Length
 */
$s_name = "auth_token_rOOP_";

define('SESSION_NAME', $s_name . substr(DEFAULT_THEME, 0, (512 / SESSION_HASH_BITS_PER_CHARACTER) - strlen($s_name)));

define('TOKEN', "CSRFToken");

/**
 * This is the Forgot Password Request email subject and body
 * @%s : The CSRFToken Value
 */
define('FORGOT_PASSWORD_EMAIL_SUBJECT', 'Forgot Password Request');
define('FORGOT_PASSWORD_EMAIL_BODY', "A request has been sent to reset your password. If this request was sent by you, please click this link to <a href=\"".SITE_URL."forgot_password.php?t=%s\">continue the password reset procedure</a>. This authorization will expire within soon, so make sure to do this right away!<br /><br />
	Sincerely,<br />
	Automatic Services @ rootOOP<br />
	<br />
	<small>This email is automatically generated. Please do not reply.</small>");

/**
 * This does the same but for Email Verification
 * @%s : Name of the user
 * @%s : The CSRFToken Value
 */
define('VERIFY_EMAIL_SUBJECT', 'rootOOP Email Verification');
define('VERIFY_EMAIL_BODY', "Hello %s, <br><br>
	To verify your email address, please click <a href=\"" . SITE_URL . "validate_email.php?t=%s\">here</a>.
	<br><br>
	Sincerely,<br>
	Automatic Services @ rootOOP<br>
	<br>
	<small>This email is automatically generated. Please do not reply. If you did not request this email, please ignore this message.</small>");
