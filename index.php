<?php

require_once 'config/config.php';
require_once 'includes/activity-logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = trim($_POST['action'] ?? '');

    $user_id = $_SESSION['user_id'] ?? null;
    $user_email = $_SESSION['user_email'] ?? null;

    if ($action !== '') {
        $success = logActivity(
            $pdo,
            $user_id,
            $user_email,
            $action,
            'success'
        );

        if ($success) {
            echo "Activity logged successfully.";
        } else {
            echo "Failed to log activity.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logger Test</title>
</head>

<body>

<form method="POST">

    <button
        type="submit"
        name="action"
        value="sample_activity"
    >
        Sample
    </button>

</form>

</body>
</html>