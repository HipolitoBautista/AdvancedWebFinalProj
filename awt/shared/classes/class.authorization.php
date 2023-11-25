<?php

require_once 'class.applicants.php';
require_once 'class.request.php';
require_once 'class.financialprofile.php';
require_once 'class.references.php';
require_once 'class.applicantcontactinfo.php';
require_once 'class.institution.php';
require_once 'class.pastacademicdetails.php';
require_once 'class.futureacademicdetails.php';
require_once 'class.applicantverification.php';
require_once 'class.portal.php';
require_once 'inc/composer/vendor/autoload.php';

class Authorization{
    function __construct() {
    }

    function __destruct() {

    }

    public function checkPermission($log, $permissions, $resource, $subResource, $method){
    
        if(!array_key_exists($resource, $permissions)) {
            $log->error(__METHOD__ . "parent resource [$resource] not exists in permission list"); 
            return false;
        }
        
        $list = $permissions[$resource];
        $subResource = $subResource == -1 ? "empty" : "/";
        $log->debug(__METHOD__. "subResource [$subResource] method [$method]");

        foreach($list as $varList){ 
            if($subResource == $varList["sub-resource"] && $method == $varList["method"]){
            
                return true;
            } else if ($subResource == "empty" && $method == $varList["method"]) {
                return true;
            }else if($method == "PUT" || $method == "DELETE" && $method == $varList["method"] && $varList["sub-resource"] != "/"){
                return true;
            }
        }

        $log->error(__METHOD__ . "subResource sub resource no permission granted");

        return false;
    }
}
?>