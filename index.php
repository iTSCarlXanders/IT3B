
<?php
require_once 'config/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    requireCsrfToken();

    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    $error = 'Invalid Login Credentials';

    if ($login === '' || $password === '') {

        // Log Incomplete
        logActivity($pdo, null, $login, 'login', 'failed');

    } else {

        $result = loginUser($pdo, $login, $password);

        if ($result === true) {

            logActivity(
                $pdo,
                $_SESSION['user_id'],
                $_SESSION['user_email'],
                'login',
                'success'
            );

            header('Location: ' . BASE_URL . '/app/' . $_SESSION['user_role'] . '/index.php');
            exit;

        } elseif ($result === 'active_session') {

            $error = 'This Account is already logged in on another device';

        } else {

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex justify-content-center align-items-center">

        <div class="container border rounded shadow-sm bg-white p-4"
             style="max-width: 350px;">

            <h4 class="text-center mb-4">Login</h4>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger small">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(generateCsrfToken()) ?>"
                >

                <div class="mb-3">
                    <label class="form-label">Username or Email</label>

                    <input
                        type="text"
                        name="login"
                        class="form-control"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Sign In
                </button>

            </form>

            <?php if (isset($_GET['timeout'])): ?>
                <div class="alert alert-warning small mt-3 mb-0">
                    Your session has timed out due to inactivity.
                    Please log in again.
                </div>
            <?php endif; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
