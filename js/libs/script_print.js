var printPage = function(id){
  switch(id){
    case "txt":
      jQuery(".img-wrap").addClass("print-hide");
      window.print();
      jQuery(".img-wrap").removeClass("print-hide");
    break;
    case "img":
      jQuery(".full-head, .md-wrap h2, .md-wrap h3").addClass("print-hide");
      window.print();
      jQuery(".full-head, .md-wrap h2, .md-wrap h3").removeClass("print-hide");
    break;
      case "imgtxt":
      window.print();
      break;
    default:
      //Do nothing
    break;
    }
  }
  jQuery("body").on("click",".print-img, .print-txt, .print-imgtxt", function(){
    var identifier = jQuery(this).prop("class").slice(6);
    printPage(identifier);
  });
  
  // Printing Actions
  var pshow = false;
  jQuery(".rslt-print").click(function(e){
    if(pshow == false){
      jQuery(this+" .print-options").stop().show("clip");
      jQuery(this).addClass("active");
      pshow = true;
    }else if(pshow == true){
      jQuery(this).removeClass("active");
      jQuery(this+" .print-options").stop().hide("clip");
      pshow = false;
    }
  });
  jQuery(document).mouseup(function(e){
    var container = jQuery(".rslt-print");
    var action_container = jQuery(".print-options li a");
    if ((!container.is(e.target) && container.has(e.target).length === 0 && !action_container.is(e.target)) && pshow == true){
        jQuery(".print-options").hide("clip");
        pshow = false;
        jQuery(".rslt-print").removeClass("active");
    }
  });
