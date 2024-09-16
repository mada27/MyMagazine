<?php
require_once('Model/BaseModel.php');
require_once('View/TemplateRenderer.php');
$model = new BaseModel();
$template = new TemplateRenderer();
foreach ($_GET as $key => $value) {
    $x = explode("//", $key);
}
$userId = $x[0];
if ($userId == '{{_userId_}}') {
    header('Location: ' . 'login.php');
} else {
    if ($userId > 0) {
        $productId = $x[1];
        $decide = $x[2];
        if ($decide === "ADD") {
            if (!$model->verifWishlistAdd($userId, $productId)) {
                $model->wishlistInsert($userId, $productId);
            }
        } else {
            $model->wishlistRemove($userId, $productId);
        }
    }
    echo $template->render($model->getProductById($productId), "view");
}

?>