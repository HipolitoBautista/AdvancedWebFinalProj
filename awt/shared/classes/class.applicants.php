<?php

require_once 'class.authorization.php';

use Respect\Validation\Validator as v;

class Applicants extends Authorization{
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
        $response["message"] = "Invalid Student Request";


        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;
        

        if(!$this->checkPermission($this->log, $this->permissions, strtolower($resource), $request, $method)){
            $response["message"] = "Permission denied to resource";
            return $response;
        }

        $response = !isset($requestParameters[1]) ? $this->getAllApplicants() : $this->getSingleApplicant($request); 

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
            $response = $this->InputNewUser($data);
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
            $response = $this->UpdateUser($data, $requestParameters[1]);
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

        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;

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
    
    public function getAllApplicants(){

        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicants found";

        $rawData = file_get_contents("php://input");
        $params = json_decode($rawData, true);

        $orderRule = !empty($params) && isset($params["order"]) ? $params["order"] : array();
        $pagingRule = !empty($params) && isset($params["paging"]) ? $params["paging"] : array();
        
        
        $orderSql = !empty($orderRule) ? "ORDER BY " : "";
        foreach($orderRule as $k => $v) {
            $orderSql .= $k . " " . $v . ", ";
        }
        
        $orderSql = rtrim($orderSql, ", ");
        
        $this->log->debug("getAllApplicants() orderSql: " . $orderSql);
        
        $pagingSql = "LIMIT 2";
        if(isset($pagingRule["start"]) && isset($pagingRule["end"])){
            $pagingSql = "LIMIT " . $pagingRule["start"] . ", " . $pagingRule["end"];
        }
        
        $this->log->debug("getAllStudents() pagingSql: " . $pagingSql);
        
        try {
            $sql = "SELECT * FROM applicant $orderSql $pagingSql";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applicant records found.");
            }
    
            $applicants = array();
            while ($row = $result->fetch_assoc()) {
                $applicants[] = $row; 
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Success";
            $response["applicants"] = $applicants; 

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function getSingleApplicant($id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicants found";
        
        try {
            $sql = "SELECT * FROM applicant WHERE id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applicant records found.");
            }
    
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
            
            $response["rc"] = "1";
            $response["log"] = "Successfully retreived applicant";   
            $response["applicants"] = $applicants; 
            
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function InputNewUser($data){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicant data found";

        if (!v::numericVal()->validate($data['id'])) {
            $response["message"] = "Invalid ID. Must be a numeric value.";
            return $response;
        }
        $id = $data['id'];

        if (!v::alpha()->length(1, null)->validate($data['first_name'])) {
            $response["message"] = "Invalid first name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $firstName = $data['first_name'];

        if (!v::alpha()->length(1, null)->validate($data['last_name'])) {
            $response["message"] = "Invalid last name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $lastName = $data['last_name'];

        if (!v::alpha()->length(1, null)->validate($data['country'])) {
            $response["message"] = "Invalid country name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $country = $data['country'];

        if (!v::alnum()->noWhitespace()->length(1, null)->validate($data['username'])) {
            $response["message"] = "Invalid username. Must be alphanumeric and have at least 1 character.";
            return $response;
        }
        $username = $data['username'];
        $password = $data['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO applicant (id, first_name, last_name, country, username, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id, $firstName, $lastName, $country, $username, $hashedPassword]);

            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted user";
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

    public function UpdateUser($data, $id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to update applicant data";
    
        if (!v::numericVal()->validate($id)) {
            $response["message"] = "Invalid ID. Must be a numeric value.";
            return $response;
        }
    
        $id = $id;
    
        if (!v::alpha()->length(1, null)->validate($data['first_name'])) {
            $response["message"] = "Invalid first name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $firstName = $data['first_name'];
    
        if (!v::alpha()->length(1, null)->validate($data['last_name'])) {
            $response["message"] = "Invalid last name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $lastName = $data['last_name'];
    
        if (!v::alpha()->length(1, null)->validate($data['country'])) {
            $response["message"] = "Invalid country name. Must contain alphabetic characters and have at least 1 character.";
            return $response;
        }
        $country = $data['country'];
    
        if (!v::alnum()->noWhitespace()->length(1, null)->validate($data['username'])) {
            $response["message"] = "Invalid username. Must be alphanumeric and have at least 1 character.";
            return $response;
        }
        $username = $data['username'];
    
        if (!v::length(6, null)->validate($data['password'])) {
            $response["message"] = "Invalid password. Must have at least 6 characters.";
            return $response;
        }
        $password = $data['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        try {
            $sql = "UPDATE applicant
                    SET first_name = ?, last_name = ?, country = ?, username = ?, password = ? 
                    WHERE id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$firstName, $lastName, $country, $username, $hashedPassword, $id]);
    
            if ($stmt_status) {
                if ($stmt->affected_rows > 0) {
                    $response["rc"] = "1";
                    $response["log"] = "Successfully updated user";
                } else {
                    throw new Exception("No records were updated. The ID may not exist in the table.");
                }            
            } else {
                throw new Exception("Unable to update user");
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
        $response["log"] = "Failed to delete applicant.";
    
        try {
            // SQL statement to delete a user from the applicants table where id matches
            $sql = "DELETE FROM applicant WHERE id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted applicant with ID: " . $id;
            } else {
                throw new Exception("No applicant found with ID: " . $id);
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
