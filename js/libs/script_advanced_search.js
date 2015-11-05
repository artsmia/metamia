  //Advanced Search Submit
  jQuery("#advanced-search input[type='submit']").click(function(e){
    e.preventDefault();
    jQuery("#main-submit").trigger("click");
  });

  //----------------------------+
  //    Advanced Search Tabs    |
  //----------------------------+
  jQuery("#advanced-search > fieldset").hide();
  jQuery("#adv-resourcespace").addClass("active");
  jQuery(".fs-resourcespace").show();
  jQuery("#advs-main-nav li").click(function(){
    var identifier = jQuery(this).prop("id").substr(4);
    jQuery("#advs-main-nav li").removeClass("active");
    jQuery(this).addClass("active"); 
    jQuery("#advanced-search > fieldset").hide(); 
    jQuery(".fs-"+identifier).show("blind");
  })

  jQuery("#advs-main-nav li").first().trigger("click");

  //input add visual search
  jQuery("#advanced-search input").focus(function(e){
    var bgColor = jQuery(this).parents(".advs-fs").css('backgroundColor');
    var title = jQuery(this).attr("id");
    var optype = jQuery(this).prev().text();
    if(jQuery(this).val()==""){        
      jQuery("#advs-srch-view").append("<span style='color:"+bgColor+"' class='advsitm-" + title.replace(/\s+/g, '') + "'> <span class='op-type'>"+optype+"</span> <b>"+title+"</b> = <span class='advs-vw-txt'></span></span>");
    };
    jQuery(this).on('keyup', function(e){
        formattedtitle = title.replace(/\s+/g, '');
        jQuery('.advsitm-' + formattedtitle + ' .advs-vw-txt').html(jQuery(this).val());
    })
  });

  //set search
  jQuery('#advanced-search input').focusout(function(e){
    if(jQuery(this).val()==''){
        var title = jQuery(this).attr('id');
        jQuery('.advsitm-'+title.replace(/\s+/g, '')).remove();
    }
  });
