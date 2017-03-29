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
define('DB_NAME', 'hospistu_ospreysecrectsolution');

/** MySQL database username */
define('DB_USER', 'hospistu_osprey');

/** MySQL database password */
define('DB_PASSWORD', 'ospreysecrectsolution@123');

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
define('AUTH_KEY',         'M9u!5fy8CL$=Xx2af)D01f;KH,EAl<*0if[|:~}{/nW%bnOMAv_o,#^}&!sQ~=3h');
define('SECURE_AUTH_KEY',  'GpP8mBb3WWpmudx>tliNAaF]AD +q$xHl[rxzLS~0VnpFr&SO_&7J0X$^@rPzLKT');
define('LOGGED_IN_KEY',    '%pv-=AX>TW;(g_[jq}2ox`QSSm3[(K<G^^`TA1XWv~Zt~L/tZN+aqxV?*Po:yE6Q');
define('NONCE_KEY',        'v<iDQ0z{.L>R&8]?26vZ ra4[m,snql~1 J6bH`>M^[-89e).0~w}ubU+YJs}?mq');
define('AUTH_SALT',        'mNa-+p|*< USe=)y=gd=1-33z2>9E:yk[>%%IFk?4U^nsc6ze:6^]]<?]Su%*YAC');
define('SECURE_AUTH_SALT', '.%BEk40VxdqJZe>P~+`)tT*zYP?gf{{92>G2L)v+^K k~rVQ>sWF8#jO2oS.|gM9');
define('LOGGED_IN_SALT',   'fm;x>om.Lg@wb7(iX]Ly-AK62cn&h MA$1AH_v#~UOm/RS/-u9vERMk[6j!)Y#6)');
define('NONCE_SALT',       'GCrtvdv6TvX67bTIDz&~s9UQp[QBd<ck5)dJRLYNF co|#i*K?{KV+,4{TqELuPu');

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
