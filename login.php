<?php
date_default_timezone_set('America/Chicago');
include_once(__DIR__."/include/config.php");
?>
<html>
  <head>
    <title><?php echo $site_title;?> - Login</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
    <style type="text/css">
.clr-red{color:rgb(239 , 53 , 53)}/*Red*/
.clr-lblue{color:rgb(140 , 210 , 205)}/*Light Blue*/
.clr-yellow{color:rgb(128 , 86 , 150)}/*Purple*/
.clr-green{color:rgb(155 , 206 , 124)}/*Green*/
.clr-dblue{color:#173C54}/*Dark Blue*/


    html{
        background: rgb(35 , 35 , 35);
	overflow:hidden;
    }
    body{
        font-family: 'Titillium Web', sans-serif;
        width: 100%;
        height: 100%;
        margin:0;
        background: rgb(35 , 35 , 35);
        text-align: center;
    }
    a{
        font-size: 12px; 
    }
    form{
        display: block;
        margin: 150px auto 0 auto;
        width: 30%;
        padding: 30px 0;
        position: relative;
    }
    #form-wrap > h2{
       color: #FFFFFF;
    }
    #form-wrap > h2 em{
      float:left;
      letter-spacing:5px;
    }
    input{
       display: block;
       padding: 7px 50px 7px 7px;
       margin: 5px auto;
       font-size: 14px;
       width: 100%;
       border: none;
//       background: #908F8A;
       color: #000;
    }
    ::-webkit-input-placeholder { /* WebKit browsers */
       color:    #000;
       opacity: 1 !important;
    }
    input[type="submit"]{
       background: #1975A3;
       color: #FFF;
       border: 10px solid #292823;
       padding : 5px 15px;
       position: absolute;
       right: -25px;
       bottom: 55px;
       width: 50px;
       height: 50px;
       border-radius: 50%;
       padding: 0;
    }
    input[type="submit"]:hover{
       cursor: pointer;
       background: #00527A;
    }
    #err{
      margin-top: 10px;
      color: #B3432E;
      display: block;
      padding: 10px;
      border: 1px solid #B3432E;
      border-radius: 5px;
      width: 40%;
      position:fixed;
      left:0;
      right:0;
      margin: 20px auto;
    }
    #disclaimer{
     border: 1px solid #FFF;
     color:rgb(219 , 205 , 185);
//     color:rgb(239 , 53 , 53);
      padding: 15px 0;
      width: 95%;
      border-radius: 5px;
      margin: 5px auto 0 auto;
     letter-spacing:0.1em;
    }
    </style>
  </head>
  <body>
    <?php
     $t = date("H");
     $on = true;
    if( $t > 8 && $t < 19){ $on = false;}if($on == true){?> 
    <div id="disclaimer">Usernames and passwords are expired weekdays from 7pm-6am(CTS). [Current time <?php echo date("H:i");?>]<br/>Please contact the help desk for further assistance.</div>
    <?php }?>
    <form id="login-form" method="POST" action="<?php echo $base_url;?>ctrl/ldap_ctrl.php">
    <div id="form-wrap">
      <h2><em>Meta</em> <img src="<?php __DIR__?>/gfx/MIA_LOGO_MARK.svg"/></h2>
      <input type="text" id="uid" name="uid" placeholder="Username"/>
       <input type="password" id="upass" name="upass" placeholder="Password"/>
      <input type="submit" value="&#10148;">
      <br/>
     <!-- <a href="#">Forgot your password?</a>-->
    <div id="msg"></div>
    </div>
    </form>
    <script type="text/javascript">
        jQuery("#login-form input[type='submit']").click(function(e){
          e.preventDefault();
          // check valid?
          var url = jQuery("#login-form").attr("action");
          console.log(url)
          jQuery.ajax({
              url: url,
              method:"POST",
              data:jQuery("#login-form").serialize(),
              success:function(data){
                  data = JSON.parse(data);
                  if(data.error == false){
                      window.location.replace("/views/home.php");
                  }else{
                     jQuery("#msg").empty().append("<span id='err'>"+data.msg+"</span>");
                     jQuery("body").effect("shake");
                     setTimeout(function(){
                         jQuery("#err").fadeOut("slow",function(){jQuery(this).remove();});
                     },5000);
                  }
              },
              error:function(data){
                  alert("Internal error:"+data);
              }
          });
        });
    </script>
  </body>
</html>
