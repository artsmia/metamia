<?php $results = new Results(); ?>
<div id="results">
<div class="grid-sizer"></div>
  <ul>
  <?php
  $totalhits = count($hits);
  for($r=0; $r<count($hits); $r++){
    $result = $hits[$r]['_source'];

    //  Config this result
    $result_config = $results->configResult($hits[$r]['_index'],$result,$hits[$r]['_type']);

    //if the result has highlighted fields returned then swap the value
    if(isset($hits[$r]['highlight'])){
      $highlight = $hits[$r]['highlight'];
      $result = $results->replaceWithHighlight($result,$highlight);
    }else{
      $highlight = false;
    }

    //  Get Thumbnail View
    echo $results->getThumbView($hits[$r]['_index'],$result_config,$result,$r,$hits[$r]['_id'],$highlight);

    //  Get Full View
    echo $results->getFullView($result,$r,$result_config,$hits[$r]['_id'],$highlight);
  }
  ?>
  </ul>
</div>
<ul class="rslt-actions">
  <li><a class="rslt-sv-page">Add these <?php echo $totalhits; ?> results</a></li>
  <li><a class="rslt-sv-query">Save entire query</a></li>
</ul>
