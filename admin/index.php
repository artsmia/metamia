<?php
include_once(__DIR__."/../include/config.php");
include_once(__DIR__."/../include/db.php");
include_once(__DIR__."/../include/sessions.php");
include_once(__DIR__."/../include/global_functions.php");
include __DIR__."/../views/templates/header.php";
if(isset($_POST['dlt']) && $_POST['dlt'] !=""){
  echo($ctrl->deleteHelpSection($_POST['dlt']));
}
if(!isset($_SESSION['access']) || $_SESSION['access']!="admin"){die("You are not an admin! Get out of here!");}
?>
<section id="admin-wrap">
  <h2>Admin</h2>
  <ul id="admin-actions">
    <li class="admin-activeusers">
      <h2>Active Users</h2>
      <ul>
        <?php echo($ctrl->getActiveUsers()); ?>
      </ul>
    </li>
    <li class="admin-helpsections">
      <h2>Manage Help</h2>
      <ul>
        <li>
          <a href="<?php echo $base_url?>admin/pages/manage-help.php" class="admin-nav-help">Create New</a>
          <?php
            $sections = $ctrl->getSections("all");
            foreach($sections as $k => $v){
              $sdsp[] = "<li>".$v['help_title'].
                " | <a href='".$base_url."admin/pages/manage-help.php?e=".$v['help_id']."'>Edit</a>".
                "| <a class='dlt-section' alt='".$v['help_id']."'>[x]</a>";
            }
            echo ("<ul><h4>Sections</h4>".implode(" ",$sdsp)."</ul>");
          ?>
        </li>
      </ul>
    </li>
    <li class="admin-elasticsearch">
      <h2>ElasticSearch</h2>
      <?php echo $elastic_url;?>
      <ul>
      <li>
      <h3>Allowed Indices</h3>
      <?php echo "<ul><li>".implode("</li><li>",explode(",",$allowed_indexes))."</li></ul>";?>
      </ii>
      <li>
      <h3>Allowed Indice Types</h3>
      <?php echo "<ul><li>".implode("</li><li>",explode(",",$allowed_types))."</li></ul>";?>
      </li>
      <li>
      <h3>Layout</h3>
      <ul>
        <li>
          <a href="<?php echo $base_url?>admin/pages/view-mapping.php" class="admin-nav-mapview">View Layout Settings</a>
        </li>
      </ul>
      </li>
    </li>
  </ul>
<div id="centralspace">

</div>
</section>
<script type="text/javascript">
jQuery(".dlt-section").click(function(e){
  var doesit = confirm("Are you absolutely positive about this?");
  if(doesit){
    var identifier = jQuery(this).attr('alt');
    data = {"dlt":identifier};
    deleteSection(data);
  }
});
var deleteSection = function(data){
 jQuery.ajax({
    type:"POST",
    data:data,
    success:function(data){
       jQuery("body").empty().append(data);
    },
    error:function(){
      alert("error");
    },
  });
}
</script>
<?php
include __DIR__."/../views/templates/footer.php";
?>

