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
            case "ADD":
                $productStock = $model->getProductStock($productId);
                if (!$model->verifyCartInsertDuplicate($userId, $productId)) {
                    if ($productStock[0]['quantity'] > 0) {
                        $model->cartInsertItem($userId, $productId);
                    } else {
                        echo '
                        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                         $(document).ready(function() {
                                    swal({
                                        position: "top-end",
                                        icon: "error",
                                        title: "Nu mai sunt produse disponibile în stoc",
                                        timer: 2500
                                    })
                                })
                    </script>
                        ';
                    }
                } else {
                    $newQuantity = $model->getCartStock($userId, $productId);
                    if ($productStock[0]['quantity'] >= $newQuantity['quantity'] + 1) {
                        $model->cartAddQuantityItem($userId, $productId);
                    } else {
                        echo '
                        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                         $(document).ready(function() {
                                    swal({
                                        position: "top-end",
                                        icon: "error",
                                        title: "Nu mai sunt produse disponibile în stoc",
                                        timer: 2500
                                    })
                                })
                    </script>
                        ';
                    }
                }
                break;
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
                } else {
                    echo '
                        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                         $(document).ready(function() {
                                    swal({
                                        position: "top-end",
                                        icon: "error",
                                        title: "Nu mai sunt produse disponibile în stoc",
                                        timer: 2500
                                    })
                                })
                    </script>
                        ';
                }
                break;
            case "INP":
                $inpValue = $_GET[$x[0]."//".$x[1]."//".$x[2]];
                if (is_numeric($inpValue) && $inpValue > 0 && $inpValue == round($inpValue)) {
                    $productStock = $model->getProductStock($productId);
                    if ($productStock[0]['quantity'] >= $inpValue) {
                        $model->cartSetQuantityItem($userId, $productId, $inpValue);
                        echo '
                        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                         $(document).ready(function() {
                                    swal({
                                        position: "top-end",
                                        icon: "success",
                                        title: "Coșul a fost actualizat!",
                                        timer: 2500
                                    })
                                })
                    </script>
                        ';
                    } else {
                        echo '
                        <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
                        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
                        <script>
                         $(document).ready(function() {
                                    swal({
                                        position: "top-end",
                                        icon: "error",
                                        title: "Cantitatea introdusă de produse nu este disponibiă în stoc",
                                        timer: 2500
                                    })
                                })
                    </script>
                        ';
                    }
                }
                break;
        }
    }
    echo $template->render($model->getProductById($productId), "view");
}
?>