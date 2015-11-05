<?php
class model_ldap{
  function ldappass($username,$pass){
    global $ldap;
    $user = "mia-ad\\".$username;
    $ldapconn = ldap_connect($ldap['server'],$ldap['port']) or die("Could not connect to LDAP server.");
    if ($ldapconn) {
      if(ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3)){
        if(ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0)){
          if(ldap_start_tls($ldapconn)){
            $ldapbind = @ldap_bind($ldapconn,$user,$pass);
          }else{
            return "cannot connect";
          }
        }
      }
      if($ldapbind){
        $search_filter = "(".$ldap['field']."=".$username.")";
        $find = ldap_search($ldapconn,$ldap['basedn'],$search_filter);
        if($find){
          $result = ldap_get_entries($ldapconn, $find);
          $userfullname = $result[0]['cn'][0];
          $ldapresult['fullname'] = $result[0]['cn'][0];
          $ldapresult['username'] = $result[0]['samaccountname'][0];
           if(isset($result[0]['memberof'])){
             foreach($result[0]['memberof'] as $k => $v){
               if($v == "CN=MetaMia-Admin,OU=Groups,DC=mia-ad,DC=org"){
                 $ldapresult['access'] = "admin";
               };
             }
           }
          return $ldapresult;
        }else{
          return true;
        }
      }else{
        return false;
      }
    }
  }
}
?>
