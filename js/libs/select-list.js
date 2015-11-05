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

