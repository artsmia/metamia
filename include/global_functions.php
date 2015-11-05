<?php
function formatkey($uglykey){
               $prettykey = ucwords(str_replace("_"," ",$uglykey));
               return $prettykey;
           }

function makePagination($response,$from,$perpage,$current_page){

      $totalpages = ceil(round($response['hits']['total']/$perpage,2));
      $pagination = array();
      $pages=array();

      //-----------------------+
      //    Previous Paging    |
      //-----------------------+
      if($current_page !=0){
        //First
         $pagination[] = "<a id='pg-first' class='pg-link'>< First</a>";
         //Prev
         $pagination[] = "<a id='pg-prev' class='pg-link'>< Prev</a>";
      }

      //-----------------------+
      //    Numbered Paging    |
      //-----------------------+
      if($totalpages>10){
        if($current_page <= 5){
          $offset=0;
        }else{
          $offset = $current_page-5;
        }
        for($p=$offset; $p<($offset+10); $p++){
          if($p < $totalpages){
            if($current_page == $p){
              $pagination[] = "<a id='pg-".$p."' class='pg-link-inr active'><b> ".($p+1)."</b></a>";
            }else{
              $pagination[] = "<a id='pg-".$p."' class='pg-link-inr'> ".($p+1)."</a>";
            }
            $pages[]=$p;
          }
        }
      }else{
        for($p=0; $p<$totalpages; $p++){
          if($current_page == $p){
           $pagination[] = "<a id='pg-".$p."' class='pg-link-inr active'><b>".($p+1)."</b></a>";
          }else{
          $pagination[] = "<a id='pg-".$p."' class='pg-link-inr'>".($p+1)."</a>";
          }
          $pages[]=$p;
        }
      }

      //-------------------+
      //    Next Paging    |
      //-------------------+
      $nextpage=$current_page+1;
      if($current_page != ($totalpages-1)){
        //next
        $pagination[] = "<a id='next'> Next > </a>";
        //last
        $pagination[] = "<a class='last' id='pg-".($totalpages-1)."'> Last [".($totalpages)."]>></a>";
      }
      if(count($pages)>1){
        return implode('',$pagination);
      }
}

function create_excerpt($str, $leng){

    //strip all html except the em tags returned from elastic search
    $stripped=strip_tags($str,'<em></em>');

    //find the next white space from the given length
    $leng = strpos($stripped, " ", $leng);
    if(strlen($stripped) > $leng){
        $newres = substr($stripped, 0,-(strlen($stripped)-$leng));
    }

    //count how many open and closed em tags we have
    $countopen = substr_count($newres,'<em>');
    $countclose = substr_count($newres,'</em>');

    //if where short add one.
    if($countopen > $countclose){
        $newres.="</em>";
    }
    $newres .= "<em class='excerpt'> [...] </em>";
    return $newres;
}
function replaceHighlight($str,$highlight,$field,$chunk,$frompaged){
    //work out each highlight match into an array of strings
    $posnav = array();
    $hr = array();
    if(!isset($highlight[$field])){
        if($frompaged){
          return array($str,$posnav);
        }else{return $str;}

    }else{

    foreach($highlight[$field] as $hkey => $hval){
        //if there's multiple highlight fields for this value
        if(is_array($hval)){
            foreach($hval as $hk => $hv){
                $hr['stripped'][$hk]=htmlentities(strip_tags(html_entity_decode($hv)));
                $hr['original'][$hk]=htmlentities($hv);
            }
        }else{
            $hr['stripped'][$hkey]=htmlentities(strip_tags(html_entity_decode($hval)));
            $hr['original'][$hkey]=htmlentities($hval);
        }
    }
    // Do the replacement
    $ns = "";
    $s = htmlentities($str,ENT_QUOTES);
echo("<pre>");
//var_dump($hr);
echo("</pre>");
    foreach ($hr['stripped'] as $highkey => $highval){
        $strlength = strlen($s);
        $searchfor = trim($highval);
        $replacement = $hr['original'][$highkey];
        $length = strlen($searchfor);
        $pos = stripos($s,$searchfor);
        if($pos !== false){
            $posnav[floor($pos/2000)]="highlight";
            $strend = -($strlength - ($pos+strlen($searchfor)));
            if($strend >= -0){
                $ns = substr_replace($s, $replacement, $pos);
            }else{
                $ns = substr_replace($s, $replacement, $pos, $strend);
            }
        }
        $s = $ns;
    }
    if($frompaged){
        return array($s,$posnav);
    }else{
        return $s;
    }
}
}

function pagedText($str, $id, $field, $highlight, $chunk){
    $str = strip_tags(html_entity_decode($str));
    $count = 0;
    $nav = array();
    $newresult = array();
    $posnav = array();

    //If the highlight field is present replace the text with the highlighted ems
    if($highlight != false){
       $rhlt=replaceHighlight($str,$highlight,$field,$chunk,true);
       $str = $rhlt[0];
       $posnav = $rhlt[1];
    }
    $totelen = strlen($str);
    $lastone = false;
    while($totelen > 0){
        if(strlen($str) > $chunk){
          $leng = strpos($str, " ", $chunk);
          if($leng == false){
            $leng = strlen($str);
            $lastone = true;
          }
        }else{
          $lastone = true;
          $leng = strlen($str);
        }
        //  Cut text
        $page = substr($str, 0, $leng);
        $newresult[] = "<div class='pt-page-".$count." ptp'>".$page."</div>";
        if(isset($posnav[$count])){
            $nav[] = "<li><a class='pt-nav-".$count." highlight'>".$count."</a></li>";
        }else{
            if($count==0){
              $nav[] = "<li><a class='pt-nav-".$count." active'>".$count."</a></li>";
            }else{
              $nav[] = "<li><a class='pt-nav-".$count."'>".$count."</a></li>";
            }
        }
        //  check length and if its less than the char leng to
        //  seperate by than set the loop to stop to prevent a never ending loop.
        if($lastone == false){
          $totelen = ($totelen - $leng);
        }else{
          break;
        }
        $count++;
        $str = substr($str, $leng);
    }
    $count=0;
    return "<div class='paged-text' id='pt-".$id."'><ul class='pt-nav'>".implode('',$nav)."</ul>".implode('',$newresult)."</div>";
    unset($nav); unset($newresult);
    ob_flush();
}
?>
