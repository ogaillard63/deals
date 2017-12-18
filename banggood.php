<?php
/**
* @project		jtbdeals
* @author		Olivier Gaillard
* @version		1.0 du 28/11/2017
* @desc			import CSV banggood
*/

use App\Utils;
use App\Item;
use App\ItemManager;
use App\Pagination;

require_once( "inc/prepend.php" );
// Récupération des variables
$action				= Utils::get_input('action','both');
$data				= Utils::get_input('data','post');

$item_manager = new ItemManager($bdd);

$today 		    = date("Y-m-d H:i:s"); 
$in_2_weeks 	= date("Y-m-d H:i:s", strtotime('+14 day'));
$shop 		    = "banggood";
$status		    = "imported";

switch($action) {
	
	case "import" :
        $rows = str_getcsv($data, "\n"); //parse the rows 
        foreach($rows as &$row) {
            $line = str_getcsv($row, ","); //parse the items in rows 
            if (is_numeric($line[0])) {
            
            $item_data = array(  "id" => -1, // creation
                "created" => $today, 
                "expired" => $in_2_weeks, 
                "title" => $line[1], 
                "link" => $line[3], 
                "affiliate_link" => $line[6], 
                "content" => "", 
                "photo_link" => $line[2], 
                "code" => "", 
                "normal_price" => "", 
                "promo_price" => trim(str_replace("US$", "", $line[4])), 
                "discount" => 0, 
                "quantity" => 0, 
                "shop" => $shop, 
                "status" => $status);
                
                //var_dump($item_data);
                $item_manager->saveItem(new Item($item_data));
                //$log->notification($translate->__('the_item_has_been_saved'));
            }
        }
        Utils::redirection("items.php");
        break;

	default:
		//$smarty->assign("titre", $translate->__('list_of_items'));
		$smarty->assign("content", "bo/items/banggood_import.tpl.html");
		$smarty->display("bo/main.tpl.html");
}
require_once( "inc/append.php" );
?>