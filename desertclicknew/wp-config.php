<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
define( 'WP_MEMORY_LIMIT', '512M' );
define(‘WP_MAX_MEMORY_LIMIT’, ‘512M’);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hospistu_desertclicknew');

/** MySQL database username */
define('DB_USER', 'hospistu_desertc');

/** MySQL database password */
define('DB_PASSWORD', 'desertclicknew@123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

// add this to your wp-config.php file
// navigate to the whitescreen page and add ?debug=debug to the url
// eg: http://mywebsite.com/problempage?debug=debug
// or on a page with query string variables already http://mywebsite.com/problempage?variable=123&debug=debug
if ( isset( $_GET['debug'] ) && 'debug' == $_GET['debug'] ) {
	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_DISPLAY', true );
	define( 'SCRIPT_DEBUG', true );
}

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hC~FG=CpEuB!Ctdi.B<?aU%tkJ1NE{cuxkmxGcwkRtr^uxIy{v<%4&T0:9C~}=XH');
define('SECURE_AUTH_KEY',  'jeD$w)@)|~>L5~klgD-p&x3A4{#S;OAkt1-AniXz%:~E-##P>DQ&KnO<[bk0.[P:');
define('LOGGED_IN_KEY',    ':BPBSS<(SSLAWkJw!PWoT2I|B+pp/44T66/Nk!G6L*<%pk`:M}uwc@7u_.9p6Lw7');
define('NONCE_KEY',        '4d9X|tiu]-l/~ps9dQ -GLNmy!j83kh%Qv9?^H95$7@:jljW{6:!_(]3vI9{|f n');
define('AUTH_SALT',        'zOyY17:g$pXf+8d$cRmh)@|%XSbcG1D9G%9x#L=D8;%yBWmT6Ws26p YZ<z/-y8{');
define('SECURE_AUTH_SALT', 'pk^:,c%/PR{fiEUez2+D(GGkV<]h6pLFMbZqYw@nL;UpIdDEtZOYvp_*3lo7zz8s');
define('LOGGED_IN_SALT',   '$r w]IQ.PnVIlP8#769bq&uEQbyCs}tFfFa>wj#75csH{9reJ=E{DQRjn$@Wabc/');
define('NONCE_SALT',       'u.B]M!6IY?5?|{1#B^zd%Z<{;_.{V9079MxS:WdR +wv?__GM.xL4xs&YXIxOg;N');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
