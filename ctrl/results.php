<?php
class Results{
  private $title;
  private $title_msg = "Click on this title to add a filter specifically for";
  public function configResult($index,$result,$rtype){
    global $elastic_config, $download_path;
    $ec = array();
    $ecr = $elastic_config[$index];
    $ec['index']=$index;
    //define title
    $ec['source'] = $ecr['title'];
    $ec['title'] = $ecr['result']['title'];
    if(isset($result[$ecr['result']['title']]) && $result[$ecr['result']['title']]){
      $this->title = $result[$ecr['result']['title']];
    }else{
      $this->title = "No Title";
    }
    //define color
    $ec['color'] = $ecr['color'];

    //define thumbnail
    $validimg = true;
    if(isset($ecr['result']['image_valid'])){
      // if validation field is set check to see that the image is valid
      $eciv = $ecr['result']['image_valid'];
      if(isset($result[$eciv[0]]) && $result[$eciv[0]] != $eciv[1]){
        $ec['thumb'] = false;
        $validimg = false;
      }
    }
    if($validimg){
      if(is_array($ecr['result']['image'])){
        if(isset($ecr['result']['image']['type'])){
          //different images based on type
          $touse = $ecr['result']['image']['type'][$rtype];
          $ec['thumb'] = $touse[0].$result[$touse[1]].$touse[2];
          
        }else{
          $ec['thumb']= $ecr['result']['image'][0].$result[$ecr['result']['image'][1]].$ecr['result']['image'][2];
        }
      }else{
        if(isset($result[$ecr['result']['image']])){
          $ec['thumb'] = $result[$ecr['result']['image']];
        }else{
          $ec['thumb'] = false;
        }
      }
    }
    //define link
    if(is_array($ecr['result']['link'])){
      // url stored as parts [0] = urlpath; [1] = $result identifier; [2] = prepend after identifier
      $ec['url']= $ecr['result']['link'][0].$result[$ecr['result']['link'][1]].$ecr['result']['link'][2];
    }else{
      $ec['url']=$ecr['result']['link'];
    }
    //define download
    if(is_array($ecr['result']['download'])){
      //download stored as parts : [0] = url path; [1] = $result identifier; [2] = options [3] = bool use inteface downloader?
      $vars = array();
      if(is_array($ecr['result']['download'][1])){
        foreach($ecr['result']['download'][1] as $k => $v){
          $vars[]="&".$k."=".$result[$v];
        }
        $ecr['result']['download'][1] = implode("",$vars);
      }else{
        $ecr['result']['download'][1] = $result[$ecr['result']['download']['1']];
      }
      $dl = $ecr['result']['download'][0].$ecr['result']['download'][1].$ecr['result']['download'][2];
      if(isset($ecr['result']['download'][3]) && $ecr['result']['download'][3]==true){
         $dl = $download_path."?file=".urlencode($dl);
      }
      $ec['download']="href='".$dl."'";
    }else{
      $ec['download']=$ecr['result']['download'];
    }
    return $ec;
  }

  public function getPreview($result){
    $preview = "";
    if($result['resource_type']=="Audio"){
      $preview = "<audio controls><source src='".strip_tags(htmlspecialchars_decode($result['preview']))."' type='audio/mpeg'></source></audio>";
    }else if($result['resource_type']=="Video"){
      $preview = "<video controls><source src='".strip_tags(htmlspecialchars_decode($result['preview']))."'></video>";
    }
    return $preview;
  }

  public function replaceWithHighlight($result,$highlight){
    global $largetext;
    foreach($result as $key => $val){
      if(isset($highlight) && !empty($highlight)){
        //match highlighted fields except extracted text as we'll handle that field differently
        if(array_key_exists($key, $highlight) && !in_array($key,$largetext) && $key != "url"){
          $result[$key]=html_entity_decode($highlight[$key][0]);
        }
      }
    }
    return $result;
  }

  //--------------------+
  //    Thumb fields    |
  //--------------------+
  public function createThumbViewFields($view,$result,$highlight){
    $thums = array();
    foreach($view as $k => $v){
      if (array_key_exists(strtolower($v),array_change_key_case($result,CASE_LOWER))){
        if(isset($result[$v]) && $result[$v]!=""){
          if(strlen(strip_tags($result[$v])) > 250){
            $newres = create_excerpt($result[$v], 250);
            $thums[$k]="<p><b class='rslt-key' title='".$this->title_msg." ".$v.".' alt='".$v."'>".htmlspecialchars_decode(formatkey($v))." : </b>".html_entity_decode($newres)."</p>";
          }else{
            $rsdc = "<span>".html_entity_decode($result[$v])."</span>";
            $thums[$k]="<p><b class='rslt-key' title='".$this->title_msg." ".$v.".' alt='".$v."'>".htmlspecialchars_decode(formatkey($v))." : </b>".html_entity_decode($rsdc)."</p>";
          }
        }
      }
    }
    if($highlight){
        $matches=array();
        foreach($highlight as $hk => $hv){
          if(is_array($hv)){
            $hv = htmlspecialchars_decode(implode(",",$hv));
          }
          $matches[] = " <em><b>".formatkey($hk)."</b></em> ".$hv;
        }
        $thums[]="<p><b>Matches :</b>".implode(", ",$matches)."</p>";
    }
    
    return implode(" ",$thums);
  }

  //----------------------+
  //    Thumbnail View    |
  //----------------------+
  public function getThumbView($idx,$conf,$rslt,$r,$id,$highlight){
    global $elastic_config;
    $ec = $elastic_config[$idx]['result'];
    $thumbnail = $conf['thumb'];
    if(isset($rslt['preview']) && $rslt['preview']!= false){
      $thumbnail = $this->getPreview($rslt);
    }else if($thumbnail != "" && $thumbnail != false){
      $thumbnail = "<img src='".$conf['thumb']."'/>";
    }
    if($thumbnail != ""){
      $thumbnail = "<div class='thumbnail'>".$thumbnail."</div>";
    }
/*    if(isset($rslt[$ec['title']])){
      $title = $rslt[$ec['title']];
    }else{
      $title = "No Title";
    }*/
    $view = $this->createThumbViewFields($ec['view'],$rslt,$highlight);
    return "<li class='result'>".
             "<h2>".
               "<a href='#md".$r."' class='fv-title' id='fv-title-".$r."'>".$this->title."</a>".
             "</h2>".
             "<h3 style='color:".$conf['color']."'><span>".$conf['source']."</span></h3>".
             $thumbnail.$view.
             "<div class='thumb-actions'><a href='#md".htmlentities($r)."' class='fv' id='".htmlentities($r)."'>Full View</a>".
             "<a href='#' id='".$id."' class='save-search' title='".html_entity_decode($this->title)."'> + </a></div>".
           "</li>";
  }

  //-----------------+
  //    Full View    |
  //-----------------+
  public function getFullView($result,$r,$result_config,$id,$highlight){
//    global $title;
    $pagination = $this->fullViewPagination($r);
    $sidebar = $this->fullViewSidebar($result,$result_config,$id);
    $result_title = "<h2>".htmlspecialchars_decode($this->title,ENT_QUOTES)."</h2>";
    $sub_title="<h3 style='color: ".$result_config['color']."'>".$result_config['source']."</h3>";
    $main = $this->fullViewMain($result,$result_config,$id,$highlight);

    return "<div class='more-data' id='md".$r."'><div class='md-wrap'>".$sidebar.$pagination.$result_title.$sub_title.$main."</div></div>";
  }

  /////
  public function fullViewPagination($r){
    global $totalhits;
    $prevmd = $r-1; $nxtmd = $r+1; if($prevmd == -1){$prevmd = "";}if($nxtmd == 26){$nxtmd="";}
    return "<div class='fv-nav'><a class='prev-md' href='#md".$prevmd."'>Prev</a> ".
           ($r+1)." of ".$totalhits.
           " <a class='next-md' href='#md".$nxtmd."'>Next</a>".
           "<a href='#' class='close-md'>Close</a></div>";
  }

  /////
  public function fullViewSidebar($result,$result_conf,$id){
    $thumbnail = $result_conf['thumb'];
      $prv = false;
    if(isset($result['preview']) && $result['preview']!= false){
      $prv = true;
      $thumbnail = $this->getPreview($result);
    }else if($thumbnail != "" && $thumbnail != false){
      $thumbnail = "<img src='".$result_conf['thumb']."'/>";
    }
    $actions = $this->determineActions($result_conf,$id,$prv);
    return "<div class='fv-sbar'><div class='img-wrap'>".$thumbnail."</div>".$actions."</div>";
  }

  ////
  public function determineActions($rconf,$id,$prv){
    $actions = array();
    $actionview = "";
    $action_defaults="<li><a id='".$id."' title='".htmlentities($this->title)."' style='border: 1px solid ".$rconf['color']."' class='fv-save-search'>Save</a></li>";
    $print_options = array();
    $print_options[] = "<li class='print-txt'><a>Print Text</a></li>";
    if($rconf['thumb']!=false && $prv === false){
      $print_options[] = "<li class='print-img'><a>Print Image</a></li>";
      $print_options[] = "<li class='print-imgtxt'><a>Image + Text</a></li>";
    }
    $print_options = implode(' ',$print_options);
    $print_options = "<li><a class='rslt-print' style='border: 1px solid ".$rconf['color']."'>".
    "Print <span>&#x25BC;</span></a><ul style='border: 1px solid ".$rconf['color']."'class='print-options'>".$print_options."</ul></li>";
    foreach($rconf as $k => $v){
      if($k == "download" && !empty($v) && $v != false	){
        $actions[] = "<li><a style='border: 1px solid ".$rconf['color']."' ".$rconf[$k].">Download</a></li>";
      }
      if($k == "url" && !empty($k)){
        $actions[] = "<li><a style='border: 1px solid ".$rconf['color']."' target='_BLANK' href='".$rconf[$k]."'>View Source</a></li>";
      }
      $actionview = implode("",$actions).$action_defaults.$print_options;
    }
    return "<ul class='fv-actions'>".$actionview."</ul>";
  }

  ////
  public function fullViewMain($result,$result_config,$id,$highlight){
    global $largetext, $layout, $array_fields;
    $sectionmain=array();
    $sections=array(
      "top"=>array(),
      "media"=>array(),
      "object"=>array(),
      "meta"=>array(),
      "rights"=>array()
    );
    foreach($result as $k => $v){
      if(!empty($v) && $v!=" "){

        if(is_array($v)){
          $v = json_encode($v);
        }

        if(in_array($k,$array_fields)){
          $nv = explode(",",$v);
          $v = "";
          $v .= "<ul>";
          for($x = 0; $x < count($nv); $x++){
            $v .= "<li>".$nv[$x]."</li>";
          }
          $v .= "</ul>";
        }
        
        if(isset($layout["main"][$result_config['index']]) && in_array($k,$layout["main"][$result_config['index']])){
          $key = array_search($k,$layout["main"][$result_config['index']]);
          if(in_array($k,$largetext)){
            if(strlen($v) > 2000){
              $sections['top'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5>".stripslashes(pagedText($v,$id,$k,$highlight,2000))."</li>";
            }else if($highlight != false){
              $sections['top'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5>".stripslashes(replaceHighlight($v,$highlight,$k,2000,false))."</li>";
            }else{
              $sections['top'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
            }
          }else{
              $sections['top'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
          }
        }

        if(isset($layout["media"][$result_config['index']]) && in_array($k,$layout["media"][$result_config['index']])){
          $key = array_search($k,$layout["media"][$result_config['index']]);
          $sections['media'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
        }
        if(isset($layout["object"][$result_config['index']]) && in_array($k,$layout["object"][$result_config['index']])){
          $key = array_search($k,$layout["object"][$result_config['index']]);
          $sections['object'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
        }
        if(isset($layout["meta"][$result_config['index']]) && in_array($k,$layout["meta"][$result_config['index']])){
          $key = array_search($k,$layout["meta"][$result_config['index']]);
          $nv = "";
          if($k == "Keywords" || $k == "tags"){
            $v = explode(",",$v);
            foreach($v as $kv => $vv){
              $nv .= "<em class='kw'>".$vv."</em>";
            }
            $v = $nv;
          }
          if($k =="url"){
            $v = "<a href='http://localhost".$v."' target='_BLANK'>".$v."</a>";
          }
          $sections['meta'][$key] = "<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
        }
        if(isset($layout["rights"][$result_config['index']]) && in_array($k,$layout["rights"][$result_config['index']])){
          $key = array_search($k,$layout["rights"][$result_config['index']]);
          $sections['rights'][$key]="<li><h5 class='rslt-key' title='".$this->title_msg." ".$k.".' alt='".$k."'>".formatkey($k)."</h5><span>".htmlentities($v)."</span></li>";
        }
      } //end if empty result
    }
    if(!empty($sections['top'])){ ksort($sections['top']); $sectionmain[] = "<ul style='border: 2px solid ".$result_config['color']."' class='fv-main'>".html_entity_decode(implode('',$sections['top']))."<div class='clear'></div></ul>";}
    if(!empty($sections['rights'])){ ksort($sections['rights']); $sectionmain[] = "<ul class='fv-rights'><h4>Rights</h4>".implode('',$sections['rights'])."<div class='clear'></div></ul>";}
    if(!empty($sections['media'])){ ksort($sections['media']); $sectionmain[] = "<ul class='fv-media'><h4>Media</h4>".implode('',$sections['media'])."<div class='clear'></div></ul>";}
    if(!empty($sections['object'])){ ksort($sections['object']); $sectionmain[] = "<ul class='fv-object'><h4>Art Object Data</h4>".html_entity_decode(implode('',$sections['object']))."<div class='clear'></div></ul>";}
    if(!empty($sections['meta'])){ ksort($sections['meta']); $sectionmain[] = "<ul class='fv-meta'><h4>Metadata</h4>".implode('',$sections['meta'])."<div class='clear'></div></ul>";}

    return "<section class='full-head'><ul>".html_entity_decode(implode('',$sectionmain))."</ul></section>";
  }

}//end class
?>
