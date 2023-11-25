<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class Institution extends Authorization{
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
        $response["message"] = "Invalid reference Request";


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        if($request == -1){
            $response["message"] = "Please provide ID of the form you want the reference of.";
            return $response;    
        }else if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        $response = $this->getSingleInstitution($request); 

        return $response;
    }

    public function POST($requestParameters){

        // Fetch the raw POST data
        $rawData = file_get_contents("php://input");

        // Decode the JSON data into a PHP array
        $data = json_decode($rawData, true);
        $response["message"] = "";
    

        $resource = strtolower(get_class($this));
        $method = __FUNCTION__;

        $request = isset($requestParameters[1]) ? $requestParameters[1] : "";
        if(!$this->checkPermission($this->log, $this->permissions, $resource, $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        // Check if the data exists
        if ($data) {
            $response = $this->InputInstitution($data);
        } else {
            // no data found
            $response = ([
                'status' => 'error',
                'message' => 'No data provided.'
            ]);
        }

        return $response;
    }

    public function PUT($requestParameters){
        
        $response["rc"] = -1;
        $response["message"] = "Invalid Student Request";
 
        $resource = strtolower(get_class($this));
        $method = __FUNCTION__;


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        if($request == -1){
            $response["message"] = "Please provide ID of the form you want to update the study cost of.";
            return $response;    
        }else if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }
        if($requestParameters[1]) { 
        // Fetch the raw POST data
        $rawData = file_get_contents("php://input");
        
        // Decode the JSON data into a PHP array
        $data = json_decode($rawData, true);
        $response = "";
        
        // Check if the data exists
        if ($data) {
            $response = $this->UpdateInstitution($data, intval($requestParameters[1]));
        } else {
            // no data found
            $response = ([
                'status' => 'error',
                'message' => 'No data provided to update.'
            ]);
        }
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the user you want updated'
            ]);
        }

        
        return $response;
    }
    
    public function DELETE($requestParameters){
        
        $response["rc"] = -1;
        $response["message"] = "User does not exist";

        $resource = strtolower(get_class($this));
        $method = __FUNCTION__;

        $request = isset($requestParameters[1]) ? $requestParameters[1] : "";

        if(!$this->checkPermission($this->log, $this->permissions, $resource, $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        if($requestParameters[1]) { 
            $response = $this->deleteInstitution($requestParameters[1]);
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the contact info you want deleted'
            ]);
        }

        
        return $response;
    }


    public function getSingleInstitution($id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no institution found for the form id provided";
        
        if(!v::numericVal()->positive()->validate($id)){
            $response["message"] = "Invalid form ID, Must be positive";
            return $response;
        }

        try {
            $sql = "SELECT * FROM institution WHERE Form_id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No institution associated with the provided form ID found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully retrieves institution associated with the provided form ID";
            $response["applications"] = $applications;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }
    public function InputInstitution($data) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No institution found";

        if (!v::intType()->positive()->validate($data['Form_ID'])) {
            $response["message"] = "Invalid Form_ID. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['Address'])) {
            $response["message"] = "Invalid Address. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['District'])) {
            $response["message"] = "Invalid District. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['City_Town_Village'])) {
            $response["message"] = "Invalid cell City / Town / Village. Must be a non-empty string.";
            return $response;
        }

        try {
            $sql = "INSERT INTO institution (Form_ID, Address, District, City_Town_Village)
            VALUES (?, ?, ?, ?)";
    
        $stmt = $this->db->prepare($sql);
        $stmt_status = $stmt->execute([
            $data['Form_ID'],
            $data['Address'],
            $data['District'],
            $data['City_Town_Village']
        ]);
        if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inpputed institution data into institution table";
            } else {
                throw new Exception("Unable to input institution into institution table");
            }
        
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }

        return $response;
    }
    
    public function UpdateInstitution($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No institution found";

        if (!v::intType()->positive()->validate($id)) {
            $response["message"] = "Invalid Form_ID. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['Address'])) {
            $response["message"] = "Invalid Address. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['District'])) {
            $response["message"] = "Invalid District. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['City_Town_Village'])) {
            $response["message"] = "Invalid cell City / Town / Village. Must be a non-empty string.";
            return $response;
        }
    
    try {
        $sql = "UPDATE institution
        SET Address = ?, District = ?, City_Town_Village = ?
        WHERE Form_ID = ?";

        $stmt = $this->db->prepare($sql);
        $stmt_status = $stmt->execute([
            $data['Address'],
            $data['District'],
            $data['City_Town_Village'],
            $id
        ]);
        if ($stmt_status) {
            $response["rc"] = "1";
            $response["log"] = "Successfully updated institution data into institution table";
        } else {
            throw new Exception("Unable to input institution into institution table");
        }
    
        $stmt->close();
        $stmt = null;
    }catch (Exception $e) {
        $this->log->error("[$e]");
        $response["log"] = $e->getMessage();
    }

    return $response;
    }
    
    public function deleteInstitution($id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to delete institution.";
    
        try {
            // SQL statement to delete a user from the applications table where id matches
            $sql = "DELETE FROM institution WHERE form_id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted institution with ID: " . $id;
            } else {
                throw new Exception("No institution found with ID: " . $id);
            }
    
            $stmt->close();
            $stmt = null;
    
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
    
        return $response;
    }
    
}

?>
