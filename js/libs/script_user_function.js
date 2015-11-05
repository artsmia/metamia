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
