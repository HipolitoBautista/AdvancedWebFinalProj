<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class StudyCost extends Authorization{
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
        $response["message"] = "Invalid studycost Request";


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        if($request == -1){
            $response["message"] = "Please provide ID of the form you want the study cost of.";
            return $response;    
        }else if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        $response = $this->getSingleStudyCost($request); 

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
            $response = $this->InputNewStudyCosts($data);
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
            $response = $this->UpdateStudyCosts($data, $requestParameters[1]);
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
            $response = $this->deleteUser($requestParameters[1]);
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the user you want deleted'
            ]);
        }

        
        return $response;
    }


    public function getSingleStudyCost($id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no studycost found for the form id provided";
        
        if(!v::numericVal()->positive()->validate($id)){
            $response["message"] = "Invalid form ID, Must be positive";
            return $response;
        }

        try {
            $sql = "SELECT * FROM studycost WHERE Form_id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No studycosts associated with the provided form ID found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully retrieves studycost associated with the provided form ID";
            $response["applications"] = $applications;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }
    public function InputNewStudyCosts($data) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No application data found";
        if (!v::numericVal()->validate($data['Form_ID'])) {
            $response["message"] = "Invalid Form ID. Must be a numeric value.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['P_Tuition_SchoolFee'])) {
            $response["message"] = "Invalid P_Tuition_SchoolFee. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['P_Books_Supplies'])) {
            $response["message"] = "Invalid P_Books_Supplies. Must be a positive integer.";
            return $response;
        }

    
        if (!v::intType()->positive()->validate($data['P_Boarding_Lodging'])) {
            $response["message"] = "Invalid P_Boarding_Lodging. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['P_Traveling'])) {
            $response["message"] = "Invalid P_Traveling. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['P_Expenses'])) {
            $response["message"] = "Invalid P_Expenses. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['P_total'])) {
            $response["message"] = "Invalid P_total. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['Tuition_SchoolFee'])) {
            $response["message"] = "Invalid Tuition_SchoolFee. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['Books_Supplies'])) {
            $response["message"] = "Invalid Books_Supplies. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['Boarding_Lodging'])) {
            $response["message"] = "Invalid Boarding_Lodging. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['Traveling'])) {
            $response["message"] = "Invalid Traveling. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['Expenses'])) {
            $response["message"] = "Invalid Expenses. Must be a positive integer.";
            return $response;
        }
    
         if (!v::intType()->positive()->validate($data['total'])) {
            $response["message"] = "Invalid total. Must be a positive integer.";
            return $response;
        }
    
        try {
            $sql = "INSERT INTO studycost (Form_ID, P_Tuition_SchoolFee, P_Books_Supplies, P_Boarding_Lodging, P_Traveling, P_Expenses, P_total, Tuition_SchoolFee, Books_Supplies, Boarding_Lodging, Traveling, Expenses, total)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['Form_ID'],
                $data['P_Tuition_SchoolFee'],
                $data['P_Books_Supplies'],
                $data['P_Boarding_Lodging'],
                $data['P_Traveling'],
                $data['P_Expenses'],
                $data['P_total'],
                $data['Tuition_SchoolFee'],
                $data['Books_Supplies'],
                $data['Boarding_Lodging'],
                $data['Traveling'],
                $data['Expenses'],
                $data['total']
            ]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted study costs ";
            } else {
                throw new Exception("Unable to input");
            }
    
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
    
        return $response;
    }
    
    public function UpdateStudyCosts($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No studycost data found";

        if (!v::numericVal()->validate($id)) {
            $response["message"] = "Invalid Form ID. Must be a numeric value.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['P_Tuition_SchoolFee'])) {
            $response["message"] = "Invalid P_Tuition_SchoolFee. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['P_Books_Supplies'])) {
            $response["message"] = "Invalid P_Books_Supplies. Must be a positive integer.";
            return $response;
        }

    
        if (!v::intType()->positive()->validate($data['P_Boarding_Lodging'])) {
            $response["message"] = "Invalid P_Boarding_Lodging. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['P_Traveling'])) {
            $response["message"] = "Invalid P_Traveling. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['P_Expenses'])) {
            $response["message"] = "Invalid P_Expenses. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['P_total'])) {
            $response["message"] = "Invalid P_total. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['Tuition_SchoolFee'])) {
            $response["message"] = "Invalid Tuition_SchoolFee. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['Books_Supplies'])) {
            $response["message"] = "Invalid Books_Supplies. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['Boarding_Lodging'])) {
            $response["message"] = "Invalid Boarding_Lodging. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['Traveling'])) {
            $response["message"] = "Invalid Traveling. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['Expenses'])) {
            $response["message"] = "Invalid Expenses. Must be a positive integer.";
            return $response;
        }
    
         if (!v::intType()->positive()->validate($data['total'])) {
            $response["message"] = "Invalid total. Must be a positive integer.";
            return $response;
        }
    
        try {
            $sql = "UPDATE studycost SET 
            P_Tuition_SchoolFee = ?, 
            P_Books_Supplies = ?, 
            P_Boarding_Lodging = ?, 
            P_Traveling = ?, 
            P_Expenses = ?, 
            P_total = ?, 
            Tuition_SchoolFee = ?, 
            Books_Supplies = ?, 
            Boarding_Lodging = ?, 
            Traveling = ?, 
            Expenses = ?, 
            total = ? 
            WHERE Form_ID = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['P_Tuition_SchoolFee'],
                $data['P_Books_Supplies'],
                $data['P_Boarding_Lodging'],
                $data['P_Traveling'],
                $data['P_Expenses'],
                $data['P_total'],
                $data['Tuition_SchoolFee'],
                $data['Books_Supplies'],
                $data['Boarding_Lodging'],
                $data['Traveling'],
                $data['Expenses'],
                $data['total'],
                $id
            ]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully updated study costs";
            } else {
                throw new Exception("Unable to input");
            }
    
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
    
        return $response;
    }

    
    public function deleteUser($id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to delete studycost.";
    
        try {
            // SQL statement to delete a user from the applications table where id matches
            $sql = "DELETE FROM studycost WHERE form_id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted studycost with ID: " . $id;
            } else {
                throw new Exception("No study cost found with ID: " . $id);
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
