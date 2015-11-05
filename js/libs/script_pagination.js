  // Pagination handleing for large text fields
  jQuery(".paged-text > div").hide();
  jQuery(".pt-page-0").show();
  jQuery(".pt-nav a").click(function(e){
      e.preventDefault();
      var parent = jQuery(this).parents(".paged-text").prop("id");
      jQuery("#"+parent+" .pt-nav a").removeClass("active");
      jQuery(this).addClass("active");
      var chid = jQuery(this).text();
      jQuery("#"+parent+" .ptp").fadeOut();
      jQuery("#"+parent+" .pt-page-"+chid).fadeIn();
  });

  //---------------------------+
  //    Pagination Handling    |
  //---------------------------+
  var change;
  jQuery(".pagination a").click(function(e){
      e.preventDefault();
      var orig = orig_filters;
      var news = jQuery("#form-search-filters").serializeArray();
      console.log(orig);
      console.log(news);
      if(JSON.stringify(orig) == JSON.stringify(news)){change = false;}else{change = true;}  
  });
//Get current page on load
  var currentpage = jQuery("#current-page").val();

  //PREV
  jQuery("#pg-prev").click(function(e){
    if(currentpage != 0){
      if(change == false){
      currentpage--;
      }else{
        currentpage = 0;
      }
      jQuery("#current-page").val(currentpage);
    }
    jQuery("#main-search-form").submit();
  });

  //First
  jQuery("#pg-first").click(function(e){
    currentpage=0;
    jQuery("#current-page").val(currentpage);
    jQuery("#main-search-form").submit();
  });

  //Numbered
  jQuery(".pg-link-inr").click(function(e){
    if(change == false){
    currentpage = this.id.substr(3);
    }else{
    currentpage=0;
    }
    jQuery("#current-page").val(currentpage);
    jQuery("#main-search-form").submit();
  });

  //NEXT
  jQuery("#next").click(function(e){
      if(change==false){
        currentpage++;
      }else{
        currentpage = 0;
      }
      jQuery("#current-page").val(currentpage);
    jQuery("#main-search-form").submit();
  });

  //Last
  jQuery(".last").click(function(e){
    if(change == false){
    currentpage = this.id.substr(3);
    }else{
    currentpage = 0;
    }
    jQuery("#current-page").val(currentpage);
    jQuery("#main-search-form").submit();
  });

  //Main submit? reset to 0
  jQuery("#main-submit").click(function(e){
    e.preventDefault();
    jQuery("#current-page").val(0);
    jQuery("#main-search-form").submit();
  });
