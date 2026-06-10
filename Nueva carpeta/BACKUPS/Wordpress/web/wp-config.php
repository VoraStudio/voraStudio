<?php


/**
 * Configuración base de WordPress.
 */

// ** Configuración de la base de datos ** //
define('DB_NAME','278011941wordpress20251112132423');
define('DB_USER','myvorastuda9');
define('DB_PASSWORD','7t02j1tb');
define('DB_HOST','localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// ** Claves de autenticación ** //
define('AUTH_KEY','i.f*iW] T,{4#MCGr`m!GY]vO:6kW0x~By~}p(L|,C-EM|:MS&UX1dQ&bAUgy(n}');
define('SECURE_AUTH_KEY','n|Y}g$o^P-0}v(cO<p @DTl !>+b;CAA_L+23(V{^R0`J,{vELz#/8KL2~W-xY3q');
define('LOGGED_IN_KEY',':?~NVsPDm4}O+e4*AEC$X`63++_]G&WA&qv4bj6`e-5c6{J}xpa6W[@Q`d{eBJNs');
define('NONCE_KEY','ZN^i*5]8?4l06+1J@(![|,gax]e]vF I-~kfI`GQ|#^fQ:Xbw-+_CE svLTR9lTq');
define('AUTH_SALT','V]04O+=5{G=k[ivg:=m2$UZ=0T;_-|q&-_r}x>~GAZ=]+3Y><jIPQ|h|dgPbu~p}');
define('SECURE_AUTH_SALT','|!Ia{7^?H/m]XxM?MkG)B/-I|JI&$&RpD-gs9Xd@;XZ{V6?>~f&MGe):-KgKXS(O');
define('LOGGED_IN_SALT','Sv [T#+q5=*NL@Inwt(VYi9<]IIdY)#N:PEb3!M=)N3^+xX<c[>%Vz:NR(6EUiU&');
define('NONCE_SALT','%W|qJN>A(F)ng9P^+baV3t$LY22ap[3MAv#Q&=H-`GeKC0&GXx#UR5&FRkB.=+8U');

/** Prefijo de la base de datos **/
$table_prefix = 'wp_';

/** Activar modo de depuración (true para desarrollo, false en producción) **/
define('WP_DEBUG', false);

/** Evitar editar archivos desde el panel de administración **/
define('DISALLOW_FILE_EDIT', true);

/** Configuración absoluta del directorio de WordPress. **/
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}

/** Configura WordPress. **/
require_once ABSPATH . 'wp-settings.php';

//Autoupdates enabled
define( 'WP_AUTO_UPDATE_CORE', true );

//Not populate themes/plugins by WordPress.
define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );
