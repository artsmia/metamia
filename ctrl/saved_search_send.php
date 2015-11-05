<?php
include(__DIR__."/search-ctrl.php");
$search = new Search();
if(isset($_POST['svslink']) && isset($_POST['svsto'])){
    $link = $_POST['svslink'];
    $to = $_POST['svsto'];
    $from = $_POST['user'];
    $uid=$_POST['uid'];
    echo $search->mailSavedSearch($link,$to,$from,$uid);
}else{
  $data['error']=true;
  $data['msg']="Email failed to send";
  echo json_encode($data);
}
?>
