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
define('DB_NAME', 'sosasap_sports');

/** MySQL database username */
define('DB_USER', 'sosasap_tarunya');

/** MySQL database password */
define('DB_PASSWORD', 'Rr23(dj)eaPT');

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
define('AUTH_KEY',         ')(:]*3B&3vumH-<LOhhY}u#z~Xm lYQa 2-X.wk+c&/L_G^1Ve!,(;qYzoutr5t6');
define('SECURE_AUTH_KEY',  'R PWj)g?Z NEVt,/,.L_Aiw5,Rc{xtiv,Y774[G!%B)d>V]@ii*?@`X6sZttjg:h');
define('LOGGED_IN_KEY',    'oyrnA,I8GL,JiFsY_*iQ?[Id;r~A`gW,>Ts*_Y`C}/~.P`zLO=SJSe}F-5p0^aVm');
define('NONCE_KEY',        '2Xw{yoY4~}4OrOqz]/3FCY3 $S8QJ3N|mda4:BE=&os,iGUFSI|:nQ>kO;0D)w<M');
define('AUTH_SALT',        'aHq ^xNkpm`BMb?x,~h/[}o|DDI<1Qre](j*RQ#!t1O#6[}XV|e:c-4GVYA;VEUZ');
define('SECURE_AUTH_SALT', '9_|oMO Jg^XF6aLOkjV=+e(&/?9<Gwf@U{CO1nLookm,xmpR|&F$]l(5q?D-UcK/');
define('LOGGED_IN_SALT',   '-,48ELx$O$CfR>Jl(Bf)tQ34{;;!OZ.wB+JTWQfLla/akRAmWl2n$>c>?GL[.y_^');
define('NONCE_SALT',       'k(/xUw=EYq7nX.06>QUEAGwT@+;#`xOCB2m@PT<Ko MhPm_*W7t?^+!W+ofUm@4r');

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
