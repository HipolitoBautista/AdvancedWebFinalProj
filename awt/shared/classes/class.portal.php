<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class Portal extends Authorization{
    private $requestArray;
    private $parameters;
    private $log;
    private $db;
    private $permissions;


    function __construct($helpers, $requestArray, $parameters){
        parent::__construct();

        $this->requestArray = $requestArray;
        $this->parameters = $parameters;
        $this->log = $helpers->log;
        $this->db = $helpers->db;
        $this->permissions = $helpers->permissions;

    }

    function __destruct(){

    }

    public function GET($requestParameters){
    
        $resource = get_class($this);
        $method = __FUNCTION__;

        $response["rc"] = -1;
        $response["message"] = "Invalid login request";


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        

        if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        $response = !isset($requestParameters[1]) ?  $response["message"] = "Invalid login request, no username" : $this->handleLogin($request); 

        return $response;
    }
    public function handleLogin($username){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Invalid login request";
        
        try {
            $sql = "SELECT * FROM applicant WHERE username = '$username'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applicant records found.");
            } else if ($result -> num_rows < 1) {
                throw new Exception("More than 2 persons with the same username.");
            };
    
            $applicants = array();
            while ($row = $result->fetch_assoc()) {
                $applicants[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            // Fetch the raw POST data
            $rawData = file_get_contents("php://input");
        

            // Decode the JSON data into a PHP array
            $data = json_decode($rawData, true);
            
            $passwords = '';
            $hashedPassword = ''; 
            foreach($applicants as $applicants){
                if(isset($applicants["password"])){
                    $passwords = $applicants["password"];
                    break;
                }
            }

            if (password_verify($data["password"], $passwords)) {
                $response["rc"] = "1";
                $response["log"] = "Successfully verified user"; 
                $response["id"] = $applicants["id"];   
            } else {           
                $response["log"] = "Wrong Password";   
            }
            
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

}