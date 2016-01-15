<?php
/**
 * Defines all constants in Global Scope.
 *
 *
 */

/** Defines Absolute Path as this directory. */
define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/** The includes path. Currently empty. */
define('INCLUDES', ABSPATH . "includes" . DIRECTORY_SEPARATOR);

/** The Classes path. Holds all classes in API. */
define('CLASSES', INCLUDES . "classes" . DIRECTORY_SEPARATOR);

/** The Admin path. Currently under development. */
define('ADMIN', ABSPATH . "admin" . DIRECTORY_SEPARATOR);

/** The Content path. Currently empty.
 * @todo Will be used for uploading (maybe) directories. */
define('CONTENT', ABSPATH . "content" . DIRECTORY_SEPARATOR);

/** The Themes directory. This will have all installed themes in the API. */
define('THEMES', ABSPATH . join(DIRECTORY_SEPARATOR, array("content", "themes")) . DIRECTORY_SEPARATOR);

/** Session bits per character. Using '4' to comply with OWASP standard. */
define('SESSION_HASH_BITS_PER_CHARACTER', 4);

/** If set to true, generates a different session token every refresh. */
define('SESSION_REGENERATE_ID', false);

/** Session security (enable if you have TLS [SSL]). */
define('SESSION_SECURE', false);

/** Forgot Password Auth Token Lifespan measured in minutes. Default 24 hrs. */
define('PASSWORD_TOKEN_LIFESPAN', 60*24);

/** If set to true will attempt to insert the default tables needed for the classes. */
define('ALLOW_TABLE_INSERT', true);

/** Sets the rootOOP's main functionality path variable. */
define('ROOP_PATH', 'rP');
