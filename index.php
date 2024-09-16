<?php
require_once('Controller/PageController.php');
require_once('Model/BaseModel.php');
require_once('View/TemplateRenderer.php');

$model = new BaseModel();
$template = new TemplateRenderer();

$allProducts = $model->getAllProducts();

echo $template->render($allProducts, "index");

//$asd = file_get_contents('templates/home/home.html');
//echo $asd;
?>
