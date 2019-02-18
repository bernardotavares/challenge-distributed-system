<?php
require '../vendor/autoload.php';
include_once 'config/database.php';
include_once 'objects/user.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

// Instantiate product object
$user = new User($db);

// Consumer Configurations
$config = \Kafka\ConsumerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setGroupId('users');
$config->setBrokerVersion('1.0.0');
$config->setTopics(['create_user', 'login', 'recover_password']);
$consumer = new \Kafka\Consumer();


// Consumer will get producer data and act accordingly
$consumer->start(function($topic, $part, $message) {
    
    // Get data
    $data = json_decode($message, true);
    $m_topic = $data['topic'];

    if($m_topic == 'create_user'){
        // Set values
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        
        // Create the user
        if($user->create()){
            // Response code and display message
            http_response_code(201);
            echo json_encode(array("message" => "User was created."));
        }
        else{
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create user."));
        }
    }
    elseif($m_topic == 'login'){
        // Set values
        $username = htmlspecialchars(strip_tags($data['username']));
        $password = htmlspecialchars(strip_tags($data['password']));
        
        // Verify Login
        if(verifyLogin($db, $username, $password)){
            http_response_code(200);
            echo json_encode(array("message" => "Login successful."));
        }
        else{
            http_response_code(404);
            echo json_encode(array("message" => "Login failed."));
        }
    }
    elseif($m_topic == 'recover_password'){
        // Set values
        $username = htmlspecialchars(strip_tags($data['username']));

        // Recover Password
        $recover = recoverPassword($db, $username);
        if($recover){
            // Response code and display message
            http_response_code(200);
            echo json_encode(array("message" => $recover));
        }
        else{
            http_response_code(404);
            echo json_encode(array("message" => "Email sent (not really)."));
        }
    }

});




// Function to verify if given password and username exist in the database
function verifyLogin($db, $username, $password){
     
    // PDO Query
    $query = "SELECT id, username, password
            FROM users WHERE
            username = :username
            LIMIT 0,1";
 
    $stmt = $db->prepare( $query );
    $stmt->bindParam(':username', $username);
    $stmt->execute();
 
    // Check if there are any rows
    $num = $stmt->rowCount();
    if($num>0){ 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hash = $row['password'];
        
        if (password_verify($password, $hash)) {
            return true;
        } else {
            return false;
        }
    }

    return false;
}


// Function for password recovery
function recoverPassword($db, $username){
     
    // PDO Query
    $query = "SELECT id, username, password
            FROM users WHERE
            username = :username
            LIMIT 0,1";
 
    $stmt = $db->prepare( $query );
    $stmt->bindParam(':username', $username);
     $stmt->execute();
 
    // Check if there are any rows
    $num = $stmt->rowCount();
 
    if($num>0){ 
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hash = $row['password'];
        $msg = "Your (hashed) Password: ".$hash;
        // $msg = wordwrap($msg,70);
        // // send email
        // mail($email,"Your Password",$msg);
        
        return $msg; // let's simplify for now
    }
 
    return false;
}