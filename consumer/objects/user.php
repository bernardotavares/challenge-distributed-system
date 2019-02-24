<?php
// User Object
class User{
 
    // Database Connection to table users
    private $conn;
    private $table_name = "users";
 
    // object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $active;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // Create a new user function
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    password = :password,
                    email = :email,
                    status = 1";
    
        // Prepare the query
        $stmt = $this->conn->prepare($query);
    
        // Sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
    
        // Bind the values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
    
        // Hash the password before saving to database
        // Normaly we would hash the password, but because we want to retrieve the password directly in the password recovery email we wont do that
        // $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $this->password);
    
        // Execute the query and check if successful
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }
}

?>