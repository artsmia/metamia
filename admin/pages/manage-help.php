<?php
include_once(__DIR__."/../../include/config.php");
include_once(__DIR__."/../../include/db.php");
include_once(__DIR__."/../../include/sessions.php");
include(__DIR__."/../../views/templates/header.php");
?>
<section id="loaded-content">
  <?php
    //  If form was submitted
    if(isset($_POST['submit'])){
      //check if creating or editing
      if(!isset($_POST['edit'])){
        echo $_POST['edit'];
        $query = $ctrl->addHelpSection($_POST['edit']);
      }else{
        $query = $ctrl->editHelpSection($_POST['edit']);
      }
      if($query){
        echo "<span class='scc-msg'>Help Section Successfully Added</span>";
      }else{
        echo "Error: ".$query;
      }
    }

    // Help Section
    $sections = $ctrl->getSections("all");
    if(!empty($sections) && $sections !== "false"){
      $hdsp = array();
      foreach($sections as $key => $sectionvals){
        $hdsp[] = "<li><a href='".$base_url."admin/pages/manage-help.php?e=".$sectionvals['help_id']."'>".$sectionvals['help_title']."</a></li>";
      }
    }
    if(isset($_GET["e"])){
      $section_to_edit = $ctrl->getSections($_GET["e"]);
    }
  ?>
  <ul id="help-section-nav">
    <?php echo implode(" ",$hdsp);?>
    <li title="Create New Section" id="help-section-nav-new"><a href="<?php echo $base_url?>admin/pages/manage-help.php">Create New</a></li>
  </ul>
  <a id="help-section-imagemanager">Manage Media</a>
  <form id="manage-help" method="POST" action="<?php $_SERVER['PHP_SELF']?>">
    <input type="hidden" name="starsan" value="true"/>
    <?php if(isset($_GET['e'])){?>
      <input type="hidden" name="edit" value="<?php echo $_GET['e']; ?>"/>
    <?php }; ?>
    <label>Title:<br/>
      <input name="help_title" id="help-title" type="text" value="<?php if(isset($_GET['e'])&&!isset($_POST['edit'])){echo $section_to_edit['help_title'];}else if(isset($_POST['help_title'])){echo $_POST['help_title'];}?>"/>
    </label>
    <br/>
    <label> Content:</label>
    <textarea name="help_content" id="ckedit"><?php if(isset($_GET['e'])&&!isset($_POST['edit'])){echo $section_to_edit['help_content'];}else if(isset($_POST['help_content'])){echo $_POST['help_content'];}?></textarea>
    <input type="submit" name="submit" value="<?php if(isset($_GET['e'])||isset($_POST['edit'])){echo 'Save';}else{echo 'Create';}?>"/>
  </form>
</section>
<section style="display:none" id="img-manager">
  <div id="img-manager-wrap">
    <section>
      <h2>Media:</h2>
      <ul id="img-manager-links">
        <?php
          $existing_files = array_diff(scandir(__DIR__."/help_gfx"),array(".",".."));
            if(!empty($existing_files)){
              $dir = $base_url."admin/pages/help_gfx/";
              foreach($existing_files as $file){
                echo '<li><img src="'.$dir.$file.'"><span>'.$dir.$file.'</span></li>';
            }
          }else{
            echo "No Files";
          }
        ?>
      </ul>
    </section>
    <section>
      <h2>Upload</h2>
      <form id="img-manager-form" method="post" action="<?php echo $base_url?>admin/pages/upload_file.php;" enctype="multipart/form-data">
        <input name="mng-file" id="mng-file" type="file"/>
        <input type="submit" value="upload"/>
      </form>
    </section>
    <div class="clear"></div>
  </div>
  <a id="close-img-manager">Close</a>
</section>

<script type="text/javascript">
/*  CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
  CKEDITOR.config.forcePasteAsPlainText = false; // default so content won't be manipulated on load
  CKEDITOR.config.basicEntities = true;
  CKEDITOR.config.entities = true;
  CKEDITOR.config.entities_latin = false;
  CKEDITOR.config.entities_greek = false;
  CKEDITOR.config.entities_processNumerical = false;
  CKEDITOR.config.fillEmptyBlocks = function (element) {
          return true; // DON'T DO ANYTHING!!!!!
  };
    
  CKEDITOR.config.allowedContent = true; // don't filter my data
*/
CKEDITOR.stylesSet.add( 'my_styles', [
    { name: 'Left Text', element: 'p', attributes: { 'class':'text-left'} },
    { name: 'Right Text', element: 'p', attributes: { 'class':'text-right'} },
    { name: 'Clear', element:'div', styles:{ clear:'both'}},
    { name: 'Right Image', element: 'p', attributes: { 'class':'image-right'} },
    { name: 'Left Image', element: 'p', attributes: { 'class':'image-left'} },
    { name: 'Left List', element: 'ul', attributes: { 'class':'list-left'} },
    { name: 'Right List', element: 'ul', attributes: { 'class':'list-right'} }
]);
CKEDITOR.config.stylesSet = 'my_styles';
CKEDITOR.config.contentsCss = '../../css/style.css';

  CKEDITOR.replace( 'ckedit' );
jQuery(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && 
             (
                 d.type.toUpperCase() === 'TEXT' ||
                 d.type.toUpperCase() === 'PASSWORD' || 
                 d.type.toUpperCase() === 'FILE' || 
                 d.type.toUpperCase() === 'SEARCH' || 
                 d.type.toUpperCase() === 'EMAIL' || 
                 d.type.toUpperCase() === 'NUMBER' || 
                 d.type.toUpperCase() === 'DATE' )
             ) || 
             d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});

/*  jQuery("body").keyup(function(e){
    if(e.keyCode == 8){
      e.preventDefault();
      e.stopPropagation();
      var navaway = confirm("Are you sure you want to navigate away from this page? All data will be lost. We can live with it if you can.")
      if(!navaway){

      }
    };
  });*/

  jQuery("#help-section-imagemanager").click(function(){
    jQuery("#img-manager").show("fold");
  });

  jQuery("#close-img-manager").click(function(){
    jQuery("#img-manager").fadeOut();
  });

  jQuery("#img-manager-form input:submit").click(function(e){
    url = "'"+jQuery("#img-manager-form").attr("action")+"'";
    e.preventDefault();
    var ajaxData = new FormData();
    thefile = jQuery("#mng-file")[0].files[0];
    ajaxData.append('file', thefile);
    jQuery.ajax({
      cache:false,
      url: "upload_file.php",
      type: "POST",
      data: ajaxData,
      headers: {"X_FILENAME":thefile.name},
      contentType: false,
      processData: false,
      beforeSend: function(){
        jQuery("#img-manager-form input:submit").attr("disabled","disabled").val("Uploading");
      },
      success: function(data){
        if(data.error == false){
          var imgdisplay = "";
          jQuery.each(data.content,function(k,v){
            imgdisplay += "<li><img src='"+base_url+"/admin/pages/help_gfx/"+v+"'><span>"+base_url+"/admin/pages/help_gfx/"+v+"</span></li>";
          });
          jQuery("#img-manager-links").empty().append(imgdisplay);
          jQuery("#img-manager-form input:submit").removeAttr("disabled").val("Upload");
        }
      },
      error: function(data){
        alert("error")
      }
    });
  });
</script>

<?php include __DIR__."/../../views/templates/footer.php"?>
