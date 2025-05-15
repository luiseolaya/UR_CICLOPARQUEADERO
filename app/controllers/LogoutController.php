<?php

namespace App\Controllers;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class LogoutController {
    public function logout() {
		 $_SESSION = [];
        session_destroy();
		   // Eliminar cookies relacionadas con la sesión
			   if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
			}
		
		
        header("Location: /cicloparqueaderos/");
        exit;
    }
}

if (isset($_POST['logout'])) {
    $logoutController = new LogoutController();
    $logoutController->logout();
}
?>
