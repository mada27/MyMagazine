<?php
require_once('View/TemplateRenderer.php');
require_once('Model/BaseModel.php');
$categoryId = $_GET['categoryId'];
$model = new BaseModel();
$template = new TemplateRenderer();
$allProducts = $model->getProductsByCategory($categoryId);
echo $template->render($allProducts,"index");
?>