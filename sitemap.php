<?php
/**
* @project		Dinero
* @author		Olivier Gaillard
* @version		1.0 du 11/12/2014
* @desc			Generate sitemap.xml
*/
header('Content-type: application/xml');

$properties_filepath = realpath(dirname (__FILE__)).'/inc/properties/'.$_SERVER["HTTP_HOST"].'/properties.ini';
if (!is_file($properties_filepath)) die("Unbale to find :  ".$properties_filepath);
$prop = parse_ini_file($properties_filepath);




// configuration
$url_prefix = 'http://www.jtbdeals.com';

$link = mysqli_connect($prop['db_hostname'],$prop['db_username'],$prop['db_password'],$prop['db_name']);


echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

echo '<url>';
echo '  <loc>http://www.jtbdeals.com</loc>';
echo '  <lastmod>'.date('c',time()).'</lastmod>';
echo '  <priority>1.00</priority>';
echo '</url>';

// categories
$sql = 'SELECT * FROM categories WHERE parent = 0 ORDER BY sort';
$result = mysqli_query($link, $sql);      
while($row = mysqli_fetch_array($result))  { 

    echo '<url>';
    echo '  <loc>'.$url_prefix.'/'.$row['path'].'</loc>';
    echo '  <lastmod>'.date('c',time()).'</lastmod>';
    
    echo '  <changefreq>daily</changefreq>';
    echo '  <priority>0.8</priority>';
    echo '</url>';

    }
// sous-categories
$sql = 'SELECT cat.path as cp, sub.path as sp FROM categories as cat 
        LEFT OUTER JOIN categories as sub on sub.parent = cat.id
        WHERE cat.parent = 0 ORDER BY sub.sort';
$result = mysqli_query($link, $sql);      
while($row = mysqli_fetch_array($result))  { 

    echo '<url>';
    echo '  <loc>'.$url_prefix.'/'.$row['cp'].'/'.$row['sp'].'</loc>';
    echo '  <lastmod>'.date('c',time()).'</lastmod>';
    echo '  <changefreq>daily</changefreq>';
    echo '  <priority>0.8</priority>';
    echo '</url>';

    }

// Autres liens    
$links = array("about", "company", "contact_us", "reviews", "disclaimer", "terms_of_use", "privacy_policy");
foreach ($links as $link) {
    echo '<url>';
    echo '  <loc>'.$url_prefix.'/'.$link.'</loc>';
    echo '  <lastmod>'.date('c',time()).'</lastmod>';
    echo '  <changefreq>monthly</changefreq>';
    echo '  <priority>0.5</priority>';
    echo '</url>';
}
echo '</urlset>';
