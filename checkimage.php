<?php
include_once('../config/config.inc.php');
include_once( '../images.inc.php');
require_once '../classes/Image.php';
require_once '../classes/Product.php';
require_once 'klogger.class.php';

$myLog = new KLogger('/var/www/lineaufficio/import/logs', KLogger::DEBUG);

/*
 * Check products
 */
$myLog->logInfo("-- START CHECK PRODUCT IMAGES PROCEDURE --");

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$sqlProduct = 'SELECT * FROM ps_product LIMIT 1000';
$products = Db::getInstance()->ExecuteS( $sqlProduct );

foreach($products as $product)
{
	$productObj = new ProductCore($product['id_product']);
 	$id_lang = Configuration::get('PS_LANG_DEFAULT');
	$images = $productObj->getImages($id_lang);
	$ps_legacy_images = Configuration::get('PS_LEGACY_IMAGES');

	foreach($images as $image)
	{
		if ($ps_legacy_images)
		{
			$filename = _PS_PROD_IMG_DIR_ . $product['id_product'] . ($image['id_image'] ? '-' . $image['id_image'] : '') . '.jpg';
		}
		else
		{
			$imageIds = $productObj->id . "-" . $image['id_image'];
			$split_ids = explode('-', $imageIds);
			$id_image = (isset($split_ids[1]) ? $split_ids[1] : $split_ids[0]);
			$filename = _PS_PROD_IMG_DIR_ . Image::getImgFolderStatic($id_image) . $id_image . '.jpg';
		}

		if(!(file_exists($filename)))
		{
			$myLog->logError("Il file ".$filename." con ID_product -> ".$productObj->id." NON esiste");
			echo "Il file ".$filename." con ID_product -> ".$productObj->id." NON esiste"."<br>";
		}
		else
		{
// 			$myLog->logInfo("Il file ".$filename." con ID_product -> ".$productObj->id." esiste");
			;
		}
	}
}

$myLog->logInfo("-- END CHECK PRODUCT IMAGES PROCEDURE --");



/*
 * Check Categories
 */
$myLog->logInfo("-- START CHECK CATEGORY IMAGES PROCEDURE --");

$sqlCategory = 'SELECT * FROM ps_category LIMIT 1000';
$categories = Db::getInstance()->ExecuteS( $sqlCategory );

foreach($categories as $category)
{
	$categoryObj = new CategoryCore($category['id_category']);
	
	if($categoryObj->id_category != 1 && $categoryObj->id_category != 2 && $categoryObj->id_category != 3)
	{
		$filename1 = _PS_CAT_IMG_DIR_ . $categoryObj->id_category . ".jpg";
		$filename2 = _PS_CAT_IMG_DIR_ . $categoryObj->id_category . "-category_default.jpg";
		$filename3 = _PS_CAT_IMG_DIR_ . $categoryObj->id_category . "-medium_default.jpg";
		
		if(!(file_exists($filename1)) && !(file_exists($filename2)) && !(file_exists($filename3)))
		{
			$myLog->logInfo("La categoria con ID_category -> ".$categoryObj->id_category." NON esiste");
			echo "Le immagini".$filename1." ".$filename2." ".$filename3."della categoria con ID_category -> ".$categoryObj->id_category." NON esistono"."<br>";
		}
		else
		{
// 			$myLog->logInfo("Il file ".$filename1." con ID_category -> ".$categoryObj->id_category." esiste");
			;
		}
	}
}

$myLog->logInfo("-- END CHECK CATEGORY IMAGES PROCEDURE --");
