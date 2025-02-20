<?php

namespace App\Controllers;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class LogoutController {
    public function logout() {
        session_destroy();
        header("Location: /UR_CICLOPARQUEADERO/inicio_sesion");
        exit;
    }
}

if (isset($_POST['logout'])) {
    $logoutController = new LogoutController();
    $logoutController->logout();
}
?>
