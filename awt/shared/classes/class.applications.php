<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class Applications extends Authorization{
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
        $response["message"] = "Invalid Application Request";


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
       
        if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        $response = !isset($requestParameters[1]) ? $this->getAllApplications() : $this->getSingleApplication($request, $requestParameters); 

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

        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        if(!$this->checkPermission($this->log, $this->permissions, $resource, $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        // Check if the data exists
        if ($data) {
            $response = $this->InputNewApplication($data);
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

        if(!$this->checkPermission($this->log, $this->permissions, $resource, $request, $method)){
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
            $response = $this->UpdateApplication($data, $requestParameters[1]);
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

        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1 ;

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
    
    public function getAllApplications(){

        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applications found";

        $rawData = file_get_contents("php://input");
        $params = json_decode($rawData, true);

        $orderRule = !empty($params) && isset($params["order"]) ? $params["order"] : array();
        $pagingRule = !empty($params) && isset($params["paging"]) ? $params["paging"] : array();
        
        
        $orderSql = !empty($orderRule) ? "ORDER BY " : "";
        foreach($orderRule as $k => $v) {
            $orderSql .= $k . " " . $v . ", ";
        }
        
        $orderSql = rtrim($orderSql, ", ");
        
        $this->log->debug("getAllApplications() orderSql: " . $orderSql);
        
        $pagingSql = "LIMIT 2";
        if(isset($pagingRule["start"]) && isset($pagingRule["end"])){
            $pagingSql = "LIMIT " . $pagingRule["start"] . ", " . $pagingRule["end"];
        }
        
        $this->log->debug("getAllApplications() pagingSql: " . $pagingSql);
        
        try {
            $sql = "SELECT * FROM applications $orderSql $pagingSql";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applications found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row; 
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Success";
            $response["applications"] = $applications; 

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function getSingleApplication($id, $request){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applications found";
        
        if(!v::numericVal()->positive()->validate($id)){
            $response["message"] = "Invalid Application ID, Must be positive";
            return $response;
        }

        try {
            $sql = "SELECT * FROM applications WHERE form_id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No application found.");
            }
    
            $applications = array();
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully retrieves application";
            $response["applications"] = $applications;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function InputNewApplication($data){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "No application data found";
        
        if (!v::numericVal()->validate($data['applicant_id'])) {
            $response["message"] = "Invalid applicant ID. Must be a numeric value.";
            return $response;
        }
        
        $applicantId = $data['applicant_id'];
        
        if (!v::numericVal()->validate($data['form_id'])) {
            $response["message"] = "Invalid Form ID. Must be a numeric value.";
            return $response;
        }
        
        $formId = $data['form_id'];
        
        if (!v::stringType()->notEmpty()->validate($data['name'])) {
            $response["message"] = "Invalid name. Must be a non-empty string.";
            return $response;
        }

        $name = $data['name'];
        
        if (!v::stringType()->notEmpty()->validate($data['address'])) {
            $response["message"] = "Invalid address. Must be a non-empty string.";
            return $response;
        }
        $address = $data['address'];
        if (!v::stringType()->notEmpty()->validate($data['area'])) {
            $response["message"] = "Invalid area. Must be a non-empty string.";
            return $response;
        }
        $area = $data['area'];
        
        if (!v::stringType()->notEmpty()->validate($data['district'])) {
            $response["message"] = "Invalid district. Must be a non-empty string.";
            return $response;
        }
        
        $district = $data['district'];

        if (!v::stringType()->notEmpty()->validate($data['country'])) {
            $response["message"] = "Invalid district. Must be a non-empty string.";
            return $response;
        }
        
        $country = $data['country'];

        
        if (!v::stringType()->notEmpty()->validate($data['city_town_village'])) {
            $response["message"] = "Invalid city/town/village. Must be a non-empty string.";
            return $response;
        }
        $cityTownVillage = $data['city_town_village'];
        
        if (!v::stringType()->notEmpty()->validate($data['date_of_birth'])) {
            $response["message"] = "Invalid date of birth. Must be a non-empty string.";
            return $response;
        }
        $dateOfBirth = $data['date_of_birth'];
        if (!v::intType()->positive()->validate($data['age'])) {
            $response["message"] = "Invalid age. Must be a positive integer.";
            return $response;
        }
        $age = $data['age'];
        if (!v::stringType()->notEmpty()->validate($data['nationality'])) {
            $response["message"] = "Invalid nationality. Must be a non-empty string.";
            return $response;
        }
        $nationality = $data['nationality'];
        if (!v::boolType()->validate($data['pep_status'])) {
            $response["message"] = "Invalid PEP status. Must be a boolean value.";
            return $response;
        }
        $pepStatus = $data['pep_status'];
        
        if (!v::date()->validate($data['start_date'])) {
            $response["message"] = "Invalid start date. Must be a valid date.";
            return $response;
        }
        $startDate = $data['start_date'];
        
        if (!v::date()->validate($data['end_date'])) {
            $response["message"] = "Invalid end date. Must be a valid date.";
            return $response;
        }
        $endDate = $data['end_date'];
        if (!v::numericVal()->validate($data['institution_id'])) {
            $response["message"] = "Invalid institution ID. Must be a numeric value.";
            return $response;
        }
        $institutionId = $data['institution_id'];
        
        try {
            $sql = "INSERT INTO applications (ApplicantID, Form_ID, name, address, area, country, district, city_town_village, DateOfBirth, Age, nationality, PEP_status, start_date, end_date)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$applicantId,$formId, $name, $address, $area, $country, $district, $cityTownVillage, $dateOfBirth, $age, $nationality, $pepStatus, $startDate, $endDate]);
        
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted application";
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

    public function UpdateApplication($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to update application data";
        
        if (!v::numericVal()->validate($id)) {
            $response["message"] = "Invalid Form ID. Must be a numeric value.";
            return $response;
        }
         $formId = $id;
        
        if (!v::stringType()->notEmpty()->validate($data['name'])) {
            $response["message"] = "Invalid name. Must be a non-empty string.";
            return $response;
        }
       
        $name = $data['name'];
        
        if (!v::stringType()->notEmpty()->validate($data['address'])) {
            $response["message"] = "Invalid address. Must be a non-empty string.";
            return $response;
        }
        $address = $data['address'];
        
        if (!v::stringType()->notEmpty()->validate($data['area'])) {
            $response["message"] = "Invalid area. Must be a non-empty string.";
            return $response;
        }
        $area = $data['area'];
        if (!v::stringType()->notEmpty()->validate($data['district'])) {
            $response["message"] = "Invalid district. Must be a non-empty string.";
            return $response;
        }

        if (!v::stringType()->notEmpty()->validate($data['country'])) {
            $response["message"] = "Invalid district. Must be a non-empty string.";
            return $response;
        }
        
        $country = $data['country'];
        
        $district = $data['district'];
        
        if (!v::stringType()->notEmpty()->validate($data['city_town_village'])) {
            $response["message"] = "Invalid city/town/village. Must be a non-empty string.";
            return $response;
        }
        
        $cityTownVillage = $data['city_town_village'];
        
        if (!v::stringType()->notEmpty()->validate($data['date_of_birth'])) {
            $response["message"] = "Invalid date of birth. Must be a non-empty string.";
            return $response;
        }
       
        $dateOfBirth = $data['date_of_birth'];
        
        if (!v::intType()->positive()->validate($data['age'])) {
            $response["message"] = "Invalid age. Must be a positive integer.";
            return $response;
        }
         $age = $data['age'];
        if (!v::stringType()->notEmpty()->validate($data['nationality'])) {
            $response["message"] = "Invalid nationality. Must be a non-empty string.";
            return $response;
        }
        $nationality = $data['nationality'];
        if (!is_bool($data['pep_status'])) {
            $response["message"] = "Invalid PEP status. Must be a boolean value.";
            return $response;
        }
        $pepStatus = $data['pep_status'];
        
        if (!v::date()->validate($data['start_date'])) {
            $response["message"] = "Invalid start date. Must be a valid date.";
            return $response;
        }

        $startDate = $data['start_date'];
        
        if (!v::date()->validate($data['end_date'])) {
            $response["message"] = "Invalid end date. Must be a valid date.";
            return $response;
        }
        $endDate = $data['end_date'];
        
        if (!v::numericVal()->validate($data['institution_id'])) {
            $response["message"] = "Invalid institution ID. Must be a numeric value.";
            return $response;
        }
        $institutionId = $data['institution_id'];
        
        try {
            $sql = "UPDATE applications
                    SET name = ?, address = ?, area = ?, country = ?, district = ?, city_town_village = ?, DateOfBirth = ?, Age = ?, nationality = ?, PEP_status = ?, start_date = ?, end_date = ?
                    WHERE Form_ID = ?";
        
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$name, $address, $area, $country, $district, $cityTownVillage, $dateOfBirth, $age, $nationality, $pepStatus, $startDate, $endDate, $formId]);
        
            if ($stmt_status) {
                if ($stmt->affected_rows > 0) {
                    $response["rc"] = "1";
                    $response["log"] = "Successfully updated application";
                }else {
                    throw new Exception("No records were updated. The Form ID may not exist in the table.");
                }    
            } else {
                throw new Exception("Unable to update application");
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
        $response["log"] = "Failed to delete application.";
    
        try {
            // SQL statement to delete a user from the applications table where id matches
            $sql = "DELETE FROM applications WHERE Form_id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted application with ID: " . $id;
            } else {
                throw new Exception("No application found with ID: " . $id);
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
