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

