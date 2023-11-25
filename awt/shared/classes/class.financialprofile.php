<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class FinancialProfile extends Authorization{
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
            $response["message"] = "Please provide ID of the form you want the financial profile of.";
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
            $response = $this->InputNewFinancialProfile($data);
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
            $response = $this->UpdateFinancialProfile($data, intval($requestParameters[1]));
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
            $response = $this->deleteFinancialProfile($requestParameters[1]);
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
        $response["log"] = "no financial profile found for the form id provided";
        
        if(!v::numericVal()->positive()->validate($id)){
            $response["message"] = "Invalid form ID, Must be positive";
            return $response;
        }

        try {
            $sql = "SELECT * FROM applicantfinancialprofile WHERE Form_id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No financial profile associated with the provided form ID found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully retrieves financial profile associated with the provided form ID";
            $response["applications"] = $applications;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }
    public function InputNewFinancialProfile($data) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No financial profile found";

        if (!v::intType()->positive()->validate($data['Form_ID'])) {
            $response["message"] = "Invalid Form_ID. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['living_status'])) {
            $response["message"] = "Invalid living_status. Must be a non-empty string.";
            return $response;
        }
        
        if (!v::intType()->positive()->validate($data['at_currently_address_yrs'])) {
            $response["message"] = "Invalid estimate for living at current address (In Years). Must be a positive integer.";
            return $response;
        }

        if (!v::intType()->positive()->validate($data['at_currently_address_months'])) {
            $response["message"] = "Invalid estimate for living at current address (In months). Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['residents_in_home'])) {
            $response["message"] = "Invalid amount of residents in home. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['dependents'])) {
            $response["message"] = "Invalid number of dependents. Must be a positive integer.";
            return $response;
        }


        if (!v::stringType()->notEmpty()->validate($data['employment_status'])) {
            $response["message"] = "Invalid employment status. Must be a non-empty string.";
            return $response;
        }

        if (!v::stringType()->notEmpty()->validate($data['occupation'])) {
            $response["message"] = "Invalid occupation. Must be a non-empty string.";
            return $response;
        }

        if (!v::stringType()->notEmpty()->validate($data['source_of_income'])) {
            $response["message"] = "Invalid source of income. Must be a non-empty string.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['income_amount'])) {
            $response["message"] = "Invalid income amount. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['monthly_income'])) {
            $response["message"] = "Invalid monthly income. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['total_household_income_annually'])) {
            $response["message"] = "Invalid total house hold income amount. Must be a positive integer.";
            return $response;
        }
        try {
            $sql = "INSERT INTO applicantfinancialprofile(Form_ID, living_status, at_currently_address_yrs, at_currently_address_months, residents_in_home, dependents, employment_status, occupation, source_of_income, income_amount, monthly_income, total_household_income_annually)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['Form_ID'],
                $data['living_status'],
                $data['at_currently_address_yrs'],
                $data['at_currently_address_months'],
                $data['residents_in_home'],
                $data['dependents'],
                $data['employment_status'],
                $data['occupation'],
                $data['source_of_income'],
                $data['income_amount'],
                $data['monthly_income'],
                $data['total_household_income_annually']
            ]);
        
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted financial data into applicantfinancialprofile";
            } else {
                throw new Exception("Unable to input financial data into applicantfinancialprofile");
            }
        
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }

        return $response;
    }
    
    public function UpdateFinancialProfile($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No financial profile found";

        if (!v::intType()->validate($id)) {
            $response["message"] = "Invalid Form_ID test. Must be a positive integer.";
            return $response;
        }
    
        if (!v::stringType()->notEmpty()->validate($data['living_status'])) {
            $response["message"] = "Invalid living_status. Must be a non-empty string.";
            return $response;
        }
        
        if (!v::intType()->positive()->validate($data['at_currently_address_yrs'])) {
            $response["message"] = "Invalid estimate for living at current address (In Years). Must be a positive integer.";
            return $response;
        }

        if (!v::intType()->positive()->validate($data['at_currently_address_months'])) {
            $response["message"] = "Invalid estimate for living at current address (In months). Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['residents_in_home'])) {
            $response["message"] = "Invalid amount of residents in home. Must be a positive integer.";
            return $response;
        }
    

        if (!v::intType()->positive()->validate($data['dependents'])) {
            $response["message"] = "Invalid number of dependents. Must be a positive integer.";
            return $response;
        }


        if (!v::stringType()->notEmpty()->validate($data['employment_status'])) {
            $response["message"] = "Invalid employment status. Must be a non-empty string.";
            return $response;
        }

        if (!v::stringType()->notEmpty()->validate($data['occupation'])) {
            $response["message"] = "Invalid occupation. Must be a non-empty string.";
            return $response;
        }

        if (!v::stringType()->notEmpty()->validate($data['source_of_income'])) {
            $response["message"] = "Invalid source of income. Must be a non-empty string.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['income_amount'])) {
            $response["message"] = "Invalid income amount. Must be a positive integer.";
            return $response;
        }
    
        if (!v::intType()->positive()->validate($data['monthly_income'])) {
            $response["message"] = "Invalid monthly income. Must be a positive integer.";
            return $response;
        }
        if (!v::intType()->positive()->validate($data['total_household_income_annually'])) {
            $response["message"] = "Invalid total house hold income amount. Must be a positive integer.";
            return $response;
        }
        try {
            $sql = "UPDATE applicantfinancialprofile 
            SET living_status = ?, 
                at_currently_address_yrs = ?, 
                at_currently_address_months = ?, 
                residents_in_home = ?, 
                dependents = ?, 
                employment_status = ?, 
                occupation = ?, 
                source_of_income = ?, 
                income_amount = ?, 
                monthly_income = ?, 
                total_household_income_annually = ?
            WHERE Form_ID = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([
                $data['living_status'],
                $data['at_currently_address_yrs'],
                $data['at_currently_address_months'],
                $data['residents_in_home'],
                $data['dependents'],
                $data['employment_status'],
                $data['occupation'],
                $data['source_of_income'],
                $data['income_amount'],
                $data['monthly_income'],
                $data['total_household_income_annually'],
                $id
            ]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully updated financial data into applicantfinancialprofile";
            } else {
                throw new Exception("Unable to update financial data into applicantfinancialprofile");
            }
        
            $stmt->close();
            $stmt = null;
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }

        return $response;
    }
    
    public function deleteFinancialProfile($id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to delete studycost.";
    
        try {
            // SQL statement to delete a user from the applications table where id matches
            $sql = "DELETE FROM applicantfinancialprofile WHERE form_id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted financial profile with ID: " . $id;
            } else {
                throw new Exception("No financial profile found with ID: " . $id);
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
