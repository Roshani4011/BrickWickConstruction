<?php
include '../db_config.php';
session_start();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Set logout message in a new session
    session_start();
    $_SESSION['logout_message'] = "You have been successfully logged out.";
    
    // Redirect to login page
    header('Location: admin.php');
    exit();
}

// Check if already logged in
$is_logged_in = isset($_SESSION['admin_id']);

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_logged_in) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $sql = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get logout message if it exists and clear it
$logout_message = isset($_SESSION['logout_message']) ? $_SESSION['logout_message'] : '';
unset($_SESSION['logout_message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        .login-container {
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 350px;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }
        
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        
        .error {
            color: #ff0000;
            background-color: #ffe0e0;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
        }
        
        .success-message {
            color: #008000;
            background-color: #e0ffe0;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }
        
        .logout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            text-align: center;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <?php if ($is_logged_in): ?>
            <!-- Dashboard content when logged in -->
            <h2>Admin Dashboard</h2>
            <p style="text-align: center;">You are currently logged in.</p>
            <a href="dashboard.php" style="display: block; width: 100%; padding: 10px; background-color: #2196F3; color: white; text-align: center; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; font-size: 16px;">Go to Dashboard</a>
            <a href="admin.php?action=logout" class="logout-btn">Logout</a>
        <?php else: ?>
            <!-- Login form when not logged in -->
            <h2>Admin Login</h2>
            
            <?php if (!empty($logout_message)): ?>
                <div class="success-message">
                    <?php echo $logout_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <input type="submit" value="Login">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>