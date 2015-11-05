<?php
define("SESSION_DELIM", "|");
class admin_model{
  private $db;
  function __construct(){
    $this->db = new Database();
  }

  function getSections($type){
    switch($type){
      case "all":
        $this->db->query("SELECT * FROM help");
        return $this->db->resultset();
      break;
      case is_numeric($type):
        $this->db->query("SELECT * FROM help WHERE help_id=:sid");
        $this->db->bind(":sid",$type);
        return $this->db->single();
      break;
      default: return false; break;
    }
  }

  function addSection($title,$content,$parent){
    $this->db->query("INSERT INTO help (help_title,help_content,help_parent) VALUES (:title,:content,:parent)");
    $this->db->bind(':title' , $title);
    $this->db->bind(':content', addslashes($content));
    $this->db->bind(':parent', $parent);
    $query = $this->db->execute();
    if($query){
      return true;
    }else{
      return false;
    }
  }
  function editSection($title,$content,$parent,$id){
    $this->db->query("UPDATE help SET help_title=:title,help_content=:content,help_parent=:parent WHERE help_id=:id");
    $this->db->bind(":title",$title);
    $this->db->bind(":content",$content);
    $this->db->bind(":parent",$parent);
    $this->db->bind(":id",$id);
    $query = $this->db->execute();
    if($query){
      return true;
    }
    return false;
  }
  function deleteSectionForever($section_id){
    $this->db->query("DELETE FROM help WHERE help_id = :id");
    $this->db->bind(":id",$section_id);
    return $this->db->execute();
  }

  function getUsers($who){
     switch($who){
       case "active":
         $this->db->query("SELECT * FROM sessions ORDER BY session_status DESC");
         $users = $this->db->resultset();
         if($users){
            $active_users = array();
            foreach($users as $user => $userdata){
              if(isset($userdata['session_data']) && $userdata['session_data'] != ""){
                $userdata['session_data']= $this->unserialize_session($userdata['session_data']);
                if($userdata['session_data']['status']=="logged in"){
                  $active_users[] = array($userdata['session_status'],$userdata['session_data']['user']);
                }
              }
            }
            return $active_users;
         }else{
            return false;
         }
       break;
       default:
         return false;
       break;
     }
  }
   function unserialize_session($session_data, $start_index=0, &$dict=null) {
   isset($dict) or $dict = array();

   $name_end = strpos($session_data, SESSION_DELIM, $start_index);

   if ($name_end !== FALSE) {
       $name = substr($session_data, $start_index, $name_end - $start_index);
       $rest = substr($session_data, $name_end + 1);

       $value = unserialize($rest);      // PHP will unserialize up to "|" delimiter.
       $dict[$name] = $value;

       return $this->unserialize_session($session_data, $name_end + 1 + strlen(serialize($value)), $dict);
   }

   return $dict;
}
}
?>
