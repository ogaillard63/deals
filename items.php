<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			Controleur des objets : items
*/

use App\Utils;
use App\Session;
use App\Item;
use App\ItemManager;
use App\Pagination;

require_once( "inc/prepend.php" );
$user->isLoggedIn(); // Espace privé

// Récupération des variables
$action				= Utils::get_input('action','both');
$page				= Utils::get_input('page','both');
$id					= Utils::get_input('id','both');
$created			= Utils::get_input('created','post');
$expired			= Utils::get_input('expired','post');
$title				= Utils::get_input('title','post');
$link				= Utils::get_input('link','post');
$affiliate_link		= Utils::get_input('affiliate_link','post');
$content			= Utils::get_input('content','post');
$photo_link			= Utils::get_input('photo_link','post');
$code				= Utils::get_input('code','post');
$normal_price		= Utils::get_input('normal_price','post');
$promo_price		= Utils::get_input('promo_price','post');
$discount			= Utils::get_input('discount','post');
$quantity			= Utils::get_input('quantity','post');
$shop				= Utils::get_input('shop','post');
$status				= Utils::get_input('status','post');

$query				= Utils::get_input('query','post');

$item_manager = new ItemManager($bdd);
$session->unsetkey("referrer");

switch($action) {
	
	case "add" :
		$smarty->assign("item", new Item(array("id" => -1)));
		$smarty->assign("content", "bo/items/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;
	
	case "edit" :
		$smarty->assign("item", $item_manager->getItem($id));
		$smarty->assign("content","bo/items/edit.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "search" :
		$smarty->assign("content","bo/items/search.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "search_results" :
		if (strlen($query) > 2) {
			$smarty->assign("items", $item_manager->searchItems($query));
		}
		else {
			$log->notification($translate->__('query_too_short'));
			Utils::redirection("items.php?action=search");
		}
		$smarty->assign("query",$query);
		$smarty->assign("content","bo/items/search.tpl.html");
		$smarty->display("bo/main.tpl.html");
		break;

	case "save" :
		// Calcul la reduction
		if ($discount == 0 && $promo_price > 0 && $normal_price > 0) { 
			$discount = (1-($promo_price/$normal_price))*100;
		}
		$data = array("id" => $id, "created" => $created, "expired" => $expired, "title" => $title, "link" => $link, "affiliate_link" => $affiliate_link, "content" => $content, "photo_link" => $photo_link, "code" => $code, "normal_price" => $normal_price, "promo_price" => $promo_price, "discount" => $discount, "quantity" => $quantity, "shop" => $shop, "status" => $status);
		$item_manager->saveItem(new Item($data));
		$log->notification($translate->__('the_item_has_been_saved'));
		if ($session->getValue("referrer")) Utils::redirection($session->getValue("referrer"));
		Utils::redirection("items.php");
		break;

	case "delete" :
		$item = $item_manager->getItem($id);
		if ($item_manager->deleteItem($item)) {
			$log->notification($translate->__('the_item_has_been_deleted'));
		}
		if ($session->getValue("referrer")) {
			$redirect = $session->getValue("referrer");
			$session->unsetkey("referrer");
			Utils::redirection($redirect);
			}
		Utils::redirection("items.php");
		break;

	default:
		$smarty->assign("titre", $translate->__('list_of_items'));
		$rpp = 10;
		if (empty($page)) $page = 1; // Display first page
		$smarty->assign("items", $item_manager->getItemsByPage($page, $rpp));
		$pagination = new Pagination($page, $item_manager->getMaxItems(), $rpp);
		$smarty->assign("btn_nav", $pagination->getNavigation());
		
		$smarty->assign("content", "bo/items/list.tpl.html");
		$smarty->display("bo/main.tpl.html");
}
require_once( "inc/append.php" );
?>