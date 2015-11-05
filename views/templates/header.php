<?php
$session = new Session();
include(__DIR__."/../../ctrl/requests.php");
include_once(__DIR__."/../../ctrl/search-ctrl.php");
$search = new Search();
$request = new Req();
$method = $_SERVER['REQUEST_METHOD'];
$checkrequest = array("POST","GET");

//---------------------+
//    Validate User    |
//---------------------+
$uid = ""; $md5user = ""; $pagename = "";
if(isset($_SESSION) && isset($_SESSION['status']) && $_SESSION['status']=="logged in"){
    //$user = $session->getUser();
    $user = $_SESSION['user'];
    $user_first_name = explode(" ",$user);
    $user_first_name = $user_first_name[0];
    $uid = $_SESSION['uid'];
    $md5user = md5($uid);
    $pageuri = pathinfo($_SERVER['REQUEST_URI']);
    $pagename = $pageuri['filename'];
    if(strpos($pagename,"?")){
       $pagename = explode("?", $pagename);
       $pagename = preg_replace('/\\.[^.\\s]{3,4}$/', '', $pagename[0]);
    }
}else{
    header("Location:".$base_url."login.php");
}

//----------------------------------+
//    Check Request and sanitize    |
//----------------------------------+
if(!in_array($method,$checkrequest)){
    $_REQUEST = $request->handle_request($method,$_REQUEST);
}else{
    if(isset($_GET) && !isset($_GET['starsan'])){
        $_GET = $request->handle_request("GET",$_GET);
    }
    if(isset($_POST) && !isset($_POST['starsan'])){
        $_POST = $request->handle_request("POST",$_POST);
    }
}

//------------------------+
//    Set Current Cart    |
//------------------------+
if(isset($_GET['current_cart']) && $_GET['current_cart'] != ""){
  $current_cart = $_GET['current_cart'];
}else{
  $current_cart = 0;
}
if(isset($_GET['sb']) && $_GET['sb'] != ""){
  $sb = $_GET['sb'];
  if($sb == "s"){
    $sb = "search";
  }else if($sb == "f"){
    $sb = "filter";
  }
}else{
  $sb="filter";
}
//-------------------------------+
//    Define & Compile Search    |
//-------------------------------+
$match_type = "match";
if(isset($_GET)  && !empty($_GET) && (isset($_GET['search'])||isset($_GET['svsrch']))){
    if(isset($_GET['index'])){
        $index = $_GET['index'];
    }else{
        $index = "_all";
    }
    $searchterm="";
    $elastic_type=$allowed_types;
    $view="thum";
    if(isset($_GET['match_type'])){$match_type = $_GET['match_type'];}
    if(isset($_GET['search']) && !empty($_GET['search'])){$searchterm=html_entity_decode($_GET['search']);}
    if(isset($_GET['index']) && $_GET['index']!="" && $_GET['index']!="_all"){
        $elastic_index=$_GET['index'];
    }else{
        $ec = array();
        foreach($elastic_config as $k => $v){
            $ec[]=$k;
        }
        $elastic_index=implode(",",$ec);
    }
/*    if(isset($_GET['type'])){
        $elastic_type=$_GET['type'];
        if(is_array($elastic_type)){
            $elastic_type=implode(",",$elastic_type);
        }
    }*/
    if(isset($_GET['view'])){$view = $_GET['view'];}
    if(isset($_GET['cp'])){$current_page = $_GET['cp'];}else{$current_page=0;};
    $perpage=25;
    $from=$current_page*$perpage;
    include(__DIR__."/../../include/queries.php");
}
$items = $search->getMappings();
$items = json_decode($items, true);

//Set Original Filters
$orig_filters = $search->setOriginalFilters();
$filterranges = array();
$filterdates = array();
foreach($items as $k => $v){
    foreach($v['mappings'] as $k => $vv){
        foreach($vv['properties'] as $pp => $val){
       if(isset($val['type']) && ($val['type'] == "integer" || $val['type'] == "number")){
            $filterranges[]=$pp;
       }else if(isset($val['type']) && $val['type'] == "date"){
           $filterdates[]=$pp;
       }
    }
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php if(isset($pagename) && $pagename != ""){echo ucwords($pagename." | ");} echo $site_title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?php echo $base_url?>css/style.css">
    <script>
      var current_cart = <?php echo $current_cart ?>;
      var pagename = "<?php echo $pagename;?>";
      var uid = "<?php echo $uid;?>";
      var user_name = "<?php echo $user;?>";
      var sb = "<?php echo $sb;?>";
      var md5uid = "<?php echo $md5user?>";
      var loadval = "<?php if(isset($index)){echo $index;}else{echo '_all';} ?>";
      var base_url = "<?php echo $base_url?>";
      var orig_filters = <?php echo json_encode($orig_filters);?>;
      var fltranges = <?php echo json_encode($filterranges); ?>;
      var fltdates = <?php echo json_encode($filterdates);?>;
      var indices = <?php echo json_encode($elastic_config);?>;
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
    <script src="<?php echo $base_url;?>js/jquery-migrate-1.2.1.min.js"></script>
    <script src="<?php echo $base_url;?>js/ckeditor/ckeditor.js"></script>
    <script src="<?php echo $base_url?>js/jquery.ba-bbq.min.js"></script>
  </head>
  <body class="page-<?php echo $pagename; ?>">
    <div id="loading-box"><b><em><span>Sea</span><span>rch</span><span>ing</span><span>...</span></em></b></span></div>
    <header>
      <h1><a id="logo" href="<?php echo $base_url;?>views/home.php"><span>Meta</span> <img src="<?php echo $base_url?>gfx/MIA_LOGO_MARK.svg"/></a></h1>
      <?php
        include __DIR__."/header_navigation.php";
        include __DIR__."/main_search_form.php";
      ?>
  </header>
  <section id="main-content" role="main">
