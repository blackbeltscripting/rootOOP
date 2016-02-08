<?php
/**
 * Arty // Web Design and Programming
 *
 * Loads all logic in core of rOOP
 *
 */
// Loads all prereqs
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."defines.php";
require_once ABSPATH.'config.php';
require_once INCLUDES.'session.php';

/**
 * PSR-4 Standard
 *
 * @see http://www.php-fig.org/psr/psr-4/ PSR-4
 * NOTE: This anonymous function does not work with PHP <= 5.4 [TESTED]
 */
spl_autoload_register(function($class) {
	$rootClass = CLASSES . $class . '.php';
	$themeClass = THEMECLASS . $class . '.php';

	if ( !empty($rootClass) || !empty($themeClass) ) {
		if (is_readable($rootClass))
			include_once $rootClass;
		elseif (is_readable($themeClass))
			include_once $themeClass;
	}
});

/** Loading all Default Classes */
$MySQL  = new MySQL();
$Option = array();
$Option['active_theme'] = new Option(TABLE_PREFIX . "active_theme");
$User = new User();

$Post = new Post();
$Ajax = new Post("ajax");

/** Add Default Login Process */
require_once INCLUDES.'login-process.php';

/** Adds Essential Callbacks for Ajax through header */
require_once INCLUDES.'general-template.php';

/** @todo Remove this by major release. This line is used to control the theme to develop. */
$Option['active_theme']->update(DEFAULT_THEME);

// If no active theme, create default option.
if (NULL === $Option['active_theme']->get())
	$Option['active_theme']->add(DEFAULT_THEME);

$theme = "";
// Get all Themes in Dir
foreach (glob( THEMES . "*" ) as $theme_dirname) {
	$t = explode("/", $theme_dirname);
	$theme_name = end($t);

	// Look for active theme and load it.
	if ($theme_name == $Option['active_theme']->get())
		define("THEME_PATH", $theme_dirname . DIRECTORY_SEPARATOR);
}
