<?php include __DIR__."/include/db.php";
$db = new Database();
$expired =  time() - (12 * 60 * 60); //12 Hours ago
$db->query("DELETE FROM sessions WHERE session_status < " . $expired);
$query = $db->execute();
if(!$query){
  echo "Failed to remove sessions";
}
?>
