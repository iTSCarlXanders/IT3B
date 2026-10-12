<?php 
require_once 'config/config.php'; 
 
 
if(isset($_SESSION['user_id'])){ 
    header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php'); 
    exit; 
} 
 
$error=''; 
 
if($_SERVER['REQUEST_METHOD'] === 'POST'){ 
    $login = trim($_POST['login'] ?? ''); 
    $password = $_POST['password'] ?? ''; 
 
    $error = 'Invalid login credentials'; 
 
    if ($login==='' || $password ===''){ 
 
        // Log incomplete login attempt 
        logActivity($pdo,null,$login,'login','failed'); 
 
    } else { 
 
        $result = loginUser($pdo,$login,$password); 
 
        if ($result===true){ 
         
            logActivity(
                $pdo,
                $_SESSION['user_id'],
                $_SESSION['user_email'],
                'login',
                'success'
            ); 
 
            header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php'); 
            exit; 
 
        }elseif($result === 'active_session'){ 
 
            $error = 'This account is already logged in on another device'; 
 
        }else{ 
 
            $error = 'Invalid login credentials'; 
        } 
 
    } 
} 
 
 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login</title> 
</head> 
<body> 
 
<form method="POST"> 
 
    <label>Username or Email</label> 
    <input type="text" 
           name="login" 
           required> 
    <br> 
    <br> 
 
    <label>Password</label> 
    <input type="password" 
           name="password" 
           required> 
    <br> 
    <br> 
 
    <button type="submit">Sign In</button> 
 
    <?php if($error !== ''): ?> 
        <p><?php echo htmlspecialchars($error); ?></p> 
    <?php endif; ?> 
 
</form> 

<?php if (isset($_GET['timeout'])): ?>
    <p>Your session has expired due to inactivity. Please log in again.</p>
<?php endif; ?>

</body> 
</html>