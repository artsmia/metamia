<?php
include_once( __DIR__."/../include/config.php");
//header('Content-Type: application/json');

//$redis = new redis(); //Instantiate global redis object

Class RedisModel{
    private $redis;
    public function __construct(){
        global $rds;
        $this->redis = new redis();
        //$this->redis->connect('54.173.69.28',6379);
        $this->redis->connect($rds['server'],$rds['port']);
        $this->redis->select($rds['db']);
    }
    //------------------------------------+
    //  Set user for first time savers    |
    //------------------------------------+
    private function firstTimer($user){
      // create new user and set search count;
      $this->redis->hSet(md5($user), "cart_count",1);
      $this->redis->hSet(md5($user).":saved_carts",0,"Temp.");
    }

    // Get Current cart for user
    private function currentCart($user){
      if($this->redis->keys(md5($user))){
        $current_cart = $this->redis->hGet(md5($user),"cart_count");
        if($current_cart != false){
          return $current_cart;
        }
      }
      return 0;
    }
    //--------------------------+
    //    add a new cart item   |
    //--------------------------+
    public function updateCartItem($user, $cart_id, $item_id, $item_title){
        //  if the user is not in the database already then this is their first item.
        if(!$this->redis->keys(md5($user))){$this->firstTimer($user);}

        //Set the value
        $setVal = $this->redis->hSet(md5($user).":cart_".$cart_id,$item_id,"$item_title");
        if(!$setVal){
          $data['error']=true;
          $data['content']=false;
          return $data;
        }else{
          if($cart_id==0){
            // if this is the temp index Set TTL
            $now = time(NULL);
            $this->redis->expireAt(md5($user).":cart_".$cart_id, $now + (4*604800));
          }
          //get the updated items to return
          $data['error']=false;
          if($cart_id==0){
            $data['cart_title']="Temp.";
          }else{
            $data['cart_title'] = $this->redis->hGet(md5($user).":saved_carts", $cart_id);
          }
          $data['content'] = $this->redis->hGetAll(md5($user).":cart_".$cart_id);
          return $data;
        }
    }
    //------------------------+
    //    Remove cart Item    |
    //------------------------+
    public function removeCartItem($user, $cart_id, $item_id){
      $data['error']=true;
      $dlt = $this->redis->hDel(md5($user).':cart_'.$cart_id, $item_id);
      if($dlt != false){
       // $updated_cart = $this->redis->hGetAll($user.":cart_".$cart_id);
        $data['error']=false;
        $data['content']=$item_id;
      }
      return $data;
    }
    private function exists($user, $value, $set){
       $exists = false;
       $search = $this->redis->hGetAll(md5($user).$set);
       foreach ($search as $k => $v){
         if($v == $value){
           $exists = true;
         }
       }
       return $exists;
    }
    //-----------------------------------+
    //    Save Temp Cart as Perm Cart    |
    //-----------------------------------+
    public function saveCart($user,$cart_title,$cart_id){
      //  First time?
      if(!$this->redis->keys(md5($user))){$this->firstTimer($user);}
      if($this->exists(md5($user), $cart_title, ":saved_carts")){
        $data['error']=true;
        $data['content'] = $cart_title." already exists. Please choose another name.";
        return data;
      };
      // Get current cart count and next count
      $curcart = $this->redis->hGet(md5($user),"cart_count");
      $nextcartid = $curcart++;
      // Add cart to carts array
      if($this->redis->hSet(md5($user).":saved_carts", $nextcartid, "$cart_title") !== false){
        // Get values stored in temp cart
        $vals = $this->redis->hGetAll(md5($user).":cart_0");
        //save the vals and reindex cart as perm
        if($this->redis->hMSet(md5($user).":cart_".$nextcartid,$vals)!==false){
          //clear temp cart
          if($this->redis->hIncrBy(md5($user),"cart_count",1)!=false){
            $this->redis->delete(md5($user).":cart_0");
            //  return updated carts
            $data['error']=false;
            $data['content'] = $this->redis->hGetAll(md5($user).":saved_carts");
            return $data;
          }
          return "failed to increment";
        }
      }
      $data['error']=true;
      $data['content']="Failed to save";
      return $data;
    }

    //-------------------+
    //    Delete Cart    |
    //-------------------+
    public function deleteCart($user,$cart_id,$cart_type){
      $data['error']=true;
      if($cart_id == 0 && $cart_type != "query"){
        $hk = $this->redis->hKeys(md5($user).":cart_".$cart_id);
        if($hk != null || $hk != false){
          foreach($hk as $k => $v){
            $this->redis->hDel(md5($user).":cart_".$cart_id, $v);
          }
        }
        $data['error']=false;
        $data['content']= $cart_id;

        return $data;

      }else if($cart_type == "cart" && $cart_id != 0){
        // delete cart record
        if($this->redis->delete(md5($user).":cart_".$cart_id)){
          // remove cart from carts array
          if($this->redis->hDel(md5($user).":saved_carts",$cart_id)){
            $data['error']=false;
            $data['type']="cart";
            $data['content']=$cart_id;
          }
        }
      }else if($cart_type == "query"){
         if($this->redis->hDel(md5($user).":saved_search",$cart_id)){
           $data['error'] = false;
           $data['type'] = "query";
           $data['content']=$cart_id;
         }
      }
      return $data;
    }

    //------------------+
    //    Save Query    |
    //------------------+
    public function addQuery($user,$url,$title){
      if(!$this->redis->keys(md5($user))){$this->firstTimer($user);}
      $data['error']=true;
      //save search
      if(strpos($url,"?")){
        $url = substr($url, strpos($url,"?"));
      }
      $save_search = $this->redis->hSet(md5($user).":saved_search",$title,$url);
      if($save_search){
        $getval = $this->redis->hGetAll(md5($user).":saved_search");
        $data['error']=false;
        $data['content']=$getval;
      }
      return $data;
    }


    //    Generate a Url to retrieve saved serach.
    //----------------------------------------------
    public function generateUrl($user,$url_val,$url_type){
        global $base_url;
        $data["error"]=true;
        switch($url_type){
          case "query": $data['content'] = $base_url."views/home.php".$url_val; $data["error"] = false; break;
          case "cart":
            $data['content'] = $base_url."views/home.php?svsrch=".md5($user).":".$url_val;$data["error"]=false; break;
          default: return false; break;
        }
        return $data;
    }

    //-----------------------------+
    //    Recall A saved Search    |
    //-----------------------------+
    public function getSearch($user,$search_type,$search_id){
      $data['error']=true;
      switch($search_type){
        case "all":
         $ct = array();
         $carts = $this->redis->hGetAll(md5($user).":saved_carts");
         if(!empty($carts) && $carts != false){ $ct = $carts;};
         $queries = $this->redis->hGetAll(md5($user).":saved_search");
         if(!empty($queries) && $queries != false){
           $ct = $ct + $queries;
         }
         $data['content'] = $ct;
         break;
        case "cart":
          if($search_id == 0){
            $data['cart_title']="Temp.";
          }else{
            $data['cart_title']=$this->redis->hGet(md5($user).":saved_carts",$search_id);
          }
          $data['content'] = $this->redis->hGetAll(md5($user).":cart_".$search_id);
        break;
        case "carts": $data['content'] = $this->redis->hGetAll(md5($user).":saved_carts");break;
        case "queries": $data['content'] = $this->redis->hGetAll(md5($user).":saved_search");break;
        default: return $data['error']=true; break;
      }
      if($data['content'] !== false){
        $data['error']=false;
        if(empty($data['content'])){
          $data['content'] = array("no results");
        }
      }
      return $data;
    }
    public function getSavedSearch($usr,$srch){
      return $this->redis->hGetAll($usr.":cart_".$srch);
    }
}
?>
