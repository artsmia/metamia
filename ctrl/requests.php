<?php
header("Content-Type: text/html; charset=UTF-8");
class Req{
    function handle_request($method,$request){
        switch ($method) {
            case 'PUT':
                request_put($request);
            break;
            case 'POST':
               return $this->request_get($request);
            break;
            case 'GET':
                return $this->request_get($request);
            break;
            case 'DELETE':
                request_delete($request);
            break;
            default:
                return "error";
//                request_error($request);
            break;
        }
    }
    //$_GET
    function request_get($request){
        $compiler = array();
        foreach($request as $requestkey => $requestval){
                $allowed_types = array("boolean","integer","double","string","array","object");
                //$nonallowed_types = array("unkown type","resource");
                $type = gettype($requestval);
                if(!in_array($type,$allowed_types)){
                       //more to determine type
                }
                if(in_array($type,$allowed_types)){
                    $sanitized = $this->sanitize($requestval,$type);
                    //recompile request
                    $compiler[$requestkey] = $sanitized;
                }
        }
        return $compiler;
    }

    function sanitize($requestval,$type){
         switch($type){
             case "boolean":
                 return (bool)$requestval;
             break;
             case "string":
                 $requestval = str_ireplace(array("\r", "\n", "%0a", "%0d"), '', stripslashes(trim($requestval)));
                // $requestval = filter_var($requestval,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                 $requestval = filter_var($requestval,FILTER_SANITIZE_STRING);
                /* if(is_numeric(trim($requestval,'"'))){
                     return (int)$requestval;
                 }
                 if(filter_var($requestval,FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE) != NULL){
                     return (bool)$requestval;
                 }*/
                 return $requestval;
             break;
             case "email":
                 $requestval = filter_var($val, FILTER_SANITIZE_EMAIL);
                 return $requestval;
             break;
             case "url":
                 $requestval = filter_var(FILTER_SANITIZE_URL);
                 return $requestval;
             break;
             case "array":
                 //iterate back through
                 $requestval = $this->request_get($requestval);
                 return $requestval;
             break;
             case "object":
                 //turn object to array and re-iterate
                 $requestval = $this->request_get(get_object_var($requestval));
                 return $requestval;
             break;
             default:
                 return "bad value";
             break;
         }
    }
    function validate($val,$type){
        switch($type){
            case "boolean":
                return filter_var($val,FILTER_VALIDATE_BOOLEAN);
            break;
            case "integer":
                return filter_var($val,FILTER_VALIDATE_INT);
            break;
            case "url":
                return filter_var($val,FILTER_VALIDATE_URL);
            break;
            case "email":
                return fitler_var($val,FILTER_VALIDATE_EMAIL);
            break;
        }
    }
}
?>
