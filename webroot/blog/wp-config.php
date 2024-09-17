<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache





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
define( 'DB_NAME', 'blog_wordpress' );

/** Database username */
define( 'DB_USER', 'blog_wordpress' );

/** Database password */
define( 'DB_PASSWORD', '&57Q86jrk' );

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
define( 'AUTH_KEY',         'CTCuDz{cP<#hF?A0:cNuVTtnTL#K{4g+=BET8XIJ^ ^@7^FB^;{G&,Vwe.hdz?|d' );
define( 'SECURE_AUTH_KEY',  'rA/B ~QoTJtEn7E0WD-X9+Cn}|X`^M&g7aWZ$A;D]>S,pBM:,Tby;JDIyBoMoI7H' );
define( 'LOGGED_IN_KEY',    'eN`g (_@DL5zO~[!dFTW^4q.V:!fti@PNufnLFXN+!OKKO,sp4VL94C:SS=$U{}=' );
define( 'NONCE_KEY',        'f5u2&0}HVJY3hS.54=FIcZ(Y#:OIU?3g/K)6Fx%?SJCqI1B2Pa2 KrNtMZ3_4+gs' );
define( 'AUTH_SALT',        '.8]kYkaoF0le6I[;ox`p)2`FS<hO,@7EAi16t-jMdJ19S;/<n{}/-Z=SMMPcfWQ[' );
define( 'SECURE_AUTH_SALT', '[4buivwCv9!CrwV6]4Pv?K2<5C;t~!6,#(k_yxL@9=1~u6/ia.N.Ui}B]Uf-vOsx' );
define( 'LOGGED_IN_SALT',   's`Zw3C-+pX4Iu5ka8b/},@R4^#l| OZG0j5k0-ciK1Pke}+4m4Rb.0x(i>eUBpfM' );
define( 'NONCE_SALT',       'P/g4toe$bqL{:;z;OKrT>naW).?^1(C~)#mSZ6pA#fOh${WK|#0CzO]Uz_{t46/P' );

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