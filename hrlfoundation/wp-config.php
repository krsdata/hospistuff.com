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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hospistu_hrlfoundation');

/** MySQL database username */
define('DB_USER', 'hospistu_hrl');

/** MySQL database password */
define('DB_PASSWORD', 'hrlfoundation@123');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Hj~B9KJFg&Hx<>F1(AGV%g9>DW`}^:?^=4-m~Rmc>H`W|@(+v;<)VB$tD_a4/P|0');
define('SECURE_AUTH_KEY',  'd&s$:{A)iEm7fowml[,@<gSL<1{B-#~[G1<1 H*1kn9]P^d5psr5698x@gEyQn&R');
define('LOGGED_IN_KEY',    '[/`ZX&<h<bBcq7u$k; $.C`z~p5_Xee3v92}J ~Wv-RjqVn.E]|KRuRs4[JJ=h`9');
define('NONCE_KEY',        'eI 7V)~::&WE6(^~~L}](F#$-gXMhyMc#2hx,I9zOhR]*cOT#k.{;&PEMzi]Z+cW');
define('AUTH_SALT',        '/[i>FnbPXnEY%)A#|z72XA}y#^oOk4SYGy<%g*Mw.B$&j`ZE?vi+~Zu9CVf8eAw&');
define('SECURE_AUTH_SALT', 'L0rW`p bXo6{+&I[^iQOd,#Ici,NW`8yz3&YYXriCLptdNmGd7[Um/;_TAt?r}a;');
define('LOGGED_IN_SALT',   'Z9u:)VkW9e bbN4tONI*{:TmIr`QeW_5 I_3#mk3m%7D%(>>vm.?ReYy=1OD=}g2');
define('NONCE_SALT',       'h^%^?tb9Ax]fIRHS7Q=z}$9VI_TLrb,?qosVSM-obeu-cEif{rjN5``G6X@?CrQ7');

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

define( 'WP_MEMORY_LIMIT', '256M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
