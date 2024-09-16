<?php
require_once('Controller/LogController.php');
$login = new LogController();

 if($login->validate()=="TRUE"){
 	echo $login->logIn();
 }

?>