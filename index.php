<?php
/**
 * @project		jtbdeals
 * @author		Olivier Gaillard
 * @version		1.0 du 04/06/2012
 * @desc	   	Accueil
 */

use App\Utils;
use App\Item;
use App\ItemManager;
use App\CategoryManager;
use App\Pagination;

require_once( "inc/prepend.php" );
$action				= Utils::get_input('action','both');
$query				= Utils::get_input('query','both');
$page				= Utils::get_input('page','both');
$path			    = Utils::get_input('path','get');
$id					= Utils::get_input('id','both');

// Rate EUR/USD
$rate = 1.176; // EUR = USD / $rate


$item_manager       = new ItemManager($bdd);
$category_manager   = new CategoryManager($bdd);

$smarty->assign("titre", $translate->__('list_of_items'));
$smarty->assign("categories", $category_manager->getCategoriesToMenu());

// currency
if(!isset($_COOKIE["currency"])) {
    setcookie("currency", "USD", time() + (86400 * 180), "/"); 
}

$currency = $_COOKIE["currency"];
if ($path == "currency") {
	if ($currency == "USD") $currency = "EUR";
    else $currency = "USD";
    setcookie("currency", $currency, time() + (86400 * 180), "/"); 
	unset($path);
	}
$smarty->assign("currency", $currency);
$smarty->assign("rate", $rate);
 

// navigation
/*
$rpp = 12;
if (empty($page)) $page = 1; // Display first page
$smarty->assign("items", $item_manager->getItemsByPage($page, $rpp));
$pagination = new Pagination($page, $item_manager->getMaxItems(), $rpp);
$smarty->assign("btn_nav", $pagination->getNavigation());
*/
// no navigation

switch($action) {
    case "send" :
        // g-recaptcha-response
        // 6LeXHTwUAAAAAOmdB3mpKhyrmLDpms_Louh5O-Gr

        break;
    
    
    case "search" :
        if (strlen($query) > 2) {
            $smarty->assign("items", $item_manager->searchItems($query));
        }
        else {
            $log->notification($translate->__('query_too_short'), "error");
            Utils::redirection("index.php");
        }
        $smarty->assign("query",$query);
        $smarty->assign("content", "front/items/list.tpl.html");
        $smarty->display("front/main.tpl.html");
		break;

	default:
		
        switch ($path) {
            case 'about':
                $smarty->assign("content", "front/misc/about.tpl.html");
                break;

            case 'company':
                $smarty->assign("content", "front/misc/company.tpl.html");
                break;

            case 'contact_us':
                $smarty->assign("content", "front/misc/contact_us.tpl.html");
                break;

            case 'reviews':
                $smarty->assign("content", "front/misc/reviews.tpl.html");
                break;

            case 'disclaimer':
                $smarty->assign("content", "front/misc/disclaimer.tpl.html");
                break;

            case 'terms_of_use':
                $smarty->assign("content", "front/misc/terms_of_use.tpl.html");
                break;

            case 'privacy_policy':
                $smarty->assign("content", "front/misc/privacy_policy.tpl.html");
                break;

            default:
                $category_path = $category_manager->isValidePath($path);
               // Test la validité de la catégorie
                $smarty->assign("items", $item_manager->getItemsByCategory($category_path));
                if (strlen($category_path)>0) $smarty->assign("breadcrumb", $category_manager->makeBreadcrumb($category_path));

                $smarty->assign("content", "front/items/list.tpl.html");
        }

$smarty->display("front/main.tpl.html");
        
}
require_once( "inc/append.php" );

