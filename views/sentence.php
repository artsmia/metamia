<?php
$sentence = array();
$sentence[]="Searching in:";
if(isset($index) && $index != "_all"){
  $index = explode(",",$index);
  $idx = array();
  foreach($index as $idxv){
     $idx[] = "<b style='color:".$elastic_config[$idxv]['color']."'>".$elastic_config[$idxv]['title']."</b>";
  }
  $sentence[] = join(' and ', array_filter(array_merge(array(join(', ', array_slice($idx, 0, -1))), array_slice($idx, -1))));
}else{
  $sentence[] = "<b>everything</b>";
}
if($searchterm!=""){
  $sentence[] = "for <b class='highlight''>".$searchterm."</b>";
}
//FILTERS
if(isset($_GET['type'])){
  foreach($_GET['type'] as $k => $v){$tps[]="<b>".$v."</b>";}
  $sentence[] = "where <em>type</em> is ".join(' or ', array_filter(array_merge(array(join(', ', array_slice($tps, 0, -1))), array_slice($tps, -1))));
}
if(isset($_GET['qaf'])){
  foreach($_GET['qaf'] as $qk => $qv){
    $qmtch[] = "<b>".formatkey($qk)."</b><em> matches </em><b class='highlight'>".$qv."</b>";
  }
  $sent[] = join(' and ', array_filter(array_merge(array(join(', ', array_slice($qmtch, 0, -1))), array_slice($qmtch, -1))));
}
if(isset($_GET['filter'])){
  foreach($_GET['filter'] as $fk => $fv){
    $fop="matches";
    if(strpos($fk,":op=")){
      $opid = substr($fk,-1);
      switch($opid){
        case 0:
          $fop = "matches";
        break;
        case 1:
          $fop = "is less than";
        break;
        case 2:
          $fop = "is greater than";
        break;
      }
      $fk = substr($fk,0,-5);
    }
    $mtch[] = "<b>".formatkey($fk)."</b><em> ".$fop."  </em><b class='highlight'>".implode(' ',$fv)."</b>";
  }
  $sent[] = join(' and ', array_filter(array_merge(array(join(', ', array_slice($mtch, 0, -1))), array_slice($mtch, -1))));
}
if(isset($_GET['filteror'])){
  foreach($_GET['filteror'] as $ok => $ov){
    $ormtch[] = "<b>".formatkey($ok)."</b><em> should match </em><b class='highlight'>".implode(' ',$ov)."</b>";
  }
  $sent[] = join(' and ', array_filter(array_merge(array(join(', ', array_slice($ormtch, 0, -1))), array_slice($ormtch, -1))));
}
if(isset($_GET['filterno'])){
  foreach($_GET['filterno'] as $nk => $nv){
    $nomtch[] = "<b>".formatkey($nk)."</b><em> must not match  </em><b class='highlight'>".implode(' ',$nv)."</b>";
  }
  $sent[] = join(' and ', array_filter(array_merge(array(join(', ', array_slice($nomtch, 0, -1))), array_slice($nomtch, -1))));
}
if(isset($sent) && !empty($sent)){
  $sentence[] = "and filtered by " . join(' and ', array_filter(array_merge(array(join(', ', array_slice($sent, 0, -1))), array_slice($sent, -1))));
}
//  $sentence[] = join(' </b>or<b> ', array_filter(array_merge(array(join(', ', array_slice($flt, 0, -1))), array_slice($flt, -1))))."</b>";
$endstr = "results";
if(isset($response['hits']['total'])){
  $rscount = $response['hits']['total'];
  if( $rscount == 1){
    $endstr = "result";
  }
}else{
  $rscount = 0;
}
$sentence[] = "there's <b style='color:#E47738'>".$rscount." </b>".$endstr.".";
echo implode(" ", $sentence);
?>
