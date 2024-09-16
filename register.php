<?php
require_once('Controller/RegisterController.php');

$register = new RegisterController();

if($register->validate() === true){
  echo $register->register();
}

?>
