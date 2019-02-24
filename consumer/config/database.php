<?php
// MySql Database Connection
class Database{
 
    // Database Credentials
    private $host = "localhost";
    private $db_name = "challenge";
    private $username = "root";
    private $password = "";
    public $conn;
 
    // Database Connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>
