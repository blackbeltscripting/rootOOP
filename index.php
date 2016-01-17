<?php
/**
 * Arty // Web Design and Programming
 *
 * Now follows PSR-1, PSR-2, (I don't know how to log yet.), PSR-4 , and preparing for PSR-5 (not yet standardized).
 * Also trying to follow OWASP standards. So far the site should prevent:
 *  -MySQL Injections
 *  -XSS Attacks
 *  -CSRF Attacks
 *  -Session Hijacking
 *
 * @author Eric Ghiraldello <ericg@arty-web-design.com>
 * @version 0.12.3
 */
/** Current Site Version */
define("VERSION", "0.12.3");

// Loads all prereqs
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "load.php";

// Logic Page
require_once THEME_PATH . "functions.php";

// Processes all Posts before the view page.
$Results = $Post->process();

// View Page
require_once THEME_PATH . "index.php";
echo "another test ";
