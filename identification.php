<?php
/**
 * @project		jtbdeals
 *
 * @author		Olivier Gaillard <ogaillard63@gmail.com>
 * @version		1.0 du 13/03/2014
 * @desc	   	Gestion de l'identification des users
 */

use App\Utils;
require_once( "inc/prepend.php" );

// Récupération des variables
$action			= Utils::get_input('action','both');

if ($action == "logout" || $action == "timeout") {
	$user->logout();
}
else {
	$smarty->assign("warning", $user->login()); // Authentification
	$smarty->assign("referer", (isset($_SERVER["HTTP_REFERER"]))?basename($_SERVER["HTTP_REFERER"]): null);
	$smarty->assign("titre", "Identification");
	$smarty->assign("content","bo/misc/login.tpl.html");
	$smarty->display("bo/main_light.tpl.html");
}

require_once("inc/append.php");
?>