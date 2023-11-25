<?php

class Request extends Handler {
    function __construct() {
        parent::__construct();
    }

    function __destruct() {

    }

    public function process($data, $permissions) {

        $clientRequest = $data["REQUEST_URI"];
        $clientRequestArray = explode("/", ltrim($clientRequest, "/"));
        $requestMethod = isset($data["REQUEST_METHOD"]) ? $data["REQUEST_METHOD"] : "GET";
        $parameters = file_get_contents('php://input');

        $resource = isset($clientRequestArray[0]) ? $clientRequestArray[0] : -1;

        $this->log->info(__METHOD__ . '--- request received --'.$resource.'--- parameters:' . json_encode($parameters));

        $service = null;

        $response["rc"] = -1;
        $response["message"] = "Invalid Request";

        $helpers = new stdClass();
        $helpers->log = $this->log;
        $helpers->db = $this->db;
        $helpers->permissions = $permissions; 
        
        switch ($resource) {
            case "applicants" : 
                $service = new Applicants($helpers, $clientRequestArray, $parameters);
                break;
            case "applications" : 
                $service = new Applications($helpers, $clientRequestArray, $parameters);
                break;
            case "studycost" : 
                $service = new StudyCost($helpers, $clientRequestArray, $parameters);
                break;
            case "financialprofile" : 
                $service = new FinancialProfile($helpers, $clientRequestArray, $parameters);
                break;
            case "reference" : 
                $service = new Reference($helpers, $clientRequestArray, $parameters);
                break;
            case "contactinfo" : 
                $service = new ApplicantContactInfo($helpers, $clientRequestArray, $parameters);
                break;
            case "institution" : 
                $service = new Institution($helpers, $clientRequestArray, $parameters);
                break;    
            case "pastacademicdetails" : 
                $service = new PastAcademicDetails($helpers, $clientRequestArray, $parameters);
                break;
            case "futureacademicdetails" : 
                $service = new FutureAcademicDetails($helpers, $clientRequestArray, $parameters);
                break;
            case "applicantverification" : 
                $service = new ApplicantVerification($helpers, $clientRequestArray, $parameters);
                break;
            case "portal" : 
                $service = new Portal($helpers, $clientRequestArray, $parameters);
                break;
            default: 
                $this->log->info(__METHOD__ . "unknown resource request...");
        }

        if ($service && in_array(strtoupper($requestMethod), ['PUT', 'POST', 'DELETE', 'GET']))  {
            $response = $service->$requestMethod($clientRequestArray);
        } else {
            $this->log->info(__METHOD__ . "unknown method sent...");
            $this->log->info($requestMethod . "unknown method sent2...");
            $response["message"] = "Method not allowed";
        }
        

        echo json_encode($response);
    }
    public function checkApiKey($data){
        $response["key_id"] = -1;
        $response["permissions"] = array();
        
        $key = isset($data["HTTP_API_KEY"]) ? $data["HTTP_API_KEY"] : NULL;

        if(!$key){
            $this->log->error(__METHOD__ . " api key not present in request");
            return $response;
        }
        
        $keyParts = explode("_", $key);
        if(count($keyParts) != 3){
            $this->log->error(__METHOD__ . " invalid api key format");
            return $response;
        }
        $secret = "toast";
        $calculatedChecksum = hash('sha256', $keyParts[1] . $secret);
        if($calculatedChecksum != $keyParts[2]){
            $this->log->error(__METHOD__ . " invalid api key checksum. calculated [$calculatedChecksum] provided [" . $keyParts[2] . "]");
            return $response;
        }
        
        $info = $this->apiKeyInfo($key);

        if($info["key_id"] == -1){
            $this->log->error(__METHOD__ . " api key not found in db. [$key]");
            return $response;
        }
        
        if(empty($info["permissions"])){
            $this->log->error(__METHOD__ . " api key has no permissions. checksum[" . $keyParts[2] . "]");
            return $response;
        }
        
        return $info;
    }

    private function apiKeyInfo($key){
        $response["key_id"] = -1;
        $response["permissions"] = array();

        try{
            $sql = "
                    select
                        ak.id,
                        pr.parent,
                        pr.resource,    
                        akp.method
                    from
                        applicant_key ak
                    LEFT JOIN applicant a on a.id=ak.owner_id   
                    LEFT JOIN applicant_key_permission akp on ak.id=akp.key_id and akp.status=1
                    LEFT JOIN permission pr on akp.permission_id = pr.id and pr.`status`=1
                    where ak.api_key=? and ak.status=1
                    ORDER BY 3 desc";
            $stmt = $this->db->prepare($sql);    
            $stmt->bind_param("s", $key);
            $stmt->execute();
    
            $result = $stmt->get_result();    
    
            if($result->num_rows < 1){
                throw new Exception("Information not found in apiKey [$key]");
            }
    
            while($row = $result->fetch_assoc()){    
                $response["key_id"] = $row["id"];
                if(strlen($row["parent"]) > 0){
                    unset($row["id"]);
                    $response["permissions"][$row["parent"]][] = array("sub-resource" => $row["resource"], "method" => $row["method"]);
                }

            }
    
            $stmt->close();
            $stmt = null;
        } catch(Exception $e) {
            $this->log->error("Error occurred: " . $e->getMessage());    
        }

        return $response;
    }
    


}

?>
