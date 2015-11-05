<?php
include __DIR__."/../model/redis.php";

if(!isset($_POST) || empty($_POST)){

    die("no post data");

}else{
    if(isset($_POST['type'])){
       $mr = new RedisModel();
       //Define Query
        switch($_POST["type"]){
            //  recall search
            case "getSearch": $query = $mr->getSearch($_POST['UserId'],$_POST['search_type'],$_POST['search_id']); break;
            //  add cart item
            case "addItem": $query = $mr->updateCartItem($_POST['UserId'],$_POST['cart_id'],$_POST['item_id'],$_POST['item_title']); break;
            //  save a query
            case "addQuery": $query = $mr->addQuery($_POST['UserId'],$_POST['sUrl'],$_POST['title']); break;
            //  remove cart item
            case "removeItem" : $query = $mr->removeCartItem($_POST['UserId'],$_POST['cart_id'],$_POST['item_id']); break;
            //Save Cart
            case "saveCart" : $query = $mr->saveCart($_POST['UserId'], $_POST['cart_title'], $_POST['cart_id']); break;
            //  Delete Cart
            case "deleteCart": $query = $mr->deleteCart($_POST['UserId'],$_POST['cart_id'], $_POST['cart_type']); break;
            //  generate link for sharing
            case "genUrl": $query = $mr->generateUrl($_POST['UserId'],$_POST['url_val'],$_POST['url_type']); break;
            //  delete search
            default:
              return false;
            break;
        }
        //Check return status and Return
        if($query['error'] !== true){
          if(isset($query['cart_title'])){
            $sdata['cart_title']=$query['cart_title'];
          }
          if(isset($query['type'])){
            $sdata['type']=$query['type'];
          }
          $sdata['results']=$query['content'];
          $sdata['error']=false;
        }else{
          $sdata['results']="Error";
          $sdata['error']=true;
        }
        echo(json_encode($sdata));
    }
}
?>
