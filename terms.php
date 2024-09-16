<?php
require_once('Model/BaseModel.php');
require_once('View/TemplateRenderer.php');
$template = new TemplateRenderer();
echo $template->render('',"terms");
?>
