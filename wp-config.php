<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_ochard' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('DISABLE_WP_CRON', false);

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '^C !=bIC6|NEfd`5B1N]TMFod|!M}9VH;53$S^JE*ICy@i)%b@oZBVv@{Y f&[rV' );
define( 'SECURE_AUTH_KEY',  ';KdF}6[ruNEL[> y-f>r=*#-lU`!yK3bU+#|CQebL9&8ySD`Y%-$xtHc5pp9Tv)%' );
define( 'LOGGED_IN_KEY',    'M|>,BZkU06yXNd!;W?%lq3?GQLs+-~iA2#f7>a_a@B$$RWEoQ2;2ZuE3&,$Zq[AR' );
define( 'NONCE_KEY',        'aeV0<cf:+U(Tw-e Y^w,]$X2wo@E3VimOydxEVsyM)kmYE@+ALX8cYk1g2$*&9zH' );
define( 'AUTH_SALT',        'CoOVVSR;JxM>gOqZ~.z,8/QC8=kgQ*.Ptmmb56:B/ceV5&JLiy$:q(,f;vTJx;Z(' );
define( 'SECURE_AUTH_SALT', 'fKMb+r[uLD*1uu!j3}6zf<Upq417*k5l2=.[eV|,Yfd/NNGnz/xfEB5@?%{FhlZx' );
define( 'LOGGED_IN_SALT',   '=(LzwW#Y@^RX+.7jLQbHYx0[z!Uy,[]d~3r.r^CXSx[1(I+v&IU2pB6^IR>1DO`6' );
define( 'NONCE_SALT',       '=0M6NOg1^mH4pR/BHouka6`@8?X;<fXHij#eS?i;B4b&.,hwf}0WBq&i6mSWqy#/' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';



