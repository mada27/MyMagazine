<?php
require_once('View/TemplateRenderer.php');
require_once('Model/BaseModel.php');
$model = new BaseModel();
$template = new TemplateRenderer();
echo $template->render('', "orders");

?>
