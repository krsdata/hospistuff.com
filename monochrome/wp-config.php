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
define('DB_NAME', 'hospistu_monochrome');

/** MySQL database username */
define('DB_USER', 'hospistu_mono');

/** MySQL database password */
define('DB_PASSWORD', 'monochrome@123');

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
define('AUTH_KEY',         '3Y||RI?!GZMt;%RhS;Z]m+GQ#U!P;opT#i,!V6?F&8!ppH~W!TdrWi4]|=br_%7+');
define('SECURE_AUTH_KEY',  'i[H#Op51flGt</4sjb-g<KJ0SW|tLcfG{4.[hwi1_trw:Y#G$|in$n[:qH!o{GQ#');
define('LOGGED_IN_KEY',    '_8$/(b^XFxdiW#aa;N:-(:N7>I&=%n*Z<>VEr<CIQ7)CiW^5FBv<LltD7kbRwI[G');
define('NONCE_KEY',        'lA51TgMsOy(7#yOT&Vj[zlgo2uoASqF##Ss3(L:ztj!p@3nk$/<8XSR+3~V7%4W~');
define('AUTH_SALT',        'r1[q1)]]m~}bGX@qM-+`n:)s!+JO~d<j=WA*Wk}oAgu>A]%8fFW}w*Ju8U-C(tg<');
define('SECURE_AUTH_SALT', 'c=4wlPu<a)ozP18l_OMB5&<{b1`lkw:O[}ja43 Ha[MkQSjbjauJOszgpi_pJ*=Y');
define('LOGGED_IN_SALT',   '00pzC3wzE4CLZvP&`):#jl;Jo0K9}Jp5gbnU2[HU#=|H#^Fy`0*0HOy7*AzLn@I+');
define('NONCE_SALT',       'F^/8Ef>%<f!0{#*k-9U;sul1@uA@a-A &==%~Z4W`1)-pP%dY15n2Cqg}_V#*Ppc');

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
