<?php
function analyzeTerms($searchstr){
  return analyzedTerms;
}
//------------------+
//    Main Query    |
//------------------+
$stopwords=array("in","a","the","of","and","to","or","&","@");
$queryType = array();
//maker sure theres a term(s)
if($searchterm !=""){
  $boolop = "must";
  $searchterm = iconv('UTF-8', 'ASCII//TRANSLIT', $searchterm);
  $st = $searchterm;
  //Check to see if it's an exact match
  if($match_type=="match_phrase"){
    $queryType['bool']['must'][][$match_type]['_all']['query']=$st;
  }else{
    //  DETERMINE PHRASES
    if(iconv_strpos($st,'"')!==false){
      $quotecount = mb_substr_count($st,'"');
      //  make sure there is atleast 2
      if($quotecount >= 2){
         // make sure there is an even count
         if($quotecount %2 == 0){
           $phrasecount = ($quotecount * 0.5);
           for($i = 0; $i<$phrasecount; $i++){
             $boolop = "must";
             $phrase = "";
             $pos1 = strpos($st,'"');
             $pos2 = strpos($st,'"',($pos1+1));
             $phrase = substr($st,($pos1+1),-(strlen($st)-($pos2)));
             if(strpos($phrase, "!") !== false && strpos($phrase,"!")==0){
               $boolop="must_not";
               $phrase = ltrim($phrase,"!");
             }
             $st = substr_replace($st,"",$pos1,(($pos2-$pos1)+1));
             $queryType['bool'][$boolop][]['match_phrase']['_all']['query']=$phrase;
           }
         }else{
           //do something
         }
      }
    }
    if($st != ""){
      $sterm = explode(" ",$st);
      foreach ($sterm as $key=>$term){
        if($term != ""){
        // Check for wildcard
        if((strpos($term,"?")||strpos($term,"*"))!=false){
          $queryType['bool']['must'][]["wildcard"]["_all"]=strtolower($term);
        // Check for MUST_NOT exclusion
        }else if(strpos($term, "!") !== false && strpos($term,"!")==0){
            $term = ltrim($term, "!");
            $queryType['bool']["must_not"][]['match']['_all']['query']=$term;
        }else{
          if(in_array($term,$stopwords)){
            $queryType['bool']['should'][]['bool']['should'][][$match_type]['_all']['query']=html_entity_decode($term);
          }else{
            $queryType['bool']['must'][][$match_type]['_all']['query']=html_entity_decode(htmlentities($term));
          }
        }
        }
      }
    }
  }
// If theres no search terms just run a match all query
}else{
    $queryType = array("match_all"=>array());
}

$queryData = array(
    "highlight" => array(
        "tag_schema" => "styled",
         "fields" => array(
            "*" => array(
                "pre_tags" => array(htmlentities("<em class='highlight'>")),
                "post_tags" => array(htmlentities("</em>"))
             )
         )
    ),
    "from" => $from,
    "size"=> $perpage,
    "query"=>array(
        "filtered"=>array(
            "query"=>$queryType
            //apend filters here
        )
    ),
    "sort"=>""
);
//---------------+
//    Filters    |
//---------------+
if(isset($_GET['filter']) && $_GET['filter']!=""){
  $filters = $_GET['filter'];
}
$operator="and";
function determineOperator($opr){
switch($opr){
   case 0: // equals
      return false;
   break;
   case 1: // greater than
      return "gt";
   break;
   case 2: // less than
      return "lt";
   break;
   case 3: //greater than equal to
      return "gte";
   break;
   case 4: //less than equal to
      return "lte";
   break;
}
}
function rangeFilter($k){
    $rangeval = substr($k,strpos($k,":op=")+4);
    $k = explode(":op=",$k);
    $k = $k[0];
    $opr = determineOperator($rangeval);
    return array($k,$rangeval,$opr);
}
if(isset($filters)){
    $cpf = compileFilters($filters);
    if(isset($cpf['range']) && !empty($cpf['range'])){
        $queryData['query']['filtered']['filter']['bool']['must'][]=$cpf['range'];
    }
    if(isset($cpf['term']) && ($cpf['term'])){
       $queryData['query']['filtered']['filter']['bool']['must'][]=$cpf['term'];
    }
    if(isset($cpf['stop'])){
       $queryData['query']['filtered']['filter']['bool']['should']=$cpf['stop'];
    }
}
if(isset($_GET['filterno'])){
    $cpf = compileFilters($_GET['filterno']);
    if(isset($cpf['range']) && !empty($cpf['range'])){
        $queryData['query']['filtered']['filter']['bool']['must_not'][]=$cpf['range'];
    }
    if(isset($cpf['term']) && ($cpf['term'])){
       $queryData['query']['filtered']['filter']['bool']['must_not'][]=$cpf['term'];
    }
}
if(isset($_GET['filteror'])){
    $cpf = compileFilters($_GET['filteror']);
    if(isset($cpf['range']) && !empty($cpf['range'])){
        $queryData['query']['filtered']['filter']['bool']['should'][]=$cpf['range'];
    }
    if(isset($cpf['term']) && ($cpf['term'])){
       $queryData['query']['filtered']['filter']['bool']['should'][]=$cpf['term'];
    }
}
              
//Query Assistant filters
if(isset($_GET['qaf'])){
  foreach($_GET['qaf'] as $qk => $qv){
     $mult = false;
     $tt = "term";
     if(count(explode(" ",$qv))>1){
       $qv = explode(" ",$qv);
       $tt = "terms";
     }
     $qaf_indices = array();
     $qaf_filters = array();
     $qaf_terms = array();
     $qfilter = $assistant_filters[$qk];
     foreach($qfilter as $qfk => $qfv){
       if(is_array($qfv)){
         foreach($qfv as $qfkv => $qfvv){
           $qaf_terms[] = array($tt=>array($qfvv=>strtolower($qv)));
         }
       }else{
           $qaf_terms[]=array($tt=>array($qfv=>strtolower($qv)));
       }
     }
     if(count($_GET['qaf'])>1){
       $qaf_filters = array("or"=>array("filters"=>$qaf_terms));
     }else{
       $qaf_filters = array("or"=>array("filters"=>$qaf_terms));
     }
     $queryData['query']['filtered']['filter']['bool']['must'][]["and"][]=$qaf_filters;
  }
}
function compileFilters($f){
   global $stopwords;
   $rangray = array();
   $stopray = array();
   $range;
   $termray = array();
   //foreach filter
   foreach($f as $k => $v){
     for($i = 0; $i<count($v); $i++){
       $nterm = array();
       $range = false;
       //determine if its a range
       if(strpos($k,":op=")!=false){
           $range = true;
           $frng = rangefilter($k);
           if($frng[2] != false){
             $k = $frng[0]; $rangeval = $frng[1]; $opr = $frng[2];
           }else{
           $range = false;
             $k = $frng[0];
           }
       }
       $v[$i] = strip_tags(htmlspecialchars_decode($v[$i]));
       $arr = preg_split("/[ ()$%,;\/.:;?&@#_-]/", $v[$i]);
       if(!empty($arr)){
          $fterm = $arr;
       };
       if(count($fterm)>1 && $range == false){
           foreach($fterm as $ftk => $ftv){
               
               if(!in_array($ftv,$stopwords) && $ftv!=""){
                 $nterm[] = strtolower($ftv);
               }
           }
           $termray[]=array("terms"=>array($k=>$nterm,"execution"=>"and"));
       //  else if single term
       }else{
           if($range == false){
               $termray[]=array("term"=>array($k=>strtolower($v[$i])));
           }
           if($range == true){
               $rangray[]=array("range"=>array($k=>array($opr=>$v[$i])));
           }
       }
     }
   }
   return array("range"=>$rangray,"term"=>$termray,"stop"=>$stopray);
}

//Filter for type sorting
/*$filter_map = array(
    "image"=>array(
        "type"=>array(
            "or"=>array(
                "Resource Type"=>"Photo",
                "image"=>"valid",
                "Media Type"=>"Photo",
            ),
        ),
    ),
    "video"=>array(
       "type"=>array(
            "or"=>array(
               "Resource Type"=>"Video",
               "Media Type"=>"Video"
            ),
        ),
    ),
    "audio"=>array(
       "type"=>array(
           "or"=>array(
               "Resource Type"=>"Audio",
               "Media Type"=>"Audio"
           ),
       ),
    ),
    "document"=>array(
        "type"=>array(
            "or"=>array(
                "Resource Type"=>"Document",
                "Media Type"=>"Document"
            ),
        ),
    ),
    "object"=>array(
        "type"=>array(
            "exists"=>array(
                 "objectid",
                 "accession number"
            )
        )
    ),
    "maps"=>array(
        "type"=>array(
            "or"=>array(
               "keyword"=>"map",
            )
        )
    ),
);*/
//---------------------------+
//    Pre Defined Filters    |
//---------------------------+
$defined_filters=array(
    "Image"=>array(
        "resourcespace"=>array("term"=>array("resource_type"=>"photo")),
        "mediabin"=>array("term"=>array("Media Type"=>"photo"))
//        "objects"=>array("image"=>"valid")
    ),
    "Video"=>array(
        "resourcespace"=>array("term"=>array("resource_type"=>"video")),
        "mediabin"=>array("term"=>array("Media Type"=>"video"))
    ),
    "Document"=>array(
	"resourcespace"=>array("term"=>array("resource_type"=>"document")),
         "mediabin"=>array("term"=>array("Media Type"=>"document"))
    ),
    "Audio"=>array(
        "resourcespace"=>array("term"=>array("resource_type"=>"audio")),
        "mediabin"=>array("term"=>array("Media Type"=>"audio"))
    ),
    "Art Object"=>array(
        "objects"=>array("type"=>array("value"=>"object_data")),
    ),
    "page"=>array(
        "yoursite.org"=>array("type"=>array("value"=>"page"))
    )
);

if(isset($_GET['type'])){
    foreach($defined_filters as $k => $v){
        if(in_array($k,$_GET['type'])){
            foreach($v as $kk => $vv){
              $filter_indices[]=$kk;
              $index_terms[]=$vv;
            }
        }
    };
    //IMAGES
    $inx_f = array("indices" => array(
//        "indices" => array("resourcespace","objects"),
        "indices"=>$filter_indices,
        "filter" => array("or" => $index_terms),
            "no_match_filter"=>array(
                "exists"=>array("field"=>"needstonotmatch")
            ),
    ));
    $queryData['query']['filtered']['filter']['bool']['must'][]=$inx_f;
}
//---------------+
//    Sorting    |
if(isset($_GET['ordval']) && $_GET['ordval']!="Relevance"){
    $sort = array(
       "_score"=>array(
           "order"=>$_GET['ordtype']
       ),
       $_GET['ordval']=>array(
           "order"=>$_GET['ordtype'],
           "missing"=>"_last",
//           "ignore_unmapped"=>true,
       )
    );
    $queryData['sort']=$sort;
}else if(isset($_GET['ordval']) && $_GET['ordval']=="Relevance"){
    $sort = array(
        "_score" => array(
            "order"=> $_GET['ordtype']
        )
    );
    $queryData['sort']=$sort;
}else{
    $sort = array(
        "_score" => array(
            "order"=> "desc"
        )
    );
    $queryData['sort']=$sort;
}
?>
