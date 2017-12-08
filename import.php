<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			import depuis la page
*               https://affiliate.gearbest.com/home/link/hottest-deals
*/

use App\Utils;
use App\Item;
use App\ItemManager;
use App\Pagination;

require_once( "inc/prepend.php" );
// Récupération des variables
$action				= Utils::get_input('action','both');
$title				= Utils::get_input('title','post');
$affiliate_link		= Utils::get_input('affiliate_link','post');
$photo_link			= Utils::get_input('photo_link','post');
$promo_price		= Utils::get_input('promo_price','post');
$discount			= Utils::get_input('discount','post');
$code				= Utils::get_input('code','post');

$item_manager = new ItemManager($bdd);

$today 		= date("Y-m-d H:i:s"); 
$tomorrow 	= date("Y-m-d H:i:s", strtotime('+1 day'));
$shop 		= "gearbest";
$status		= "published";

switch($action) {
	
	case "import" :
		// tester avec le affiliate_link si l'annonce n'est pas deja connue
		
		$data = array(  "id" => -1, // creation
						"created" => $today, 
						"expired" => $tomorrow, 
						"title" => $title, 
						"link" => "", 
						"affiliate_link" => $affiliate_link, 
						"content" => "", 
						"photo_link" => $photo_link, 
						"code" => $code, 
						"normal_price" => "", 
						"promo_price" => $promo_price, 
						"discount" => $discount, 
						"quantity" => "", 
						"shop" => $shop, 
						"status" => $status);

		$item_manager->saveItem(new Item($data));
		$log->notification($translate->__('the_item_has_been_saved'));
		Utils::redirection("items.php");
		break;

	default:
		//$smarty->assign("titre", $translate->__('list_of_items'));
		$smarty->assign("content", "bo/items/import.tpl.html");
		$smarty->display("bo/main.tpl.html");
}
require_once( "inc/append.php" );
?>