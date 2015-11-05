<?php
include_once(__DIR__."/include/config.php");
include_once(__DIR__."/include/db.php");
include_once(__DIR__."/include/sessions.php");
$Session = new Session();
$_SESSION['status']="logged Out";
$Session->_gc();
//session_destroy();
 header("Location:index.php");
 exit();
?>
