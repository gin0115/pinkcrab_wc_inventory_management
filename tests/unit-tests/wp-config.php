<?php

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/wordpress/' );

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress installation defined above.
 */
define( 'WP_DEFAULT_THEME', 'default' );

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define( 'DB_NAME'       , getenv( 'WP_DB_NAME' ) ?: 'wordpress_develop' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST'       , '127.0.0.1' );
define( 'DB_CHARSET'    , 'utf8' );
define( 'DB_COLLATE'    , '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 */
define( 'AUTH_KEY', '>xw<_ T#~]l2b5XP0=J+mwqV(:b12O8)U-^I]h=U1t1g,;@ZwFZEi}If w4-]1U6' );
define( 'SECURE_AUTH_KEY', 'MR/<J[=Jml<}c*;t0w8&8TsdwvQQzi1?kk{LYgfhI5O^J<yR./hAtws@{B,-z]{B' );
define( 'LOGGED_IN_KEY', '{B`(Ruj?F+Whd_-IdkS4aPhWW605~*?y;Pn1J((OC<y;>zM_8#7@L#rNl:K2HCNb' );
define( 'NONCE_KEY', 'I-&5jynYY%|A*uExt/2z)Sl}1SC6U|B>Ke?%Abd60[ii:GYMc3J;G6/~!$3RkC[W' );
define( 'AUTH_SALT', '0`!;X]-~1XP0]U}+sDOo6n6;oyo5tREG:9;`Ztxpa>< JpC^83=eAezCXp=SF`^x' );
define( 'SECURE_AUTH_SALT', 'AJ@A>[PAdh:DK%)KO*MX5pGr3~AAg5w6Q6mig_>tpGry`=+&gJ|nj}#TdK+@ H#(' );
define( 'LOGGED_IN_SALT', 'Hu[q-P?(mE/Xd6N|WODDciZ3y%1!x]Tu?GGYX|1dz St_*ousn3Hp!sStuSdM`j6' );
define( 'NONCE_SALT', 'C.Itr%;9R2d.JDkr[MJ(Qk!lX*=MRs?.FZ_dI:,9>pkk%4mtbWdcYWAG9[5MXFm<' );
define( 'WP_CACHE_KEY_SALT', '=>I?<qfV@v{y[v1)SUU_lOV35dQUUH5YJv(^$FFOcz(DQQTJkChRf?OUrd(WC_f>' );

$table_prefix = 'tests_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
