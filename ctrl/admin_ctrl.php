<?php
include __DIR__."/../model/admin_model.php";
class admin_ctrl extends admin_model{
  public function addHelpSection(){
    if(!isset($_POST)){
      return "no data";
    }else{
      if(isset($_POST["help_title"]) && $_POST["help_title"]!=""){
          $title = $_POST["help_title"];
      }else{
        return "Title is required";
      }
      if(isset($_POST["help_content"]) && $_POST["help_content"]!=""){
         $content = $_POST["help_content"];
      }else{
        return "Content is required";
      }
      if(isset($_POST["help_parent"])){
        $parent = $_POST["help_parent"];
      }else{
        $parent = 0;
      }
      $query = $this->addSection($title,$content,$parent);
      if($query){
        return true;
      }else{
        return false;
      }
    }
  }
  public function deleteHelpSection(){
    if(isset($_POST["dlt"])){
      if(is_numeric($_POST["dlt"])){
        $query = $this->deleteSectionForever($_POST["dlt"]);
        if($query != false){
          return "Goodbye section. You will be missed.";
        }else{
          return "Failed to delete the section. Sorry.";
        }
      }
    }
  }
  public function editHelpSection($id){
    if(!isset($_POST)){
      return "no data";
    }else{
      if(isset($_POST["help_title"]) && $_POST["help_title"]!=""){
          $title = $_POST["help_title"];
      }else{
        return "Title is required";
      }
      if(isset($_POST["help_content"]) && $_POST["help_content"]!=""){
         $content = $_POST["help_content"];
      }else{
        return "Content is required";
      }
      if(isset($_POST["help_parent"])){
        $parent = $_POST["help_parent"];
      }else{
        $parent = 0;
      }
      $query = $this->editSection($title,$content,$parent,$id);
      if($query){
        return true;
      }else{
        return false;
      }
    }
  }
  public function getHelpSection($type){
    $query = $this->getSections($type);
    if($query !== "false"){
       return $query;
    }else{
       return false;
    }
  }
  public function uploadImage(){
  }
  public function getLayoutOptions(){
      global $layout, $elastic_config;
      $display = array();
      $display[]="<ul id='adm-vw-layout'>";
      foreach($layout as $container => $systemlayout){
        $width = ((100/count($systemlayout))-4)."%";
        $display[] = "<ul><h1>".$container."</h1>";
          foreach($systemlayout as $sys => $sysvalues){
            $display[]="<ol style='background:".$elastic_config[$sys]['color']."; width:".$width."'><h2>".$elastic_config[$sys]['title']."</h2>";
            foreach($sysvalues as $v){
              $display[] = "<li>".$v."</li>";
            }
            $display[] = "</ol>";
          }
       $display[]="</ul>";
      }
      $display[]="</ul>";
      $display = implode(" ", $display);
      return $display;
  }
  
  public function getActiveUsers(){
    $users = $this->getUsers("active");
    if($users !== false){
      //  Determine how long it's been since they where active
      foreach($users as $uk => $uv){
        $idle = $this->determineUserIdleTime($uv[0]);
        $users[$uk][0]=$idle;
      }
      $active_user_display = array();
      foreach($users as $au_key => $au_vals){
        $active_since = $au_vals[0];
        $user_full_name = $au_vals[1];
        $active_user_display[] = "<li>".$user_full_name." : Last Active ".$active_since." ago</li>";
      }
    return implode(" ",$active_user_display);
    }else{
      return "No Active Users.";
    }
  }
  function determineUserIdleTime($unix){
    $idle = time()-$unix;
    if($idle < 60){
      return $idle . "Seconds";
    }else if($idle > 60){
      $idle =  $idle/60;
      $tm = "Minutes";
      if($idle > 60){
        $idle = $idle/60;
        $tm = "Hours";
        if($idle > 24){
          $idle = $idle / 24;
          $tm = "Days";
        }
      }
      if(round($idle)==1){
        $tm = substr($tm,0,-1);
      }
      return round($idle)." ".$tm;
    }
  }
}
?>
