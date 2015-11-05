<?php
include_once(__DIR__."/../include/config.php");
include_once(__DIR__."/../model/elastic_model.php");
include_once(__DIR__."/../model/redis.php");
class Search{
    private $elastic_model;
    private $redMod;
    public function __construct(){
        $this->redMod = new RedisModel();
        $this->elastic_model = new elastic_model();
    }
    //execute search query
    public function sendQuery($queryData){
        $queryData = json_encode($queryData);
        $response = $this->elastic_model->executeSearchQuery($queryData);
        if($response){
            return $response;
        }else{
            return false;
        }
    }
    //get mappings
    public function getMappings(){
        $query = $this->elastic_model->getMappings();
        if($query == false){
            return false;
        }else{
           return $query;
        }
    }
    public function setOriginalFilters(){
    $orig_filters = array();
    if(isset($_GET['type'])){
        foreach($_GET['type'] as $k => $v){
            $orig_filters[] = array("name" => "type[]", "value"=>$v);
        }
    }
    if(isset($_GET['filter'])){
        foreach ($_GET['filter'] as $fk => $fv){
            for($i= 0; $i<count($fv); $i++){
            $orig_filters[] = array("name" => "filter[".$fk."][]", "value"=>$fv[$i]);
            }
        }
    }
    if(isset($_GET['filterno'])){
        foreach ($_GET['filterno'] as $fk => $fv){
            for($i= 0; $i<count($fv); $i++){
            $orig_filters[] = array("name" => "filterno[".$fk."][]", "value"=>$fv[$i]);
            }
        }
    }
    if(isset($_GET['filteror'])){
        foreach ($_GET['filteror'] as $fk => $fv){
            for($i= 0; $i<count($fv); $i++){
            $orig_filters[] = array("name" => "filteror[".$fk."][]", "value"=>$fv[$i]);
            }
        }
    }
    if(isset($_GET['qaf'])){
        foreach ($_GET['qaf'] as $fk => $fv){
            $orig_filters[] = array("name" => "qaf[".$fk."]", "value"=>$fv);
        }
    }
    return $orig_filters;
    }

    public function compileInput($k,$v,$t){
           $operators = array("=",">","<");
           $frstopname = array("&"=>"","!"=>"no","or"=>"or");
           $rmvbtn = "<a class='rm-val' href='#'>[x]</a>";
           $rngbtn = "";
           $inptitle = $k;
           if(strpos($k,":op=")){
               $op = $operators[substr($k,strpos($k,":op=")+4)];
               $kt = explode(":op=",$k);
               $inptitle = $kt[0];
               $rngbtn = "<a class='rng'>".$op."</a>";
           }
           if($t != false){
           $fname = "filter".$frstopname[$t]."[".$k."][]";
           $opbtn = "<a class='ornot' alt='".$inptitle."'>".$t."</a>".$rngbtn;
           }else{
            $opbtn = "";
            $fname = "qaf[".$k."]";
           }
           $formattedtitle = formatkey($inptitle);
           return "<label for='".$k."'>$formattedtitle:<br/>".$opbtn.
                   "<input type='text' name='".$fname."' value='$v' class='filter'/>".$rmvbtn.
                   "</label>";
    }

    public function setFilterInputs(){
        $filterstodisplay = array();
        $rmvbtn = "<a class='rm-val' href='#'>[x]</a>";
            if(isset($_GET['filter'])){
                foreach($_GET['filter'] as $k => $v){for($i=0; $i<count($v); $i++){$filterstodisplay[] = $this->compileInput($k,$v[$i],"&");}}
            }
            if(isset($_GET['filterno'])){
               foreach($_GET['filterno'] as $k => $v){for($i=0; $i<count($v); $i++){$filterstodisplay[] = $this->compileInput($k,$v[$i],"!");}}
            }
            if(isset($_GET['filteror'])){
               foreach($_GET['filteror'] as $k => $v){for($i=0; $i<count($v); $i++){$filterstodisplay[] = $this->compileInput($k,$v[$i],"or");}}
            }
            if(isset($_GET['qaf'])){
               foreach($_GET['qaf'] as $k => $v){$filterstodisplay[] = $this->compileInput($k,$v,false);}
            }
        return implode('',$filterstodisplay);
    }
    public function recallSavedSearch(){
      global $redMod;
      $sid = explode(":",$_GET['svsrch']);
      //recall the search
      $rcsearch = $this->redMod->getSavedSearch($sid[0],$sid[1]);
      if($rcsearch){
        //format the return
        $idray=array();
        foreach($rcsearch as $k => $v){
            $idray[]=$k;
        }
        return $idray;
      }else{
        return false;
      }
    }
    public function mailSavedSearch($link,$to,$from,$uid){
        global $base_url;
        //validate
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $data['error']=true;
            $data['msg']="Sorry ".$to." is not a valid email address";
            return json_encode($data);
        }
        //attempt to send
        $subject = "MetaMia: You've been sent a search.";
        $message = $from." has sent you a search from MetaMia. <br/>This search can be access via: <a href='".$link."'>".$link."</a>";
        $headers = "From: ".$uid."@yoursite.org" . "\r\n" .
        "Content-type: text/html; charset=iso-8859-1" . "\r\n" .
        "Reply-To: ".$uid."@yoursite.org" . "\r\n" .
        "X-Mailer: PHP/" . phpversion();
        if(mail($to,$subject,$message,$headers)){
            $data['error']=false;
            $data['msg']="Email was successfully sent";
            return json_encode($data);
        }else{
            $data['error']=true;
            $data['msg']="Failed sending email.";
            return json_encode($data);
        };
    }

}
?>

