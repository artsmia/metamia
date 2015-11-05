<div id="selfhelp-center">
<a id="darrow">&#10097;</a><br/>
  <h3>Welcome to the <em>"Self Help Center"</em></h3>
  <?php include(__DIR__."/../../ctrl/admin_ctrl.php"); $ctrl = new admin_ctrl(); $sections = $ctrl->getHelpSection("all");
    foreach($sections as $key => $content){
      $nav[] = "<li><a id='h-".$key."'>".$content['help_title']."</a></li>";
      $section[]="<section>".$content['help_content']."</section>";
    }
  ?>
  <ul id="sld-nav"><?php echo implode(" ",$nav); ?><div class="clear"></div></ul>
  <div id="help-section-content"></div>
  <?php echo implode(" ",$section);?>
</div>
         <script>
         jQuery(document).ready(function(){
           jQuery("#selfhelp-center > section").each(function(k,v){
                jQuery("#selfhelp-center > section").hide();
           });
           jQuery("#sld-nav").on("click","a",function(){
                jQuery("#selfhelp-center > section").fadeOut();
                jQuery("#sld-nav a").removeClass("active");
                jQuery(this).addClass("active");
                var current = jQuery("section").get(jQuery(this).prop("id").substr(2));
                jQuery(current).fadeIn();
           });
           var current = jQuery("section").get(0);
           jQuery(current).show();
         });

         </script>
