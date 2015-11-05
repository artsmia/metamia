<?php
include __DIR__."/include/config.php";
include __DIR__."/include/db.php";
include __DIR__."/include/sessions.php";
$session = new Session();
if(isset($_SESSION) && isset($_SESSION['status']) && $_SESSION['status']=="logged in"){
header('Location: '.$base_url.'views/home.php');
}else{
header('Location:'.$base_url.'login.php');
}
?>
