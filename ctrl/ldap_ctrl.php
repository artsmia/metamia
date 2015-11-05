<?php
if(isset($_POST['uid']) && isset($_POST['upass'])){
    include __DIR__."/../include/config.php";
    include __DIR__."/../model/ldap_model.php";
    $dap = new model_ldap();
    $user = $_POST['uid'];
    $pass = $_POST['upass'];
    /*if($user == "debug" && $pass == "debug")
    {
      include_once(__DIR__."/../include/db.php");
      $database = new Database();
      include_once(__DIR__."/../include/sessions.php");
      $session = new Session();
      $_SESSION['uid']="debug";
      $_SESSION['user']="debug";
      $_SESSION['status']="logged in";
      $_SESSION['access']="admin";
      $data['error']=false;
      echo json_encode($data); exit();
    }
    */

    $request = $dap->ldappass($user,$pass);

    if($request != false) {
        include_once(__DIR__."/../include/db.php");
        $database = new Database();
        include_once(__DIR__."/../include/sessions.php");
        $session = new Session();
        $_SESSION['status']="logged in";
        $data['error']=false;
        //check for username
        if(is_array($request)){
           $_SESSION['uid']=$request['username'];
           $_SESSION['user']=$request['fullname'];
           if(isset($request['access'])){
             $_SESSION['access']=$request['access'];
           }
        }
        echo json_encode($data);
    }
    else {
        $data['error']=true;
        $data['msg']="Invalid Username or Password";
        echo json_encode($data);
    }
}
else {
  die("No direct access.");
}
?>
