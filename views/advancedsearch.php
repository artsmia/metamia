<?php
  include(__DIR__."/../include/config.php");
  include(__DIR__."/../include/db.php");
  include(__DIR__."/../include/sessions.php");
  include(__DIR__."/templates/header.php");
  ?>
  <form id="advanced-search" method="POST">
  <?php
  function findglobal($key, $array){
      $map=array("unique"=>array(),"globals"=>array());
      foreach($array as $k => $v){
        foreach($v['properties'] as $prop => $kv){
            if(!array_key_exists($prop,$map["unique"])){
               if(isset($kv['type'])){$kv=$kv['type'];}
               $map["unique"][$prop]=$kv;
            }else{
               $map["globals"][$prop]=$kv['type'];
            }
        }
      }
      foreach($map["globals"] as $gk=>$gv){
         if(array_key_exists($gk,$map["unique"])){
            unset($map["unique"][$gk]);
         }
      }
      return $map;
  }
  $mappings = json_decode($search->getMappings(), true);
echo("<pre>");
//var_dump($mappings);
echo("</pre>");
  $display = array();
  foreach($mappings as $mkey=>$mval){
      //find out whats global
      $map = findglobal($mkey, $mval['mappings']);
      //push index to main nav
      $display['nav'][]=$elastic_config[$mkey]['title'];
      $display[$mkey]['sub_nav']=array("global");

      //start building the display
      $display[$mkey]['content']="<fieldset class='advs-fs fs-".$elastic_config[$mkey]['title']."' style='background:".$elastic_config[$mkey]['color']."'>".
      "<h2>" . $elastic_config[$mkey]['title'] . "</h2>";
      //if globals are set
      if(isset($map['globals']) && !empty($map['globals'])){
          $display[$mkey]['content'].="<fieldset><h3>Globals</h3>";
          //append an input for each of the globals
          foreach($map['globals'] as $mpk => $mpv){
              $addrange = "";
              $datepicker="";
              $inptype = "text";
              if($mpv == "date" || $mpv == "integer" || $mpv =="number"){
                 $addrange = "<a class='rng' alt='".$mpk."'>=</a>";
                 switch($mpv){
                   case "integer":
                   case "number":
                   $inptype = "number";
                   break;
                   case "date":
                   $inptype = "date";
                   break;
                   default:
                   $inptype = "text";
                   break;
                 }
              }
              if($mpv == "date"){
                 $datepicker = "<script>jQuery('#".preg_replace('/\s+/', '', $mpk)."').datepicker({dateFormat:'yy-mm-dd'});</script>";
              }
              $display[$mkey]['content'].="<label><span>".$mpk."</span><a alt='".$mpk."' class='ornot'>&</a>".$addrange."<input type='".$inptype."' class='filter' id='".preg_replace('/\s+/', '', $mpk)."' name='filter[".$mpk."][]'/>".$datepicker."</label>";
          };

          $display[$mkey]['content'].="</fieldset>";
      }
      $display[$mkey]['content'].="<fieldset>";
      foreach($map['unique'] as $upk => $upv){
          $addrange = "";
          $inptype = "text";
          if($upv == "date" || $upv == "integer"){
             $addrange = "<a class='rng' alt='".$upk."'>=</a>";
             $inptype = "text";
             switch($upv){
                   case "integer":
                   case "number":
                   $inptype = "number";
                   break;
                   case "date":
                   $inptype = "date";
                   break;
                   default:
                   $inptype = "text";
                   break;
                 }
          }
          $display[$mkey]['content'].="<label><span>".$upk."</span><a alt='".$upk."' class='ornot'>&</a>".$addrange."<input type='".$inptype."' class='filter' id='".$upk."' name='filter[".$upk."][]'/></label>";
      }
     $display[$mkey]['content'].="</fieldset></fieldset>";
  }
  ?>
  <div id="advs-srch-view"></div>
  <ul id="advs-main-nav">
  <?php
  foreach($display['nav'] as $nav){
    echo("<li id='adv-".$nav."'>".$nav."</li>");
  }
  ?>
  </ul>
  <?php
    foreach($elastic_config as $k => $v){
      echo($display[$k]['content']);
    }
  ?>
  <input type="submit" value="Submit">
  </form>
  <?php
  include(__DIR__."/templates/sidebar.php");
  include(__DIR__."/templates/footer.php");
?>
