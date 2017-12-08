<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 19/02/2017
* @desc			Controleur des objets : users
*/

use App\Utils;
use App\User;
use App\UserManager;
require_once( "inc/prepend.php" );

$user->isLoggedIn(); // Espace privé
$user->isAllowed(SUPER_ADMIN); // Espace privé


// Récupération des variables
$action			= Utils::get_input('action','both');
$id				= Utils::get_input('id','both');
$lastname		= Utils::get_input('lastname','post');
$firstname		= Utils::get_input('firstname','post');
$login			= Utils::get_input('login','post');
$email			= Utils::get_input('email','post');
$password		= Utils::get_input('password','post');
$password1		= Utils::get_input('password1','post');
$password2		= Utils::get_input('password2','post');
$profil_id		= Utils::get_input('profil_id','post');
$expiration		= Utils::dateToSql(Utils::get_input('expiration','post'));

$user_manager = new UserManager($bdd);

switch($action) {
	
	case "add" :
		$smarty->assign("user", new User(array("id" => -1)));
		$smarty->assign("content", "users/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("user", $user_manager->getUser($id));
		$smarty->assign("content","users/edit.tpl.html");
		$smarty->display("main.tpl.html");
		break;


	case "save" :
		if (strlen($password1)>0) {
			if ($password1 == $password2) $password = MD5($password1);	
			else {
				$log->notification($translate->__('passwords_dont_match'), 'error');
				Utils::redirection("users.php");
				}
			} 
		$data = array("id" => $id, "lastname" => $lastname, "firstname" => $firstname, "login" => $login, 
		"email" => $email, "password" => $password, "profil_id" => $profil_id, "expiration" => $expiration);
		$user_manager->saveUser(new User($data));
		$log->notification($translate->__('the_user_has_been_saved'));
		Utils::redirection("users.php");
		break;

	case "delete" :
		$user = $user_manager->getUser($id);
		if ($user_manager->deleteUser($user)) {
			$log->notification($translate->__('the_user_has_been_deleted'));
		}
		Utils::redirection("users.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_users'));
		$smarty->assign("users", $user_manager->getUsers());
		$smarty->assign("content", "users/list.tpl.html");
		$smarty->display("main.tpl.html");
}
require_once( "inc/append.php" );
?>