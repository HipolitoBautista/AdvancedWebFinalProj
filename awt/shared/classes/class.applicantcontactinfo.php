<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class ApplicantContactInfo extends Authorization{
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

        $response = $this->getSingleContactInfo($request); 

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
            $response = $this->InputContactInfo($data);
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
            $response = $this->UpdateContactInfo($data, intval($requestParameters[1]));
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
            $response = $this->deleteContactInfo($requestParameters[1]);
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the contact info you want deleted'
            ]);
        }

        
        return $response;
    }


    public function getSingleContactInfo($id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no reference found for the form id provided";
        
        if(!v::numericVal()->positive()->validate($id)){
            $response["message"] = "Invalid form ID, Must be positive";
            return $response;
        }

        try {
            $sql = "SELECT * FROM applicantcontactinfo WHERE Form_id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No reference associated with the provided form ID found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully retrieves reference associated with the provided form ID";
            $response["applications"] = $applications;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }
    public function InputContactInfo($data) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No reference found";

        if (!v::intType()->positive()->validate($data['Form_ID'])) {
            $response["message"] = "Invalid Form_ID. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['HomePhone'])) {
            $response["message"] = "Invalid Home Phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['WorkPhone'])) {
            $response["message"] = "Invalid work phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['CellPhone'])) {
            $response["message"] = "Invalid cell phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['Other'])) {
            $response["message"] = "Invalid phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['Email'])) {
            $response["message"] = "Invalid email. Must be a non-empty string.";
            return $response;
        }

        

        try {
            $sql = "INSERT INTO applicantcontactinfo (Form_ID, HomePhone, WorkPhone, CellPhone, Other, Email)
            VALUES (?, ?, ?, ?, ?, ?)";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['Form_ID'],
                $data['HomePhone'],
                $data['WorkPhone'],
                $data['CellPhone'],
                $data['Other'],
                $data['Email']
            ]);
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted data into contact table";
            } else {
                throw new Exception("Unable to input contact info into contact table");
            }
        
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }

        return $response;
    }
    
    public function UpdateContactInfo($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No reference found";

        if (!v::intType()->positive()->validate($id)) {
            $response["message"] = "Invalid Form_ID. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['HomePhone'])) {
            $response["message"] = "Invalid Home Phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['WorkPhone'])) {
            $response["message"] = "Invalid work phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['CellPhone'])) {
            $response["message"] = "Invalid cell phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['Other'])) {
            $response["message"] = "Invalid phone number. Must be a non-empty string.";
            return $response;
        }
        if (!v::stringType()->notEmpty()->validate($data['Email'])) {
            $response["message"] = "Invalid email. Must be a non-empty string.";
            return $response;
        }

        

        try {
            $sql = "UPDATE applicantcontactinfo 
            SET HomePhone = ?, WorkPhone = ?, CellPhone = ?, Other = ?, Email = ?
            WHERE Form_ID = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['HomePhone'],
                $data['WorkPhone'],
                $data['CellPhone'],
                $data['Other'],
                $data['Email'],
                $id
            ]);
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully updated data into contact table";
            } else {
                throw new Exception("Unable to update contact info into contact table");
            }
        
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }

        return $response;
    }
    
    public function deleteContactInfo($id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to delete contact info.";
    
        try {
            // SQL statement to delete a user from the applications table where id matches
            $sql = "DELETE FROM applicantcontactinfo WHERE form_id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted contact info with ID: " . $id;
            } else {
                throw new Exception("No contact info found with ID: " . $id);
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
