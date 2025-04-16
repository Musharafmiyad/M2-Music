<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - M2 Music Warehouse</title>
    <link rel="stylesheet" href="loginstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 100px auto;
            padding: 20px;
        }
        .welcome-message {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff4444;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <header>
        <!-- Your existing header content -->
    </header>

    <div class="dashboard-container">
        <div class="welcome-message">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION["firstname"]); ?>!</h1>
            <p>You are now logged into your account.</p>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <footer>
        <!-- Your existing footer content -->
    </footer>
</body>
</html>