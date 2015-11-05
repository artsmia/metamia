<?php
  class Session{
    private $db;
    public function __construct(){
      $this->db = new Database;
      session_set_save_handler(
        array($this, "_open"),
        array($this, "_close"),
        array($this, "_read"),
        array($this, "_write"),
        array($this, "_destroy"),
        array($this, "_gc")
      );
      session_start();
    }
    public function _open(){
      if($this->db){
        return true;
      }
      return false;
    }
    public function _close(){
      if($this->db->close()){
        return true;
      }
      return false;
    }
    public function _read($id){
      $this->db->query('SELECT session_data FROM sessions WHERE session_id = :id');
      $this->db->bind(':id', $id);
      if($this->db->execute()){
        $row = $this->db->single();
        return $row['session_data'];
      }else{
        return '';
      }
    }
    public function _write($id, $data){
      $access = time();
      $this->db->query('REPLACE INTO sessions VALUES (:id, :access, :data, :status)');
      $this->db->bind(':id', $id);
      $this->db->bind(':access', $access);
      $this->db->bind(':data', $data);
      $this->db->bind(':status', 0);
      if($this->db->execute()){
        return true;
      }
        return false;
      }
    public function _destroy(){
      $this->db->query('DELETE FROM sessions WHERE session_id = :id');
      $this->db->bind(':id', session_id());
      if($this->db->execute()){
        return true;
      }
      return false;
    }
    public function _gc(){
      // Calculate what is to be deemed old
      $this->db->query('DELETE FROM sessions WHERE session_status < :old');
      $this->db->bind(':old', time()-3600);
      if($this->db->execute()){
        return true;
      }
        return false;
    }
    public function _exists($username){
      $this->db->query('SELECT user_session_id FROM users WHERE user_session_id = :id AND user_name=:usr');
      $this->db->bind(':id', session_id());
      $this->db->bind(':usr',$username);
      if($this->db->execute()){
        $row = $this->db->single();
        return $row['session_id'];
      }else{
        return false;
      }
    }
   public function getUser(){
    $sid=session_id();
    $this->db->query("SELECT * FROM users WHERE user_session_id = :sid");
    $this->db->bind(':sid',$sid);
    if($this->db->execute()){
        return $this->db->single();
    }else{
        return "";
    }
   }
  }
?>
