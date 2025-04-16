<?php
require_once 'auth_config.php';
session_start();
header('Content-Type: application/json');

try {
    // Get and sanitize form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        throw new Exception("All fields are required");
    }

    // Prepare select statement
    $sql = "SELECT id, firstname, lastname, email, password FROM users WHERE email = ?";
    $stmt = $auth_pdo->prepare($sql);
    
    // Execute the prepared statement
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            // Password is correct, start a new session
            session_regenerate_id();
            
            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $row['id'];
            $_SESSION["email"] = $row['email'];
            $_SESSION["firstname"] = $row['firstname'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            throw new Exception("Invalid password");
        }
    } else {
        throw new Exception("No account found with that email");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>