<?php

class BaseModel{
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbName = 'shop';
	private $conn;
	public $allProducts=[],$allCategories=[],$user=[],$id=[],$em=[],$wishlist=[],$stock=[], $cartItems=[];

	public function __construct(){
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbName);
		if ($this->conn->connect_error) {
   			die("Connection failed: " . $this->conn->connect_error);
		}
	}

	public function getAllProducts(){
		$result = $this->conn->query("SELECT * FROM products WHERE isDeleted = 0 ORDER BY categoryId ASC, name");
		if ($result->num_rows > 0) {
    			while($row = $result->fetch_assoc()){
    				array_push($this->allProducts,$row);
    			}
    		return $this->allProducts;
		}
		else{
			return $this->allProducts;
		}
	}

	public function getAllCategories(){
		$result = $this->conn->query("SELECT * FROM categories");
		if ($result->num_rows > 0) {
    			while($row = $result->fetch_assoc()) {
    				array_push($this->allCategories,$row);
    			}
    		return $this->allCategories;
		}
		else{
			return $this->allCategories;
		}
	}

    public function searchProducts($name){
        $result = $this->conn->query("SELECT * FROM products WHERE name LIKE '%$name%' AND isDeleted = 0 ORDER BY categoryId ASC, name");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                array_push($this->allProducts,$row);//array_values($row)
            }
            return $this->allProducts;
        }
        else{
            return $this->allProducts;
        }
    }

	public function validateUser($email,$password){
		$result = $this->conn->query("SELECT * FROM users WHERE email='$email'");
		if ($result->num_rows > 0) {
    		while($row = $result->fetch_assoc()) {
                if(password_verify($password, $row['password'])) {
                    array_push($this->user,$row);
                }
    		}
    		return $this->user;
		}
		else{
			return $this->user;
		}
	}

	public function getProductById($e){
	//	$result = $this->conn->query("SELECT * FROM products WHERE id=$e");
        $this->id = [];
		$result = $this->conn->query("SELECT products.*, product_image.images  FROM products left join product_image on products.id = product_image.productId WHERE products.id=$e");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		array_push($this->id,array_values($row));//array_values($row)
	    	}
	    	return $this->id;
		}
		else{
			return $this->id;
		}
	}

	public function getEmail($email){
		$result = $this->conn->query("SELECT email FROM users WHERE email='$email'");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		array_push($this->em,$row);//array_values($row)
	    	}
	    	return $this->em;
		}
		else{
			return $this->em;
		}
	}

	public function registerInsert($email,$password,$firstName,$lastName,$phone){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	 	$result= $this->conn->query("INSERT INTO users (email,password,firstname,lastname,phone,userType)
	         									 VALUES ('$email','$hashedPassword','$firstName','$lastName','$phone' ,'customer')");
	}

	public function productsInsert($name,$description,$price,$image){
	 	$result= $this->conn->query("INSERT INTO products (name,description,price,image)
											VALUES ('$name','$description','$price','$image')");
	}

	public function getProductsByCategory($e){
		$this->id=[];
		$result = $this->conn->query("SELECT * FROM categories INNER JOIN products ON categories.id=products.categoryId WHERE categories.id='$e' AND products.isDeleted = 0 
                                            ORDER BY products.name");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		array_push($this->id,$row);//array_values($row)
	    	}
	    	return $this->id;
		}
		else{
			return $this->id;
		}
	}

	public function getWishlistByUserId($id){
		$result = $this->conn->query("SELECT * FROM products INNER JOIN user_wishlist ON products.id=user_wishlist.productId WHERE user_wishlist.userId=$id");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		array_push($this->wishlist,$row);//array_values($row)
	    	}
	    	return $this->wishlist;
		}
		else{
			return $this->wishlist;
		}
	}

    public function getUserCartByUserId($id){
        $this->cartItems = [];
        $result = $this->conn->query("SELECT cart_item.*, products.name, products.image, products.price FROM cart_item left join products on cart_item.productId = products.id 
                                        WHERE cart_item.userId=$id");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                array_push($this->cartItems,$row);//array_values($row)
            }
            return $this->cartItems;
        }
        else{
            return $this->cartItems;
        }
    }

	public function getUserIdByEmail($email){
		$this->id=[];
		$result = $this->conn->query("SELECT id FROM users WHERE email='$email'");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		array_push($this->id,$row);//array_values($row)
	    	}
	    	return $this->id;
		}
		else{
			return $this->id;
		}
	}

    public function getUserFirstNameByEmail($email){
        $lastName = '';
        $result = $this->conn->query("SELECT firstname FROM users WHERE email='$email'");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $lastName = $row['firstname'];
            }
            return $lastName;
        }
        else{
            return $lastName;
        }
    }

	public function verifWishlistAdd($user,$product){
		$ok=0;
		$result = $this->conn->query("SELECT * FROM user_wishlist WHERE productId=$product AND userId=$user");
		if ($result->num_rows > 0) {
	    	while($row = $result->fetch_assoc()) {
	    		$ok=1;
	    	}
	    	return $ok;
		}
		else{
			return $ok;
		}
	}

	public function wishlistInsert($user,$product){
		$result= $this->conn->query("INSERT INTO `user_wishlist`(`userId`, `productId`) VALUES ('$user','$product')");
	}

	public function wishlistRemove($user,$product){
	 	$result= $this->conn->query("DELETE FROM `user_wishlist` WHERE `userId`=$user AND `productId`=$product");
	}

    public function cartInsertItem($user,$product){
        $result= $this->conn->query("INSERT INTO `cart_item`(`userId`, `productId`, `quantity`) VALUES ('$user','$product', 1)");
    }

    public function verifyCartInsertDuplicate($user,$product){
        $ok=0;
        $result = $this->conn->query("SELECT * FROM cart_item WHERE productId=$product AND userId=$user");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $ok=1;
            }
            return $ok;
        }
        else{
            return $ok;
        }
    }

    public function verifyCartQuantity($user,$product){
        $ok=0;
        $result = $this->conn->query("SELECT cart_item.quantity FROM cart_item WHERE productId=$product AND userId=$user");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if ($row['quantity'] > 1) {
                    $ok = 1;
                }
            }
            return $ok;
        }
        else{
            return $ok;
        }
    }

    public function getCartStock($user, $productId){
        $cartStock = '';
        $result = $this->conn->query("SELECT `quantity` FROM cart_item WHERE  productId=$productId AND userId=$user");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
               $cartStock = $row;
            }
            return $cartStock;
        }
        else{
            return $cartStock;
        }
    }

    public function cartAddQuantityItem($user,$product){
        $result= $this->conn->query("UPDATE `cart_item` SET quantity = quantity + 1 WHERE userId = $user AND productId = $product");
    }

    public function cartDeleteQuantityItem($user,$product){
        $result= $this->conn->query("UPDATE `cart_item` SET quantity = quantity - 1 WHERE userId = $user AND productId = $product");
    }

    public function cartDeleteItem($user,$product){
        $result= $this->conn->query("DELETE FROM `cart_item` WHERE `userId`=$user AND `productId`=$product");
    }

    public function cartSetQuantityItem($user, $product, $newQuantity){
        $result= $this->conn->query("UPDATE `cart_item` SET quantity = $newQuantity WHERE userId = $user AND productId = $product");
    }

    public function orderDetailInsert($firstName, $lastName, $email, $phone, $address, $city, $note){
        $sql = "INSERT INTO `user_order_details` (`firstName`, `lastName`, `email`, `phone`, `address`, `city`, `note`) 
                                            VALUES ('$firstName', '$lastName', '$email', '$phone', '$address', '$city', '$note')";
        if ($this->conn->query($sql) === TRUE) {
            return $this->conn->insert_id;
        }
    }

	public function orderInsert($user, $product, $quantity, $status, $detailId){
	 	$result= $this->conn->query("INSERT INTO `user_order`(`userId`, `productId`, `quantityPurchased`, `status`, `detailsId`) 
                                                VALUES ('$user', '$product', '$quantity', '$status', '$detailId' )");
	}

    public function getProductStock($productId){
        $this->stock = [];
        $result = $this->conn->query("SELECT `quantity` FROM products WHERE `id` = $productId");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                array_push($this->stock,$row);//array_values($row)
            }
            return $this->stock;
        }
        else{
            return $this->stock;
        }
    }
    public function updateStock($productId, $quantity){
        $productStock = $this->getProductStock($productId)[0]['quantity'] - $quantity;
        $result = $this->conn->query("UPDATE `products` SET `quantity` = $productStock WHERE `products`.`id` = $productId");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                array_push($this->stock,$row);//array_values($row)
            }
            return $this->stock;
        }
        else{
            return $this->stock;
        }
    }

    public function getOrdersByUser($id){
        $userOrder = [];
        $result = $this->conn->query("SELECT user_order.*, products.name, products.price, products.image
                            FROM user_order left join products on user_order.productId = products.id 
                            WHERE user_order.userId = $id ORDER BY user_order.dateBuy DESC");
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                array_push( $userOrder,$row);
            }
            return $userOrder;
        }
        else{
            return $userOrder;
        }
    }


}

?>