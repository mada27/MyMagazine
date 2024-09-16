<?php

//require_once('Controller/LogController.php');
class TemplateRenderer
{

    public $template, $defaultTemplate, $allProducts, $productTemplate, $termsTemplate, $productViewTemplate, $myOrdersTemplate, $checkoutTemplate;

    public function __construct()
    {
        $this->defaultTemplate = file_get_contents('templates/template.html');
    }

    public function render($data, $var)
    {
        $model = new BaseModel();
        $product = file_get_contents('templates/product.html');
        $loggedMenu = file_get_contents('templates/logged/menu.html');
        session_start();
        if (empty($_SESSION['email'])) {
            $username = 'Conectare';
            $this->defaultTemplate = str_replace('{{ logged }}', '', $this->defaultTemplate);
            $this->defaultTemplate = str_replace('{{ wishlist }}', '', $this->defaultTemplate);
            $this->defaultTemplate = str_replace('{{ shopCart }}', '', $this->defaultTemplate);
            $this->defaultTemplate = str_replace('{{ LOG }}', '../login.php', $this->defaultTemplate);
            $product = str_replace('{{ checkoutForm }}', "Please log in", $product);
        } else {
            $this->defaultTemplate = str_replace('{{ LOG }}', '#', $this->defaultTemplate);
            $shopCart = file_get_contents('templates/cart/cart.html');
            $wishlistCart = file_get_contents('templates/wishlist/wishlist.html');
            $username = $_SESSION['email'];
            $userId = $_SESSION['userId'];
            $userWishlist = $model->getWishlistByUserId($userId);
            $wishlist = '';
            foreach ($userWishlist as $key => $value) {
                $wishlistCartProduct = file_get_contents('templates/wishlist/wishlist-product.html');
                $wishlistCartProduct = str_replace('{{ productName }}', $value['name'], $wishlistCartProduct);
                $wishlistCartProduct = str_replace('{{ userIdWishlist }}', $userId, $wishlistCartProduct);
                $wishlistCartProduct = str_replace('{{ wishListProductId }}', 'view.php?productId=' . $value['productId'], $wishlistCartProduct);
                $wishlistCartProduct = str_replace('{{ productIdWishlist }}', $value['productId'], $wishlistCartProduct);
                $wishlist .= $wishlistCartProduct;
            }
            $wishlistCart = str_replace('{{ wishlistProducts }}', $wishlist, $wishlistCart);
            $allCartProducts = '';
            $getUserCart = $model->getUserCartByUserId($userId);
            $cartTotalValue = 0;
            $cartTotalItems = 0;
            foreach ($getUserCart as $key => $value) {
                $cartProduct = file_get_contents('templates/cart/cart-product.html');
                $cartProduct = str_replace('{{ cart_userId }}', $userId, $cartProduct);
                $cartProduct = str_replace('{{ cart_productId }}', $value['productId'], $cartProduct);
                $cartProduct = str_replace('{{ cart_product_name }}', $value['name'], $cartProduct);
                $cartProduct = str_replace('{{ cart_product_image }}', $value['image'], $cartProduct);
                $cartProduct = str_replace('{{ cart_product_quantity }}', $value['quantity'], $cartProduct);
                $cartProduct = str_replace('{{ cart_product_calc_price }}', $value['price'] * $value['quantity'], $cartProduct);
                $cartTotalValue += $value['price'] * $value['quantity'];
                $cartTotalItems += $value['quantity'];
                $allCartProducts .= $cartProduct;
            }
            $shopCart = str_replace('{{ cart_total_items }}', $cartTotalItems, $shopCart);
            if ($cartTotalItems === 0) {
                $shopCart = str_replace('{{ go_to_checkout }}',  'index.php', $shopCart);
            } else {
                $shopCart = str_replace('{{ go_to_checkout }}',  'checkout.php', $shopCart);
            }
            if ($cartTotalItems > 1 || $cartTotalItems < 1) {
                $shopCart = str_replace('{{ cart_total_items_in }}', $cartTotalItems . ' Produse', $shopCart);
            } else {
                $shopCart = str_replace('{{ cart_total_items_in }}', $cartTotalItems . ' Produs', $shopCart);
            }
            $shopCart = str_replace('{{ cart_total_value }}', $cartTotalValue, $shopCart);
            $shopCart = str_replace('{{ cartProducts }}', $allCartProducts, $shopCart);
            if ($wishlist) {
                $wishlistCart = str_replace('{{ wishlistIcon }}', 'images/site/wishlist-icon.svg', $wishlistCart);
            } else {
                $wishlistCart = str_replace('{{ wishlistIcon }}', 'images/site/wishlist-empty-icon.svg', $wishlistCart);
            }
            $loggedMenu = str_replace('{{ firstNameLogged }}', $model->getUserFirstNameByEmail($username), $loggedMenu);
            $this->defaultTemplate = str_replace('{{ logged }}', $loggedMenu, $this->defaultTemplate);
            $this->defaultTemplate = str_replace('{{ wishlist }}', $wishlistCart, $this->defaultTemplate);
            $this->defaultTemplate = str_replace('{{ shopCart }}', $shopCart, $this->defaultTemplate);
            $checkoutForm = file_get_contents('templates/checkoutForm.html');
            $year = '';
            for ($i = 10; $i >= 0; $i--) {
                $x = '<option value="{{ year }}"></option>';
                $p = str_replace('{{ year }}', date("Y") + $i, $x);
                $year .= $p;
            }
            $product = str_replace('{{ userId }}', $userId, $product);
            $checkoutForm = str_replace('{{ year }}', date("Y"), $checkoutForm);
            $checkoutForm = str_replace('{{ years }}', $year, $checkoutForm);
            $checkoutForm = str_replace('{{ errors }}', "", $checkoutForm);
            $product = str_replace('{{ checkoutForm }}', $checkoutForm, $product);
            $product = str_replace('{{ userIdd }}', $userId, $product);
            //}
        }

        if ($var == "index") {
            if (is_string($data)) {
                $noFounds= file_get_contents('templates/no-products/no-products.html');
                $noFounds = str_replace('{{ searchString }}', $data, $noFounds);
                $this->defaultTemplate = str_replace('{{ content }}', $noFounds, $this->defaultTemplate);
            } else {
                $t = '';
                $this->allProducts = $data;
                foreach ($this->allProducts as $i => $v) {
                    $this->productTemplate = file_get_contents('templates/product-template.html');
                    $this->productTemplate = str_replace('{{ name }}', $v['name'], $this->productTemplate);
                    $this->productTemplate = str_replace('{{ image }}', $v['image'], $this->productTemplate);
                    if ($v['quantity'] > 0) {
                        $this->productTemplate = str_replace('{{ stockProduct }}', 'În stoc', $this->productTemplate);
                        $this->productTemplate = str_replace('{{ textColor }}', 'textColorGreen', $this->productTemplate);
                    } else {
                        $this->productTemplate = str_replace('{{ stockProduct }}', 'Stoc epuizat', $this->productTemplate);
                        $this->productTemplate = str_replace('{{ textColor }}', 'textColorRed', $this->productTemplate);
                    }
                    $this->productTemplate = str_replace('{{ price }}', $v['price'], $this->productTemplate);
                    $this->productTemplate = str_replace('{{ productId }}', $v['id'], $this->productTemplate);
                    $t .= $this->productTemplate;
                }
                $this->defaultTemplate = str_replace('{{ contentClass }}', 'row', $this->defaultTemplate);
                $this->defaultTemplate = str_replace('{{ content }}', $t, $this->defaultTemplate);
            }
        } elseif ($var == "view") {
            $a = $data;
            $this->productViewTemplate = file_get_contents('templates/product-view/product-view.html');
            $setWishlistButton = false;
            if (!empty($userWishlist)) {
                foreach ($userWishlist as $key => $value) {
                    if ($a[0][0] === $value['productId']) {
                        $setWishlistButton = true;
                    }
                }
            }
            if ($setWishlistButton) {
                $this->productViewTemplate = str_replace('{{ displayRemove }}', 'displayRemoveToWishlist', $this->productViewTemplate);
            } else {
                $this->productViewTemplate = str_replace('{{ displayAdd }}', 'displayAddToWishlist', $this->productViewTemplate);
            }
            if (!empty($_SESSION['userId'])) {
                $this->productViewTemplate = str_replace('{{ userId }}', $_SESSION['userId'], $this->productViewTemplate);
            }
            $this->productViewTemplate = str_replace('{{ productId }}', $a[0][0], $this->productViewTemplate);
            $this->productViewTemplate = str_replace('{{ name }}', $a[0][1], $this->productViewTemplate);
            $this->productViewTemplate = str_replace('{{ description }}', $a[0][2], $this->productViewTemplate);
            $this->productViewTemplate = str_replace('{{ price }}', $a[0][3], $this->productViewTemplate);
            $this->productViewTemplate = str_replace('{{ image }}', $a[0][4], $this->productViewTemplate);
            if ($a[0][9]) {
                $allImages = '';
                foreach ($a as $key => $value) {
                    $imageProduct = file_get_contents('templates/product-view/product-images.html');
                    $imageProduct = str_replace('{{ imageSrc }}', $value[9], $imageProduct);
                    $allImages .= $imageProduct;
                }
                $this->productViewTemplate = str_replace('{{ product-all-images }}', $allImages, $this->productViewTemplate);
            } else {
                $this->productViewTemplate = str_replace('{{ product-all-images }}', '', $this->productViewTemplate);
            }
            if ($a[0][7] > 5) {
                $this->productViewTemplate = str_replace('{{ stockProduct }}', 'ÎN STOC', $this->productViewTemplate);
                $this->productViewTemplate = str_replace('{{ colorStock }}', 'color-stock-green', $this->productViewTemplate);
            } elseif($a[0][7] <= 5 && $a[0][7] >= 2) {
                $this->productViewTemplate = str_replace('{{ stockProduct }}', 'ULTIMELE ' . $a[0][7] . ' PRODUSE', $this->productViewTemplate);
                $this->productViewTemplate = str_replace('{{ colorStock }}', 'color-stock-orange', $this->productViewTemplate);
            } elseif($a[0][7] == 1 ) {
                $this->productViewTemplate = str_replace('{{ stockProduct }}', 'ULTIMUL PRODUS ÎN STOC', $this->productViewTemplate);
                $this->productViewTemplate = str_replace('{{ colorStock }}', 'color-stock-red', $this->productViewTemplate);
            } else {
                $this->productViewTemplate = str_replace('{{ stockProduct }}', 'STOC EPUIZAT', $this->productViewTemplate);
                $this->productViewTemplate = str_replace('{{ colorStock }}', 'color-stock-red', $this->productViewTemplate);
                $product = str_replace('{{ disabled }}', 'disabledContent', $product);
            }
            $this->defaultTemplate = str_replace('{{ content }}', $this->productViewTemplate, $this->defaultTemplate);
        } elseif ($var == "terms") {
            $this->termsTemplate = file_get_contents('templates/terms/terms.html');
            $this->defaultTemplate = str_replace('{{ content }}', $this->termsTemplate, $this->defaultTemplate);
        } elseif ($var == "checkout") {
            $this->checkoutTemplate = file_get_contents('templates/checkout/checkout.html');
            $allCheckoutProducts = '';
            $getUserCart = $model->getUserCartByUserId($_SESSION['userId']);
            if (empty($getUserCart)) {
                header('Location: ' . 'index.php');
            }
            $totalCartValue = 0;
            foreach ($getUserCart as $key => $value) {
                $checkoutProduct = file_get_contents('templates/checkout/checkout-product.html');
                $checkoutProduct = str_replace('{{ checkout_product_id }}', $value['productId'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_userId }}', $value['userId'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_image }}', $value['image'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_name }}', $value['name'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_price }}', $value['price'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_quantity }}', $value['quantity'], $checkoutProduct);
                $checkoutProduct = str_replace('{{ checkout_product_price_total }}',$value['price'] * $value['quantity'], $checkoutProduct);
                $totalCartValue += $value['price'] * $value['quantity'];
                $allCheckoutProducts .= $checkoutProduct;
            }
            $this->checkoutTemplate = str_replace('{{ checkout_user_Id }}', $_SESSION['userId'], $this->checkoutTemplate);
            $this->checkoutTemplate = str_replace('{{ checkout_products_total_price }}', $totalCartValue, $this->checkoutTemplate);
            $this->checkoutTemplate = str_replace('{{ checkout_products }}', $allCheckoutProducts, $this->checkoutTemplate);
            $this->defaultTemplate = str_replace('{{ content }}', $this->checkoutTemplate, $this->defaultTemplate);
        } elseif ($var == "orders") {
            if (!empty($_SESSION['userId'])) {
                $getUserId = $_SESSION['userId'];
            } else {
                header('Location: ' . 'login.php');
            }
            $getUserOrders = $model->getOrdersByUser($getUserId);
            $dataArraySorted = array();
            $i = 0;
            $k = 0;
            foreach ($getUserOrders as $key => $value) {
                if( $key == 0) {
                    $dataArraySorted[$i][$k] = $value;
                }
                if($key > 0) {
                    $prev = $getUserOrders[$key - 1];
                    if ((substr($value['dateBuy'], 0, 10)) === (substr($prev['dateBuy'], 0, 10))) {
                        $k++;
                        $dataArraySorted[$i][$k] = $value;
                    } else {
                        $k = 0;
                        $i++;
                        $dataArraySorted[$i][$k] = $value;
                    }
                }
            }
            $this->myOrdersTemplate = file_get_contents('templates/my-orders/my-orders.html');

//            $allOrder = '';
//            foreach ($getUserOrders as $key => $value) {
//                $rowTemplate = file_get_contents('templates/my-orders/order.html');
//                $rowTemplate = str_replace('{{ user-order-id }}', 'item-order-id-' . $value['id'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_id_number }}', $value['id'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_stats }}', $value['status'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_product_image }}',$value['image'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_product_name }}',$value['name'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_product_value }}', $value['price'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_product_quantity }}', $value['quantityPurchased'], $rowTemplate);
//                $rowTemplate = str_replace('{{ user_order_total_values }}', $value['price'] * $value['quantityPurchased'], $rowTemplate);
//
//                $allOrder.=$rowTemplate;
//            }
//            $this->myOrdersTemplate = str_replace('{{ replacement_user_order }}',$allOrder,  $this->myOrdersTemplate);


            $allOrder = '';
            foreach ($dataArraySorted as $key => $value) {
                $rowTemplate1 = file_get_contents('templates/my-orders/order.html');
                $oneOrder = '';
                $orderStatus = (array) null;
                $calcTotalValueProducts = 0;
                $productCount = 0;
                $datePurchased = '';
                foreach ($value as $key2 => $value2) {
                    $rowTemplate = file_get_contents('templates/my-orders/same-day-order.html');
                    $rowTemplate1 = str_replace('{{ user-order-id }}', 'item-order-id-' . $value2['id'], $rowTemplate1);
                    $rowTemplate1 = str_replace('{{ user_orders_id_number }}', $value2['id'], $rowTemplate1);
                    array_push($orderStatus, $value2['status']);
                    $calcTotalValueProducts += $value2['price'] * $value2['quantityPurchased'];
                    $productCount += $value2['quantityPurchased'];
                    $datePurchased = $value2['dateBuy'];
                    $rowTemplate = str_replace('{{ user_order_product_image }}', $value2['image'], $rowTemplate);
                    $rowTemplate = str_replace('{{ user_order_product_name }}', $value2['name'], $rowTemplate);
                    $rowTemplate = str_replace('{{ user_order_product_value }}', $value2['price'], $rowTemplate);
                    $rowTemplate = str_replace('{{ user_order_product_quantity }}', $value2['quantityPurchased'], $rowTemplate);
                    $rowTemplate = str_replace('{{ user_order_total_values }}', $value2['price'] * $value2['quantityPurchased'], $rowTemplate);
                    $oneOrder .= $rowTemplate;
                }
                $ok = 1;
                foreach ($orderStatus as $values){
                    if ($values != 'aprobat') {
                       $ok = 0;
                    }
                }
                if($ok == 1) {
                    $displayStatusOrder = 'aprobat';
                } else {
                    $displayStatusOrder = 'anulat';
                }

                $datePurchased = substr($datePurchased, 0, 10);
                $x = explode("-", $datePurchased);
                $yearDate = $x[0];
                $monthDate = $x[1];
                if ($monthDate < 10) {
                    $monthDate = $x[1][1];
                }
                $dayDate = $x[2];
                $monthRo = array('Ianuarie', 'Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie');
                $finaleDate = $dayDate . ' ' . $monthRo[$monthDate-1] . ' ' . $yearDate;
                $rowTemplate1 = str_replace('{{ user_orders_stats }}', $displayStatusOrder, $rowTemplate1);
                $rowTemplate1 = str_replace('{{ user_orders_total_value }}', $calcTotalValueProducts, $rowTemplate1);
                $rowTemplate1 = str_replace('{{ user_orders_total_items }}', $productCount, $rowTemplate1);
                $rowTemplate1 = str_replace('{{ user_orders_purchased }}', $finaleDate, $rowTemplate1);
                $rowTemplate1 = str_replace('{{ same_day_order }}', $oneOrder, $rowTemplate1);
                $allOrder .=  $rowTemplate1;
            }
            $this->myOrdersTemplate = str_replace('{{ replacement_user_order }}', $allOrder, $this->myOrdersTemplate);



            $this->defaultTemplate = str_replace('{{ content }}', $this->myOrdersTemplate, $this->defaultTemplate);
        }

        if ($var != 'view'){
            $this->defaultTemplate = str_replace('{{ bodyClass }}', 'bodyProductList', $this->defaultTemplate);
        }


        // Categoriile
        $drop = '';
        $allCategories = $model->getAllCategories();
        foreach ($allCategories as $key => $v) {
            $dropmenu = file_get_contents('templates\categories.html');
            $dropmenu = str_replace('{{ categoryId }}', $v['id'], $dropmenu);
            $dropmenu = str_replace('{{ categoryName }}', $v['name'], $dropmenu);
            $drop .= $dropmenu;
        }
        $this->defaultTemplate = str_replace('{{ categoriesDropDown }}', $drop, $this->defaultTemplate);
        $this->defaultTemplate = str_replace('{{ sign }}', $username, $this->defaultTemplate);


        $this->defaultTemplate = str_replace('{{ footer }}', file_get_contents('templates/footer/footer.html'), $this->defaultTemplate);


        return $this->defaultTemplate;
    }


}


?>