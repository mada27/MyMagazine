<?php
require_once('Model/BaseModel.php');
require_once('View/TemplateRenderer.php');
$model = new BaseModel();
$template = new TemplateRenderer();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['pk'];
    $userDetailId = $model->orderDetailInsert($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['phoneNumber'], $_POST['address'],
        $_POST['city'], $_POST['additionalInfo']);
    $products = $model->getUserCartByUserId($userId);
    foreach ($products as $product) {
        $model->orderInsert($userId, $product['productId'], $product['quantity'], 'aprobat', $userDetailId);
        $model->updateStock($product['productId'], $product['quantity']);
        $model->cartDeleteItem($userId, $product['productId']);
    }
    sleep(3);
    header('Location: ' . 'my-order.php');
} else {
    echo $template->render('', "checkout");
}
?>