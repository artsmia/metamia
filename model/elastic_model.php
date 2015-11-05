<?php

class elastic_model{
    public function getMappings(){
      global $elastic_url, $allowed_indexes;
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $elastic_url.$allowed_indexes."/_mapping");
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
      $items = curl_exec($curl);
      if($items != true){
          return false;
      }else{
          return $items;
      }
    }
    public function executeSearchQuery($queryData){
        global $req, $elastic_url, $elastic_index, $elastic_type;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $elastic_url."$elastic_index/$elastic_type/_search");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $queryData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($queryData))
        );
        return curl_exec($curl);
    }
}
?>
