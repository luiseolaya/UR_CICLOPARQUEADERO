<?php

// Encabezados para evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");


$inactividad = 900; //segundos


if (session_status() === PHP_SESSION_NONE) {
    // Establece duración de sesión y cookie
    ini_set('session.gc_maxlifetime', $inactividad);
    session_set_cookie_params($inactividad);
    session_start();
}




//  Control de inactividad

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $inactividad) {
    session_unset();     // limpia variables de sesión
    session_destroy();   // destruye la sesión
    session_start();
    $_SESSION['inactividad'] = 'Ha superado el tiempo de inactividad, su sesión fué cerrada por seguridad';

    header("Location: /cicloparqueaderos/"); // redirige al login
    exit();
}









$_SESSION['LAST_ACTIVITY'] = time(); // actualiza el tiempo de sesion
