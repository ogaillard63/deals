<?php
/**
 * @project		WebApp Generator
 * @author		Olivier Gaillard
 * @version		1.0 du 04/06/2012
 * @desc	   	Initialisation des ressources
 */

use App\Session;
use App\Logger;
use App\UserAuth;
use App\Translator;

// Autoload
require 'vendor/autoload.php';

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

setlocale(LC_ALL, 'fr_FR');
date_default_timezone_set('Europe/Paris');

define('DS', '/');
define('PATH_APP', 			realpath(dirname (__FILE__).'/..'));
define('PATH_INC', 			PATH_APP.'/inc');
define('PATH_LANG',			PATH_APP.'/lang');
define('PATH_PROPERTIES', 	PATH_INC.'/properties');
define('PATH_CLASSES', 		PATH_INC.'/classes');

// Paramètres de l'application
$properties_filepath = PATH_PROPERTIES.DS. $_SERVER["HTTP_HOST"].DS.'properties.ini';
if (!is_file($properties_filepath)) die("Unbale to find :  ".$properties_filepath);
$prop = parse_ini_file($properties_filepath);

// Paramètres des chemins d'accès
define('PATH_TPL_RELATIVE',		'tpl'); 
define('PATH_TPL', 				PATH_APP.DS.PATH_TPL_RELATIVE.DS); // Dossier des templates de vues

// Session
$session = Session::getInstance();

// Initialisation du gestionnaire de templates
$smarty = new Smarty;
$smarty->setTemplateDir(PATH_TPL);
$smarty->config_dir  =  'lang';
$smarty->compile_dir  = 'tpl_cache';
$smarty->compile_check = true;
$smarty->force_compile = true;
$smarty->assign('tpl', PATH_TPL_RELATIVE);
$array = explode(DIRECTORY_SEPARATOR, dirname (__FILE__));
$path_app = "/".$array[count($array)-2];
$smarty->assign('path_app', $path_app);
//$smarty->assign('path_app', "");


// Connexion à la base de données
$bdd = new PDO("mysql:host=".$prop['db_hostname'].";port=".$prop['db_port'].";dbname=".$prop['db_name'].";charset=utf8", $prop['db_username'], $prop['db_password']);
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Log
$log = new Logger($bdd, $smarty, $session);

// Authentification
$user = new UserAuth($bdd);
if ($user->isLogged())
	$smarty->assign("user_logged", $session->getValue("firstname")." ".$session->getValue("lastname"));
// Profils
define('SUPER_ADMIN', 		300);
define('ADMIN', 			200);
define('USER', 				100);

// Gestion des traductions
$_SESSION['filePathLang'] = PATH_LANG.DS."fr.txt";
if(isset($_GET['cnt'])) {
	if (in_array($_GET['cnt'], array('fr','en')))
		$_SESSION['filePathLang'] = PATH_LANG.DS.$_GET['cnt'].".txt";
	}
$translate = new Translator($_SESSION['filePathLang']); 

// Gestion des notifications
if ($alert = $session->getValue("alert")) {
	$smarty->assign("alert", $alert);
	$session->unsetKey("alert");
}

// Génération du menu dynamique
foreach (glob(PATH_APP. "/*.php") as $filename) {
	$fi = pathinfo($filename);
	if ($fi['filename'] != "index") {
		$smarty->append("choices", array("link" => $fi['basename'],"title" => $fi['filename']));
	}
}

?>