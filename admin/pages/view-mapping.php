<?php
include __DIR__."/../../include/config.php";
include __DIR__."/../../include/db.php";
include __DIR__."/../../include/sessions.php";
include __DIR__."/../../views/templates/header.php";
  foreach($assistant_filters as $filter_title => $source){
    $display[] = "<li><h3>" . $filter_title . "</h3><ul>";
    foreach($source as $source_key => $mapped_value){
      $display[]="<li><b>" . $source_key . "</b><ul>";
      if(is_array($mapped_value)){
      foreach($mapped_value as $mkey => $mval){
        $display[]="<li>" . $mval . "</li>";
      }
      }else{
        $display[]="<li>" . $mapped_value . "</li>";
      }
      $display[]="</ul></li>";
    }
    $display[]="</ul></li>";
  }
?>
<section id="help-mappings">
  <section>
  <h1>Query Assistant Mappings</h1>
  <?php  echo "<ul>".implode(" ", $display)."</ul>"; ?>
  </section>
  <section>
  <?php  echo $ctrl->getLayoutOptions();?>
  </section>
</section>
<?php include __DIR__."/../../views/templates/footer.php";?>
