  jQuery('#selfhelp-center').css({'height':'0px'});
  var tick = 0;
  var settick = function(){ if(tick==0){tick=1;} else if(tick==1){tick=2;} else if(tick==2){tick=1;}}
  
  //Top Bar Help Section Click Func()
  jQuery('#darrow').click(function(e){
      e.stopPropagation();
      e.preventDefault();
      if(tick==0 || tick==2){
          jQuery('#selfhelp-center').animate({height:650},{duration:400, easing: 'easeOutBounce', complete: settick});
          jQuery('#darrow').css({'transform':'rotate(-90deg)'});
      }
      if (tick==1){
          jQuery('#selfhelp-center').animate({height:0},{duration:400, easing: 'easeOutBounce', complete: settick});
          jQuery('#darrow').css({'transform':'rotate(90deg)'});
      }
  });
  //  Hide help section if user clicks out of it
  jQuery(document).mouseup(function (e){
    if(tick==1){
      var container = jQuery('#selfhelp-center');
      if (!container.is(e.target) && container.has(e.target).length === 0){
        jQuery('#selfhelp-center').animate({height:0},{duration:400, easing: 'easeOutBounce', complete: settick});
        jQuery('#darrow').css({'transform':'rotate(90deg)'});
      }
    }
  });
  //-----------------+
  //    Help Text    |
  //-----------------+
  jQuery('.help-text a').click(function(e){
    e.preventDefault();
    jQuery(this).next().toggle('fade');
  });
  jQuery(document).mouseup(function (e){
        var container = jQuery('.help-text');
        var tohide = jQuery('.help-text p');
        if (!container.is(e.target) && container.has(e.target).length === 0){
            tohide.fadeOut();
        }
  });
