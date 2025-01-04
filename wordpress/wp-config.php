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
define( 'DB_NAME', 'wp_learn_sphere' );

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
define( 'AUTH_KEY',         'W#!)fm8G^GLLWW{|)y6@1m!Etr?yLY~Yt0FEbOvACSc.Gi|M$4Jq.mk{BAyiO}lB' );
define( 'SECURE_AUTH_KEY',  'y@8-L`hl)p:)UDjmCLwI7(ytZFv&gmqo;_;*Ayrl=F(WARj*VQ6$DtrC ;D3:jBS' );
define( 'LOGGED_IN_KEY',    '0;iubEo}}t|r=_FiR:3o[PQDYBO[%CS2-]P=_nhmtp]a+.Rm4}w(|&rYDY+;@R]a' );
define( 'NONCE_KEY',        'k/D+r=`cAdlJYMz,gIqS1uj21x@C_k+FshV;1w:{MgRkLM.~V9rgWdcX}xud,)2T' );
define( 'AUTH_SALT',        'IcDj`w=D[jpz@zT4k1aqNKTG9=~}G]@5|~;k6[/ok/cD(W0aAWt@D^N?SCzUYOc}' );
define( 'SECURE_AUTH_SALT', '2vYKmUGFb1zML:{*oK|BJ6M/N~<-(X_c+QlBwcAak?/^S V(Sm84[aO<YExT=lp>' );
define( 'LOGGED_IN_SALT',   '8vAaN@MY;h+tW@RCE|s~9J4x(gLFe*8&nPbj`eKStQ6!rv!fAKEp!zN~XvG8TJ2_' );
define( 'NONCE_SALT',       'bq5X/^;dnLr<>Yq|+,<y#_l=@Ad_KVaB:@Bzk%;_Bi ;AQY69Ou>tTe;XDxwjqf?' );

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
$table_prefix = 'learnsphere_';

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
