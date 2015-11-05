jQuery("#loading-box").css("display","block");
jQuery("#loading-box").fadeOut();
jQuery('header').css("top",-100);
jQuery('header').animate({"top":0},500);
jQuery(".more-data").css("display","none");
jQuery("#welcome").fadeIn("slow");
  jQuery("#welcome h2 span").hide();
  jQuery(document).ready(function(){
  var timer=0;
  jQuery("#welcome h2 span").each(function(k,v){ 
    setTimeout(function(){
      jQuery(v).fadeIn("slow");
    },150*timer);
    timer++;
  });
});      
//-------------+
//  Sidebar    |
//-------------+
var state = "open";
//  Show-Hide Sidebar
jQuery('#sh-sidebar').click(function(e){
      e.preventDefault();
      if(state == "open"){
          jQuery('.sidebar').animate({right:"-20%"},500);
          jQuery('#sh-sidebar').html("&#10096; Filters");
          jQuery(".more-data").animate({"width":"94%"});
          jQuery("#results-dashboard").animate({"width":"92%","padding-right":"5%"},500);
          jQuery('#results').animate({"width":"96%"},500);
          jQuery('footer').animate({"width":"100%"});
          state="closed";          
          setTimeout(function(){  
            $container.masonry();
         },300);
      }
      else if(state =="closed"){
          jQuery('.sidebar').animate({right:0},500);
          jQuery(".more-data").animate({"width":"74%"});
          jQuery('#sh-sidebar').animate({"right":0},500).html("Hide &#10097;");
          jQuery('#results').animate({"width":"77%"},500);
          jQuery("#results-dashboard").animate({"width":"76%"},500);
          jQuery('footer').animate({"width":"80%"},600);
          state="open";
          setTimeout(function(){
            $container.masonry();
         },300);
      }
      
  });

  // Order Select
  jQuery("#result-order select").on("change",function(){
      jQuery("#main-search-form").submit();
  });

  // Hide images that fail to laod and update layout
  var imgs = document.getElementsByTagName('img')
  for(var i=0,j=imgs.length;i<j;i++){
      imgs[i].onload = function(e){
        $container.masonry('layout');
      }
      imgs[i].onerror = function(e){
        this.parentNode.remove(this);
        $container.masonry('layout');
      }
  }

  //------------------+
  //    Mason Grid    |
  //------------------+
  var $container = $('#results');
  $container.masonry({   
      "itemSelector": ".result", 
      "columnWidth": ".grid-sizer",
      "percentPosition": true
  });
  // update layout function
  showResults = function(){
      $container.masonry();
  }
  //  List view click
  jQuery("#list-view").click(function(e){
    e.preventDefault();
    if(jQuery(this).hasClass("active") == false){
      viewList();
    }
  })  
  //  List view with thumbnails click
  jQuery("#mid-view").click(function(e){
    if(jQuery(this).hasClass("active") == false){
      viewListThumb();
    }
  });
  //  Thumbnail view click
  jQuery("#thum-view").click(function(e){
    e.preventDefault();
    if(jQuery(this).hasClass("active") == false){
      viewThumb();
    }
  });
  //  List view settings
  viewList = function(){
    jQuery("#view-selector a").removeClass("active");
    jQuery("#list-view").addClass("active");
    jQuery(".result").css("width","98%");
    jQuery(".thumb-actions").css({"position":"absolute","right":"5px", "bottom":"7px"});
    jQuery(".thumbnail, .result p, .result video, .result audio").css("display","none");
    jQuery(".result > a, .result > h2, .result > h3").css({"display":"inline-block","margin-right":"10px","vertical-align":"middle"});
    jQuery(".result > a").css("float","right");
    jQuery("#view-type").val("list");
    showResults();
  };
  //  List view with thumbnail settings
  viewListThumb = function(){
    jQuery("#view-selector a").removeClass("active");
    jQuery("#mid-view").addClass("active");
    jQuery(".result").css("width","98%");
    jQuery(".thumb-actions").css({"position":"absolute","right":"5px", "bottom":"7px"});
    jQuery(".thumbnail").css({"display":"block","max-width":"20%","float":"left","margin-right":"10px"});
    jQuery(".result > p").css({"display":"block"});
    jQuery(".result video, .result audio").css({"display":"block","float":"left","margin-right":"10px"});
    jQuery("#view-type").val("mid");
    showResults();
  }  
  //  Thumbnail view settings
  viewThumb = function(){
    jQuery("#view-selector a").removeClass("active");
    jQuery("#thum-view").addClass("active");
    jQuery(".result").css("width","30%");
    jQuery(".thumb-actions").css({"position":"relative", "right":"0","bottom":"-5px"});
    jQuery(".thumbnail, .result p").css({"display":"block","float":"none","max-width":"100%","margin":"0"});
    jQuery(".result > a, .result > h2, .result > h3").css({"display":"block","margin-right":"0"});
    jQuery(".result > a").css("float","none");
    jQuery(".result video, .result audio").css({"display":"block","max-width":"100%"});
    jQuery("#view-type").val("thum");
    showResults();
  };

  //Show images when they are loaded
  //jQuery('.result img').on('load', function(){
      jQuery(".thumb-loader").css("display","none");
      jQuery('.result img').fadeIn("slow").css("display","block");


  //---------------------+
  //    Fixed dashbar    |
  //---------------------+
  if(pagename == "home"){
    var isfixed = false;
    jQuery(window).scroll(function(){
      var scrolltop = jQuery(window).scrollTop();
      if(scrolltop > 100 && isfixed == false){
        isfixed = true;     
        jQuery("#results-dashboard").addClass("rs-dsh-fxd");
        jQuery("#main-content").css("padding-top","115px");
      }else if(scrolltop < 100 && isfixed == true){
        isfixed = false;
        jQuery("#results-dashboard").removeClass("rs-dsh-fxd");
        jQuery("#main-content").css("padding-top",0);
      }
    });
  }
jQuery(window).load(function(){
var $container = $('#results');
  $container.masonry('layout');
});
