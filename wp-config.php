<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wpproject' );

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
define( 'AUTH_KEY',         '/t{2S9*bjlf33,,9fzCNxe@zm[_L{YVsOP_KKiFh_1EPV1@>JKmzdMjeT,51&|If' );
define( 'SECURE_AUTH_KEY',  '^&,Y{TAbeMyw2w8J* {/MN&Fq2PVVW4C. txjTLv2iDR^>rxY8v,Y]0*M566g9Bl' );
define( 'LOGGED_IN_KEY',    '5f//`/<PcnngYZFTO/Pq&C_vmWZ1$X#,ktuIo,5*Zojp4SDajoN;AGC.:X=]Gbxj' );
define( 'NONCE_KEY',        'GaHjq+b^4h6N]hwXz&e8Ij^TnBk]#XXwywXu 7yM*;s&+:V+^iY5,jU@M`CU)ZRw' );
define( 'AUTH_SALT',        'ZNgSj4bESFI`R)K4OnGOc+z!#_zR*B;RMIlI4r?tjB9U#{p<z<dSvCiKGXC.k@^R' );
define( 'SECURE_AUTH_SALT', 'uA*qUK^)9b ]syx[59cQHfvh,n+P#!1?r8Z9qx<Ok=v[_e<$7?<BHUv!mC51EQ1B' );
define( 'LOGGED_IN_SALT',   '`z*5nS-ir]=c,YUrx993qQ()eow`:n#s:ee:SaT3f;ve*|#@q/>|,m}UE{GFJs|!' );
define( 'NONCE_SALT',       '1ewLbu$=+j1mY|b]39nuC7GCkP].@QK-R#/WJ*xj;H1$^Fi,ppWTWgx8ZM8|;2UQ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
