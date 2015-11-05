document.createElement("article");
document.createElement("footer");
document.createElement("header");
document.createElement("hgroup");
document.createElement("nav");
document.createElement("aside");

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

  //---------------+ 
  //    FILTERS    |
  //---------------+     
  //  Hide default checkboxes for type filters
  jQuery("#type-chkbx input").css("display","none");
  //  On load set each checkbox state
  jQuery(".type").each(function(k,v){
     if(jQuery(this).is(":checked")){
        jQuery(this).parent("label").css("opacity","1");
     }else{
       jQuery(this).parent("label").css("opacity","0.3");
     }
  });
  //  On click update each checkbox state
  jQuery(".faux-cbx").click(function(){
    if(jQuery(this.children[0]).is(":checked")){
        jQuery(this).css("opacity","1");
    }else{
        jQuery(this).css("opacity","0.3")
    }
  });
  //  Query assistant - append input to sidebar on change
  jQuery('#qa select').change(function(){
      var value=jQuery(this).val();
      var adc ="";
      var flttype = "text";
      if(jQuery.inArray(value,fltranges)>=0){
          adc = "<a class='rng' alt='" + value + "'>=</a>";
          flttype = "number";
      }else if(jQuery.inArray(value,fltdates)>=0){
          adc = "<a class='rng' alt='" + value + "'>=</a>";
          flttype = "date";
          dp =  "";   
      }
      jQuery("#inputs").append("<label for='"+value+"'>"+value+"<br/>"+
      "<input id='value' type='"+flttype+"' class='filter' name='qaf["+value+"]' />"+
      "<a class='rm-val' href='#'> [x] </a><br/></label>");
  });
  //  Determine Filter Type operator
  function determineType(operator){
    switch(operator){
       case "!":return "filterno";break;
       case "&":return "filter"; break;
       case "or":return "filteror"; break;
       case "=":return 0; break;
       case ">":return 1; break;
       case "<":return 2; break;
    }
  }
  //  switch "and", "or" or "not"
  function filteroperators(thisObj,optype){
      var opval;
      var filterinput = thisObj.parent().children(".filter");
      var fltval = thisObj.parent().children(".ornot").attr("alt");
      if(thisObj.parent().find(".rng").length != 0){
         var range = true;
      }else{
         var range = false;
      }
      //determine button and set operator values
      if(optype == "flt"){
          var chfilt = true;
          var operators = ["!","&","or"];
      }else{
          var chfilt = false;
          var operators = ["=",">","<"];
      }
      var oplngth = operators.length;

      //determine where where calling from
      if(thisObj.closest("#inputs").length>0){ var cfrom = "sidebar";}
      else if(thisObj.closest(".advs-fs").length>0){var cfrom = "advanced";}

      //get the field value and set the next operator
      var crntop = ((jQuery.inArray(thisObj.text(),operators))+1);
      //if we hit the end start over
      if(crntop == oplngth){
          crntop = 0;
      }
      //if called from operator button
      if(chfilt){
          flttype = determineType(operators[crntop]);
          if(!range){
            var nameval = flttype+"["+fltval+"][]";
          }else{
            opval = determineType(thisObj.next().text());
            var nameval = flttype+"["+fltval+":op="+opval+"][]";
          }
      //else if called from type button
      }else{
          flttype = determineType(thisObj.prev().text());
          opval = determineType(operators[crntop]);
          var nameval = flttype+"["+fltval+":op="+opval+"][]";
      }
      filterinput.attr("name",nameval);
      //set text of button to new operator val
      thisObj.text(operators[crntop]);
      // if called rom advaned update the search view
      if(cfrom == "advanced"){
          var rpval = thisObj.parent().children(".filter").attr("id").replace(/\s+/g, '');
          jQuery(".advsitm-"+rpval+" .op-type").text(operators[crntop])
      }
  }
  jQuery("#inputs, .advs-fs").on("click",".ornot", function(e){
      filteroperators(jQuery(this),"flt");
  });
  jQuery("#inputs, .advs-fs").on("click",".rng",function(e){
      filteroperators(jQuery(this),"rng");
  });
  //Remove Filter
  jQuery('#inputs').on("click",".rm-val", function(e){
      e.preventDefault();
      jQuery(this).parent('label').remove();
  });
  //Filter result click
  jQuery(".rslt-key").click(function(e){
    var label = jQuery(this).text();
    var labelval = jQuery(this).attr("alt");
    var labelname = labelval;
    var theval = jQuery(this).next().text();
    var lblid = label.replace(/ /g,'');
    var adc ="";
    var dp ="";
    var flttype = "text";
    if(jQuery.inArray(labelval,fltranges)>=0){
        adc = "<a class='rng'>=</a>";
        flttype = "number";
        labelname += ":op=0";
    }else if(jQuery.inArray(labelval,fltdates)>=0){
       adc = "<a class='rng'>=</a>";
       flttype = "date";
       labelname += ":op=0";
       dp = "<script>jQuery('#"+lblid+"').datepicker({dateFormat:'yy-mm-dd'});</script>";
    }
    jQuery("#inputs").append("<label for='"+label+"'>"+label+"<br/>"+
      "<a class='ornot' alt='"+labelval+"'>&</a>"+adc+
      "<input type='"+flttype+"' id='"+lblid+"' class='filter' value='"+theval+"' name='filter["+labelname+"][]'/><a class='rm-val' href='#'> [x] </a>"+dp+"</label>");
  });
  //Submit Filters
  jQuery("#refine-search").click(function(e){ 
      e.preventDefault();
      jQuery("#current-page").val(0);
      jQuery("#main-search-form").submit();
  });
  // Clear All Filters
  jQuery("#flt-clear").click(function(e){
     e.preventDefault();
     jQuery("#inputs").empty();
     jQuery("#search-filters input").removeAttr("checked");
     jQuery(".faux-cbx").css("opacity",0.3);
  });


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

  //Reset Form Click
  jQuery("#reset-form").click(function(){
    jQuery("#flt-clear,#clear-search").trigger("click");   
    updateSelectList("_all");
  });
  //--------------------------+
  //    Main Search Submit    |
  //--------------------------+
  jQuery("#main-search-form").on("submit",function(e){
     jQuery("#loading-box").fadeIn("fast");
      //Get Order Val
      jQuery.each(jQuery("#result-order select"),function(k,v){
          jQuery("#main-search-form").append("<input type='hidden' name='"+v.name+"' value='"+v.value+"'/>");
      });
      //Get Checkbox vals
      jQuery.each(jQuery("#search-filters input:checkbox"),function(sfk,sfv){
          if(jQuery(this).is(":checked")){
              jQuery("#main-search-form").append("<input type='hidden' name='"+sfv.name+"' value='"+sfv.value+"'>");
          }
      });
      //Get Filters
      jQuery(".filter").each(function(k,v){
          if(v.value == ""){
             jQuery(this).parent("label").remove();
          }else{
             var value = v.value.trim();
             jQuery("#main-search-form").append("<input type='hidden' name='"+v.name+"' value='"+value+"'/>");
          }
      });
  });
//----------------------+
//    Search Bubbles    |
//----------------------+

  var totswidth;
  var btnwidth = 75;
  var stringsearch="";

    //Get dimensions on ready
      totswidth = jQuery("#faux-input").width();  
      var searchterms = "";
    
      //if the input is not empty on load
      if(jQuery("#main-search-input").val() != ""){
        //Update selector value
        searchterms = jQuery("#main-search-input").val();
        //Give a short timeout pad and trigger focusout
        setTimeout(function(){jQuery("#main-search-selector").val(searchterms).trigger("focusout")},100);
      }else{
        //Set placeholder and trigger focus
        jQuery("#main-search-selector").attr("placeholder","Enter Search Term(s)...");
        setTimeout(function(){jQuery("#main-search-selector").trigger("focus")},100);
      }
      //Function to re-index bubble id's if other are deleted
      function updateIds(){
        var count = 0;
        var ogclass = "";
        jQuery("#search-bubbles li").each(function(k,v){
          var origclass = jQuery(this).attr("class").split(' ');
          if(origclass.length > 1){
             origclass.splice(0,1);
             ogclass = origclass.join(' ');
          }
          var newclass = 'bubble-'+count+' '+ogclass;
          jQuery(this).removeClass();
          jQuery(this).addClass(newclass);
          count++;
        });
      };
      //Function to convert search back to string
      function updateSearch(){
        if(searchterms != ""){
          var search = "";
          if(stringsearch == false){
            search=searchterms.join(" ");
          }else{
            search = searchterms;
          }
          return search;
        }
      };
      //check if where exact string matching
      function checkStringSearch(){
        if(jQuery("#string-search").is(":checked")){
          stringsearch = true;
          jQuery("#ex-m").css("opacity","1");
          jQuery("#ex-m span").css("background","#00CC00");
        }else{
          stringsearch = false;
          jQuery("#string-search").parent("label").css("opacity","0.7");
          jQuery("#ex-m span").css("background","#000");
        }
      }
      checkStringSearch();
   //Resize update dimensions
   jQuery(window).resize(function(){
     totswidth = jQuery("#faux-input").width();
     jQuery("#main-search-selector").trigger("focusin").trigger("focusout");
   });
    //-----------------------------+
    //    Searchbar - Focus Out    |
    //-----------------------------+
    jQuery("#main-search-selector").focusout(function(){
      checkStringSearch();
      searchterms = jQuery("#main-search-selector").val();
      if (stringsearch == false){
        if(searchterms.indexOf('\"')!=-1){
          var quotecount = searchterms.match(/\"/g).length;
          if(quotecount >= 2){
             var phrasecount = quotecount * 0.5;
             var lastpos = 0;
             for(i=0; i<phrasecount; i++){
               pos1 = searchterms.indexOf('\"',lastpos);
               pos2 = searchterms.indexOf('\"',pos1+1)+1;
               phrase = searchterms.substr(pos1,(pos2-pos1));
               newphrase = phrase.split(" ").join("&nbsp;");
               searchterms = searchterms.replace(phrase,newphrase);
               lastpos = pos1+newphrase.length;
             }
          }
        }
        //split search string to array by spaces
        searchterms = searchterms.split(/ +/);
        jQuery.each(searchterms,function(k,v){
          //If the value is not empty append a bubble
          var styleclass = "";
          if(v != ""){
            if(v.indexOf("!")== 0 || (v.indexOf("!")==1 && v.indexOf('\"')==0)){
              var styleclass = "bub-not"; 
            }
            if((v.indexOf("?") != -1 || v.indexOf("*")!=-1) && v.indexOf('\"') != 0){
              var styleclass = "bub-wild";
            }
           v = v.split("&nbsp;").join(" ");
            searchterms[k]=v;
            jQuery("#search-bubbles").append("<li class='bubble-"+k+" "+styleclass+"'>"+v+"<a class='remove' title='remove'>x</a></li>");
          }
        });
      }else if(stringsearch==true){
          if(searchterms != ""){
          jQuery("#search-bubbles").append("<li class='bubble-1'>"+searchterms+"<a class='remove' title='remove'>x</a></li>");
          }
      }
      //Get and set width dimensions
      bubwidth = jQuery("#search-bubbles").width();
      var searchwidth = Math.floor(totswidth-90-bubwidth);
      jQuery("#main-search-selector").css("width",searchwidth).val("");

      //Convert search back to string and set
      jQuery("#main-search-input").val(updateSearch());
      if(jQuery("#main-search-input").val() != ""){
          jQuery("#main-search-selector").attr("placeholder","");  
      }else{
        jQuery("#main-search-selector").attr("placeholder","Enter Search Term(s)...");  
      }

    //----------------------------+
    //    Remove word function    |
    //----------------------------+
    jQuery("li").on('click', 'a.remove', function() {
      if(stringsearch == false){
        var liid = jQuery(this).parent("li").prop("class").split(' ')[0].substr(7);
        //remove from array, view, and reindex ids
        searchterms.splice(liid,1);
      }else{
        searchterms = "";
        jQuery("#main-search-selector").trigger("focusout");
      }
      jQuery(this).parent("li").remove();
      updateIds();
 
      //update the search value
      jQuery("#main-search-input").val(updateSearch());

      //reset width dimensions
      bubwidth = jQuery("#search-bubbles").width();
      var searchwidth = Math.floor(totswidth-90-bubwidth);
      jQuery("#main-search-selector").css("width",searchwidth);
    });
  });//Close focusout

  //----------------------------+
  //    Searchbar - Focus In    |
  //----------------------------+
  jQuery("#main-search-selector").focusin(function(){
    //Set width dimensions
    var searchwidth = Math.floor(totswidth-90);
    jQuery("#main-search-selector").css("width",searchwidth);
    
    //clear the bubbles and set value back to text
    jQuery("#search-bubbles").empty();
    jQuery("#main-search-selector").val(updateSearch());
  });
  //-------------------------
  //    Keypress function
  //-------------------------
  // makes sure that value is updated just in case the user is
  // a really fast typer and smacks that enter key before other
  // funcs complete.
  jQuery("#main-search-selector").on("keyup",function(){
      jQuery("#main-search-input").val(jQuery(this).val());
  })
  jQuery(document).keypress(function(e){
    if(e.keyCode == 13){
        updateSearch();
        jQuery("#current-page").val(0);
        jQuery("#main-search-form").submit();
    }
  });
  //clear-search
  jQuery("#clear-search").click(function(e){
      e.preventDefault();
      jQuery("#search-bubbles li").remove();
      searchterms = "";
      jQuery("#main-search-selector").trigger("focusout");

  });
  //---------------------------+
  //    String Search Click    |
  //---------------------------+
  jQuery("#ex-m").click(function(){
    jQuery("#main-search-selector").trigger("focusin").trigger("focusout"); 
  });

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

//  Initially Set Saved Search Status to FALSE
var ssstatus=false;
var saves = [];
var active_cart_view = "Current Cart";
//---------------+
//  Functions    |
//---------------+

  //Set Cart Actions
  function setCartActions(current_cart){
    // Show / hide Share button
    if(current_cart == 0){
      jQuery("#genurl-saved-search").hide();
      jQuery("#save-cart").show();
    }else{
      jQuery("#genurl-saved-search").show();
      jQuery("#save-cart").hide();
    }
    // Show / Hide Delete button
    if(jQuery("#saved-search-results li").length == 0){
      jQuery("#clear-saved-search").hide();
    }else{
      jQuery("#clear-saved-search").show();
    }
  }

  //Main Ajax Function
  function heyAjax(data,scc,err){
    jQuery.ajax({
        url: "../ctrl/redis-ctrl.php",
        type: "POST",
        dataType:"json",
        data:data,
        success: scc,
        error: err,
    });
  }

  // Recall Save from URL
  function recallsaves(){
    jQuery.ajax({
      url: "../ctrl/get_saved_search.php",
      cache: false,
      type: "POST",
      data: saves,
      processData: false,  
      success: function(data){
        var pdata = JSON.parse(data);
        pdata=pdata.hits.hits;
        var newray=[];
        jQuery.each(pdata,function(k,v){ 
           newray.push(v._source);
        });
        finalray=[];
        jQuery.each(newray,function(k,v){
            var result = "<li class='result'><h2>"+v.title+"</h2><a href='#' onclick='rmsearch()'>Remove</a></li>"
            finalray.push(result);
        })
        jQuery("#main-content").append(finalray);
      },
      statusCode: {
        404: function() {
          alert( "ERROR: This search was not found in the system." );
        },
        500:function(){
          alert("ERROR: Internal system error.");
        }
      },
      error: function(){
          alert("ERROR: undefined - Could not recall search.");
      }
    });
  }

  // Highlight and select URL
  function SelectText(element) {
      var text = document.getElementById(element);
      if (jQuery.browser.msie) {
          var range = document.body.createTextRange();
          range.moveToElementText(text);
          range.select();
      } else if (jQuery.browser.mozilla || jQuery.browser.opera) {
          var selection = window.getSelection();
          var range = document.createRange();
          range.selectNodeContents(text);
          selection.removeAllRanges();
          selection.addRange(range);
      } else if (jQuery.browser.webkit) {
          var selection = window.getSelection();
          selection.setBaseAndExtent(text, 0, text, 1);
      }
  } 
//-----------------+
//    Callbacks    |
//-----------------+

  // Callback - Add Query - Success
  addqscc = function (data){
    if(data.error=="true"){
      jQuery(".confirm-save").append(data.msg);
    }else{
      jQuery(".confirm-save").fadeOut("slow",function(){
        jQuery(".fixed-bg").fadeOut("slow");
        jQuery("#ss-subnav").after("<span class='scc-msg'>Successfully Saved</span>");
        setTimeout(function(){
          jQuery("#saved-search-form .scc-msg").hide("fold",function(){
            jQuery(this).remove();
          });
        },3000);
        jQuery(".confirm-save").remove();
        jQuery(".ss-saved").trigger("click");
      });
      current_cart = 0;
      getSearch();
    }
  };

  addqerr = function (data){
    alert(data);
  }

  // Callback - Add Search Item - Success
  addscc=function(data){
    if(data.error != true){
      saves = [];
      var display = "";
      if("cart_title" in data){
        display += "<h3>"+data.cart_title+"</h3>";
      }
      jQuery.each(data.results,function(k,v){
            if(data.results == "no results"){
              display += "<em>No Assets</em>";
            }else{
              display += "<li id='"+k+"'>"+v+"<a class='ssi-dl'>[x]</a></li>";
              saves.push(k);
            }
      })
      jQuery("#saved-search-results").empty().append(display);
      if(ssstatus == false){
        jQuery("#sb-nav-search").css("display","block").addClass("active").trigger("click");
        ssstatus = true;        
      }
    }else{
      jQuery("#saved-search-results").prepend(data.results);
    }
    setCartActions(current_cart);
  };

  //  Callback - Add Search ERROR
  adderr=function(data){
     jQuery("#saved-search-results").append(data);
  }
  
  //  Callback - Get Search ERROR
  geterr=function(data){
    jQuery("#sb-nav-filter").addClass("active");
    ssstatus = false;
  }

  // Callback Generate Url - Success
  genscc=function(data){
        var pup = jQuery("<div class='fixed-bg'><div style='display:none;' class='popup'>"+
          "Your search can be accessed here:<br/>"+
          "<a id='svs-link' href='"+ data.results +"'>"+data.results+"</a>"+
          "<ul id='pop-actions'>"+
          "<li id='svs-copy'>Select</li>"+
          "<li id='svs-mail'>Email</li></ul>"+
          "<button>Sweet Thanks.</button></div></div>"
    ).hide().fadeIn("slow");
    jQuery("body").append(pup);
    jQuery(".popup").show("clip");
    jQuery(".popup button").click(function(e){
        jQuery(this).parent(".popup").fadeOut("300",function(){
            jQuery(".popup").remove();
        });
        jQuery(".fixed-bg").fadeOut("slow",function(){
            jQuery(".fixed-bg").remove();
        });
    });
  }

  // Callback - Save Search - Success
  var ssscc=function(data){
    if(data.error == false){
      var display = "";
      if("cart_title" in data){ display += "<h3>"+data.cart_title+"</h3>" }
      jQuery.each(data.results,function(k,v){
        if(jQuery.isNumeric(k)){
          var thisclass = "itm-cart"; 
          var ico = "C";
          var type = "carts";
          var dsptitle = v;
          var id = k;
        }else{ 
          var thisclass = "itm-query"; 
          var dlt = "dlt-query";
          var ico = "Q";
          var type = "queries"
          var dsptitle = k;
          var id = v;
        }
        var canDel = "<a class='dlt-crt'> [x]</a>"+
          "<a class='shr-crt' id='shr-crt"+id+"'>Share</a>";
        if(thisclass == "itm-cart" && k == 0){
          canDel = "";
        }
        if(v=="no results"){
          display+="<em>No "+type+"</em>";
        }else{
            display += "<li class='"+thisclass+"' id='"+thisclass+k+"'>"+
            "<a id='"+id+"' class='itm-vw'>"+dsptitle+"</a>"+
            canDel+"<span class='itm-ico'>"+ico+"</span></li>";
        }
      });
        display = jQuery(display).hide().fadeIn();
        jQuery("#saved-items").empty().append(display)
    }else{
      alert("error");
    }
  }
  
  //  Callback - Save Search - Error
  var sserr=function(data){  
    alert("Failed to Save ");
  }
  //  Callback - Delete Cart - Success
  dcscc = function(data){
    if(data.error != true){
      if("type" in data){
        var identifier = data.type+data.results;
        jQuery("#itm-"+identifier).hide("explode",function(){
          jQuery(this).remove();
        });
      }else{
        var identifier = data.results;
      }
      // if we just deleted the current cart set us back to Temp
      if(data.results == current_cart){
        current_cart = 0;
        setCartActions(current_cart);
        getSearch();
      }
    }
  };

  //  Callback - Delete Search Item - Success
  delscc = function(data){
    if(data.error == false){
      jQuery("#cart #"+ data.results).hide("explode",function(){
        jQuery(this).remove();
        setCartActions(current_cart);
        var pos = jQuery.inArray(data.results,saves);
        saves.splice(pos,1);
      });
    }
  };

  //  Retrieve Search
  function getSearch(){
     data = {"type":"getSearch","UserId":uid,"search_type":"cart","search_id":current_cart};
     heyAjax(data,addscc,geterr);
  }

//-----------------------+
//    Click Functions    |
//-----------------------+

  /*====    Share    ====*/
  /*=====================*/

  /*  Share - Popup - Show  */
  jQuery("body").on("click","#svs-mail",function(e){
     jQuery(".popup").append("<form>"+
     "<label for='svs-to'>To: <input id='svs-to' type='email' name='svsto'/></label>"+
     "<input id='svs-send' type='submit' value='Send'/>"+
     "<a id='svs-send-cancel'>cancel</a>"+
     "</form>")
     jQuery("#svs-to").trigger("focus");
  });
  jQuery("body").on("click","#svs-copy",function(e){
    e.preventDefault();
    SelectText("svs-link");
  });
  //  Share Email - Popup - Cancel
  jQuery("body").on("click","#svs-send-cancel",function(e){
    e.preventDefault();
    jQuery(".popup form").fadeOut("slow",function(){jQuery(this).remove();});
  });

  // Send Email - Execute
  jQuery("body").on("click","#svs-send",function(e){
    e.preventDefault();
    jQuery(this).attr("disabled","disabled").css("opacity",0.3);
    var link = jQuery("#svs-link").text();
    var to = jQuery("#svs-to").val();
    if(to ==""){
        jQuery(this).removeAttr("disabled").css("opacity",1);
        alert("Please enter an email address.");
    }else{
      jQuery.ajax({
          url: "http://localhost/ctrl/saved_search_send.php",
          type: "POST",
          data: {"svsto":to,"svslink":link,"user":user_name,"uid":uid},
          success: function(data){
             data=JSON.parse(data);
             if(data.error==false){
               jQuery(".popup form").append("<br/><span class='scc-msg'>"+data.msg+"</span>");
               jQuery("#svs-send-cancel").text("close");
             }else{
               jQuery(".popup form").append("<br/><span class='error'>"+data.msg+"</span>")
               setTimeout(function(){
                   jQuery(".popup form .error").fadeOut("slow",function(){this.remove();})
               },5000);
             }
             jQuery("#svs-send").removeAttr("disabled").css("opacity",1);
          },
          error: function(){
              alert("error");
          }
      });
    }
  });

  //SHARE CART
  jQuery("#saved-carts").on("click",".shr-crt",function(e){
     e.preventDefault();
     var identifier = jQuery(this).prop("id").substr(7);
     var ctype = (jQuery.isNumeric(identifier)) ? 'cart' : "query";
     genUrl(identifier,ctype);
  });


  //=====     Save     =====
  //========================
  
  //  Save Query - Popup - Show
  jQuery(".rslt-sv-query").click(function(){
    var popup = jQuery("<div class='fixed-bg'>"+
        "<div class='confirm-save'><h3>Please name your search.</h3>"+
        "<input type='text' id='svq-title' placeholder='Enter name' />"+
        "<button id='svq-confirm'>Save</button>"+
        "<button id='svq-cancel'>Cancel</button></div></div>").hide().fadeIn("slow");
    jQuery("body").append(popup);
  });

  //  Save Query - Execute
  jQuery("body").on("click","#svq-confirm",function(){
    var queryTitle = jQuery("#svq-title").val();
    if(queryTitle == ""){
      jQuery(".err").remove();
      jQuery(".confirm-save h3").after("<span class='err'>No. Really. It needs a name.</span>");
      setTimeout(function(){
        jQuery(".err").fadeOut("slow",function(){
          jQuery(this).remove();
        });
      },4000);
    }else{
      data = {"type":"addQuery","UserId":uid,"sUrl":window.location.href,"title":queryTitle};
       heyAjax(data,addqscc,addqerr);
    }  
  });

  // Save Query - Cancel
  jQuery("body").on("click","#svq-cancel",function(){  
    jQuery(".confirm-save").hide("fold",function(){
      jQuery(this).remove();
    });
    jQuery(".fixed-bg").fadeOut("slow",function(){
      jQuery(".fixed-bg").remove();
    });
  });

  //Save Result Set - Add to Cart
  jQuery(".rslt-sv-page").click(function(){
    jQuery(".save-search").each(function(k,v){
      jQuery(this).trigger("click");
    });
  });
  
  //  Save Cart - popup
  jQuery("#save-cart").click(function(e){
    e.preventDefault();
    if(jQuery("#saved-search-results li").length != 0){
    var popup = jQuery("<div class='fixed-bg'><div class='confirm-save'>"+
      "<h3>Lets give this search a name.</h3>"+
      "<label><input type='text' class='cart-name' placeholder='Name your search.'></label><br/>"+
      "<button id='svcart'>Save</button>"+
      "<button id='svq-cancel'>Cancel</button></div></div>").hide().fadeIn("slow");
    jQuery("body").append(popup);
    }else{
      alert("There are no Assets in this Set to save.");
    }
  });

  //  Save Cart - Execute
  jQuery("body").on("click","#svcart",function(e){
    var cart_title = jQuery(".cart-name").val();
    if(cart_title == ""){
      jQuery(".err").remove();   
      jQuery(".cart-name").css("border","1px solid #F00");
      jQuery(".confirm-save h3").after("<span class='err'>No really it needs a name</span>");
      setTimeout(function(){
        jQuery(".err").fadeOut("slow",function(){jQuery(this).remove()});
      },5000);
    }else{
      var data = {"type":"saveCart","UserId":uid,"cart_title":cart_title,"cart_id":current_cart}
      heyAjax(data,addqscc,addqerr);
    }
  });

  function genUrl(url_val,urltype){
     data={"type":"genUrl","UserId":uid, "url_val":url_val,"url_type":urltype};
     heyAjax(data,genscc,adderr);
  }
  //Generate Url - call
  jQuery("#genurl-saved-search").click(function(e){
    e.preventDefault();
    genUrl(current_cart, "cart");
  });

  // View as Search
  jQuery("#view-cart").click(function(e){
    e.preventDefault();
    url = base_url+"views/home.php?svsrch="+md5uid+":"+current_cart;
    window.location.replace(url);   
  });
  //Add Search Item click function
  jQuery(".save-search, .fv-save-search").click(function(e){
      e.preventDefault();
      var identifier = jQuery(this).prop("id");
      if(active_cart_view != "Current cart"){
        jQuery(".ss-current").trigger("click");
      }
      if(jQuery.inArray(identifier,saves) == -1){
        saves.push(identifier);
        var item_title = jQuery(this).prop("title");
        var data = {"type":"addItem","UserId":uid,"cart_id":current_cart,"item_id":identifier,"item_title":item_title};
        heyAjax(data,addscc,adderr);
      }else{
        jQuery("#saved-search-results "+ identifier).effect("hightlight");
      }
  });

  //====    View    ====
  //====================
  
    //--------------------+
    //  View all Saves    |
    jQuery(".ss-saved").click(function(e){
      e.preventDefault();
      getAllSaves();
    });
  
    function getAllSaves(){
      jQuery("#cart").hide();
      jQuery("#saved-carts").show("slide");
      // The first li in the saved cart nav is set to retrieve all searches so lets just trigger it
      jQuery(".saved-carts-nav li").first().trigger("click");
    }

    //GET SINGLE CART
    jQuery("#saved-items").on("click",".itm-cart .itm-vw",function(){
      var identifier = jQuery(this).prop("id");
      current_cart = identifier;
      data={"type":"getSearch","UserId":uid,"search_type":"cart","search_id":identifier};
      heyAjax(data,addscc,adderr);
      jQuery("#current-cart").val(current_cart);
      jQuery(".ss-current").trigger("click");
    });
    
    // GET SINGLE QUERY
    jQuery("#saved-items").on("click",".itm-query .itm-vw",function(){
      var theurl = jQuery(this).prop("id");
      window.location.replace(base_url+"views/home.php"+theurl);
    });
    
    
  //====    DELETE    ====
  //======================
    
    //Delete Cart - Execute
    jQuery("#saved-carts").on("click",".dlt-crt",function(){
      var identifier = jQuery(this).parents("li").prop("id").substr(8);
      if(jQuery.isNumeric(identifier)){
        ctype = "cart";
      }else{
        ctype = "query";
        var identifier = jQuery(this).parents("li").prop("id").substr(9);
      }
      var usure = confirm("Are you sure you want to delete this?");
      if(usure){
        data = {"type":"deleteCart","UserId":uid,"cart_id":identifier,"cart_type":ctype};
        heyAjax(data, dcscc, sserr);
      }
    });

    // Cart / Querys nav
    jQuery(".saved-carts-nav li").click(function(){
      jQuery(".saved-carts-nav li").removeClass("active");
      jQuery(this).addClass("active");
      var identifier = jQuery(this).text().toLowerCase();
      data = {"type":"getSearch","UserId":uid,"search_type":identifier,"search_id":""}
      heyAjax(data,ssscc,sserr);
    });

    //  Cart - Remove Item - Execute
    jQuery("#saved-search-form").on("click",".ssi-dl",function(e){
       e.preventDefault();
       var thisid = jQuery(this).parents("li").prop("id"); 
       var data = {"type":"removeItem","UserId":uid,"cart_id":current_cart,"item_id":thisid}
       heyAjax(data,delscc,adderr);
    });
    
    //  Cart - Delete - Execute
    jQuery("#clear-saved-search").click(function(e){
      e.preventDefault();
      var sure = confirm("Are you sure you want to delete this cart?");
      if(sure){
        var data = {"type":"deleteCart","UserId":uid, "cart_id":current_cart, "cart_type":"cart"};
        heyAjax(data,dcscc,adderr);
      }
    })

  //====    Navigation     ====

  //-------------------
  //    Sidebar Nav
  //-------------------
  jQuery(".sb-nav a").click(function(e){
      e.preventDefault();
      var action = jQuery(this).prop("id").substr(7);
      setActiveSidebar(action);
  })
  setActiveSidebar = function(action){
    if(action == "search"){
      ssstatus = true; 
      getSearch();
    }
    else if(action == "filter"){
      ssstatus = false;
    }
    jQuery("#sb-view").val(action.slice(0,1));
    jQuery(".sb-nav a").removeClass("active");
    jQuery("#sb-nav-"+action).addClass("active");
    jQuery(".sidebar form").hide("fade"); 
    jQuery(".sb-form-"+action).show("drop");
  }
  setActiveSidebar(sb);

  //current cart
  jQuery(".ss-current").click(function(e){
    e.preventDefault();
    jQuery("#cart").show("clip");
    jQuery("#saved-carts").hide("clip");
  });

  // All subnav links - set active state
  jQuery("#ss-subnav li a").click(function(){
    jQuery("#ss-subnav li a").removeClass("active");
    jQuery(this).addClass("active");
    active_cart_view = jQuery(this).text();
  });

// On load get current and setup cart. 
//getSearch();
setCartActions(current_cart);

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

    var updateSelectList = function(idx,indices){
        var origval = jQuery("#faux-select-input").val();
        console.log(indices);
        console.log(idx);
        if(idx != "_all"){
          var clr = indices[idx].color;
          var txt = indices[idx].title;
          if(idxval.indexOf("_all")!=-1){
            idxval.splice(idxval.indexOf("_all"),1);
            idxdisplay.splice(idxval.indexOf("_all"),1);
          }
          if(idxval.indexOf(idx) == -1){
            idxval.push(idx);
            idxdisplay.push("<span class='idx' style='background:" + clr + "'>"+txt+"</span>");
          }else{
            idxdisplay.splice(idxval.indexOf(idx),1);
            idxval.splice(idxval.indexOf(idx),1);
          }
        }else{
          idxval = ["_all"];
          idxdisplay = ["<span class='idx'>All</span>"]
        }
        if(idxval.length == 0 || idxval.length == 4){
          idxval = ["_all"];
          idxdisplay = ["<span class='idx'>All</span>"]
        }
          var spanlength = (100/idxval.length)+"%";
        jQuery("#faux-select-selector span").empty().html(idxdisplay);
        jQuery(".idx").css("width",spanlength);
        jQuery("#faux-select-input").val(idxval);
    }

//jQuery(document).ready(function(){
    idxval = [];        
    idxdisplay = [];
    //updateSelectList(loadval);
    //set the form value on load
    if(loadval == ""){
        loadval = "_all";
    };
    loadval = loadval.split(",");
    jQuery.each(loadval,function(k,v){
      updateSelectList(v,indices);
    }); 
    jQuery("#faux-select-input").val(loadval);
    //updateSelectList(loadval);
                
    var isopen =  false;
    //Show / hide select list
    jQuery("#faux-select-selector").click(function(e){
        e.preventDefault();
        if(isopen == false){
            jQuery("#faux-select-list").show("blind");
            isopen = true;
        }else{
            jQuery("#faux-select-list").hide("blind");
            isopen = false;
        }
    });
            
    jQuery(document).mouseup(function (e){
        var container = jQuery("#faux-select");
        var tohide = jQuery("#faux-select-list");
        if (!container.is(e.target) && container.has(e.target).length === 0){
            tohide.hide();
            isopen = false;
        }
    });


    //Update select values on selection
    jQuery("#faux-select li").click(function(e){
        //jQuery("#faux-select-list").hide();
        updateSelectList(jQuery(this).prop("id").substr(3),indices);
        isopen = false;
    });
//});


$(function(){
  
  // Keep a mapping of url-to-container for caching purposes.
  var cache = {
    // If url is '' (no fragment), display this div's content.
    '': $('#results')
  };
  
  // Bind an event to window.onhashchange that, when the history state changes,
  // gets the url from the hash and displays either our cached content or fetches
  // new content to be displayed.
  $(window).bind( 'hashchange', function(e) {
    
    // Get the hash (fragment) as a string, with any leading # removed. Note that
    // in jQuery 1.4, you should use e.fragment instead of $.param.fragment().
    var url = $.param.fragment();
    
    // Remove .bbq-current class from any previously "current" link(s).
    $( 'a.fv-current' ).removeClass( 'fv-current' );
    
    // Hide any visible ajax content.
      $( '.more-data' ).fadeOut(2000);
      $('.result').show();
      var $container = $('#results');
      $container.masonry();
    // Add .bbq-current class to "current" nav link(s), only if url isn't empty.
    url && $( 'a[href="#' + url + '"]' ).addClass( 'fv-current' );
    
    if ( cache[ url ] ) {
      // Since the element is already in the cache, it doesn't need to be
      // created, so instead of creating it again, let's just show it!
      //jQuery("#"+cache[ url ]).show();
      jQuery("#"+url).fadeIn("fast");
    } else {
      // Show "loading" content while AJAX content loads.
      
      // Create container for this url's content and store a reference to it in
      // the cache.
      cache[ url ] = $( '<div class="more-data-sv"/>' )
        // Append the content container to the parent container.
        .appendTo( '#results' )
        
        // Load external content via AJAX. Note that in order to keep this
        // example streamlined, only the content in .infobox is shown. You'll
        // want to change this based on your needs.
        .load( url, function(){
          // Content loaded, hide "loading" content.
          $( '#loading-box' ).fadeOut();
          jQuery("#"+url).fadeIn("slow");
        });
    }
  })
  
  // Since the event is only triggered when the hash changes, we need to trigger
  // the event now, to handle the hash the page may have loaded with.
  $(window).trigger( 'hashchange' );
  
});

