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
