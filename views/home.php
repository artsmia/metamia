<?php
include_once(__DIR__."/../include/config.php");
include_once(__DIR__."/../include/db.php");
include_once(__DIR__."/../include/sessions.php");
include_once(__DIR__."/../include/global_functions.php");
include_once(__DIR__."/templates/header.php");

//----------------------------------+
//    No Search + No Save = Home    |
//--------------------------------- +
if(!isset($_GET['search']) && !isset($_GET['svsrch'])){
  include_once(__DIR__."/templates/welcome.php");
//----------------------+
//    Perform Search    |
//----------------------+
}else if(isset($_GET['svsrch']) || isset($_GET['search']) && !empty($_GET)){

  // Saved Search
  if(isset($_GET['svsrch'])){
    $elastic_type="";
    $elastic_index = $allowed_indexes;
    include_once(__DIR__."/../include/queries/saved_search_query.php");
    $rc = $search->recallSavedSearch();
    if($rc != false){
      $queryData['query']['ids']['values'] = $rc;
    }else{
        echo("<span class='no-rslts'>Sorry we can not re-call this search. It may have expired!</span>");
    }
  }
//  var_dump(json_encode($queryData));
  $response = json_decode($search->sendQuery($queryData),true);

  // if Response fails
  if($response == false){?> <script> confirm("Sorry we are unable to connect to elastic search at this time.");</script> <?php exit();};

  // Not Found
  if((!isset($response['hits']) || $response['hits']['total']==0 ) && $pagename == "home"){
    include_once(__DIR__."/not-found.php");
  }else{
  // Results
    include(__DIR__."/../ctrl/results.php");
    $hits = $response['hits']['hits'];
    include(__DIR__."/dashboard.php");
    include(__DIR__."/results.php");
  }
}
include __DIR__."/templates/sidebar.php";
include __DIR__."/templates/footer.php";
?>
