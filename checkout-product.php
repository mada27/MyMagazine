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
        switch ($decide) {
            case "DELETE":
                $model->cartDeleteItem($userId, $productId);
                break;
            case "DOWN1":
                if($model->verifyCartQuantity($userId, $productId)) {
                    $model->cartDeleteQuantityItem($userId, $productId);
                } else {
                    $model->cartDeleteItem($userId, $productId);
                }
                break;
            case "UP1":
                $productStock = $model->getProductStock($productId);
                $newQuantity = $model->getCartStock($userId, $productId);
                if ($productStock[0]['quantity'] >= $newQuantity['quantity'] + 1) {
                    $model->cartAddQuantityItem($userId, $productId);
                }
                break;
            case "INP":
                $inpValue = $_GET[$x[0]."//".$x[1]."//".$x[2]];
                if (is_numeric($inpValue) && $inpValue > 0 && $inpValue == round($inpValue)) {
                    $productStock = $model->getProductStock($productId);
                    if ($productStock[0]['quantity'] >= $inpValue) {
                        $model->cartSetQuantityItem($userId, $productId, $inpValue);
                    }
                }
                break;
        }
    }
    echo $template->render('', "checkout");
}
?>