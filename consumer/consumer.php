<?php
require '../vendor/autoload.php';
include_once 'config/database.php';
include_once 'objects/user.php';

date_default_timezone_set('PRC');
use Kafka\Consumer;
use Kafka\ConsumerConfig;
// use Monolog\Handler\StdoutHandler;
// use Monolog\Logger;

// // Create the logger
// $logger = new Logger('my_logger');
// // Now add some handlers
// $logger->pushHandler(new StdoutHandler());

// Consumer Configurations
$config = \Kafka\ConsumerConfig::getInstance();
$config->setMetadataRefreshIntervalMs(10000);
$config->setMetadataBrokerList('127.0.0.1:9092');
$config->setGroupId('users');
$config->setBrokerVersion('1.0.0');
$config->setTopics(['create_user', 'login', 'recover_password']);

$consumer = new \Kafka\Consumer();
// $consumer->setLogger($logger);

$consumer->start(function($topic, $part, $message) {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();

    // Get data
    $data = json_decode($message["message"]["value"], true);


    // We act depending of the topic
    if($topic == 'create_user'){

        // Instantiate product object
        $user = new User($db);

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
    elseif($topic == 'login'){
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
    elseif($topic == 'recover_password'){
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
    $query = "SELECT id, username, password, email
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

        // Send password recovery email
        $to      = $row['email'];
        $from    = 'challenge@example.com';
        $message = 'hello, here is your password: '.$row['password'];
        $headers = 'From: challenge@example.com' . "\r\n" .
        'Reply-To: challenge@example.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        sendMail($to, $from, $message, $headers);
        
        return $message; // let's return the message for now
    }

    return false;
}

function sendMail($to, $from, $message, $headers){

    try {
        mail($to, $subject, $message, $headers);
        echo 'Message has been sent.';
    }
    catch (Exception $e) {
        echo 'Message could not be sent.';
    }
}