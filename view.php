<?php
require_once('View/TemplateRenderer.php');
require_once('Model/BaseModel.php');
$productId = $_GET['productId'];
$model = new BaseModel();
$template = new TemplateRenderer();
$allProducts = $model->getAllProducts();
//echo $template->render($allProducts,$model->getProductById($productId));
echo $template->render($model->getProductById($productId), "view");


?>