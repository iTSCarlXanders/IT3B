<?php

// Start User Session
function startUserSession($pdo)
{
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO user_sessions (
            user_id,
            session_start
        )
        VALUES (
            :user_id,
            NOW()
        )
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    return $pdo->lastInsertId();
}


// End User Session
function endUserSession($pdo)
{
    if (!isset($_SESSION['session_id'])) {
        return false;
    }

    $session_id = $_SESSION['session_id'];

    $stmt = $pdo->prepare("
        UPDATE user_sessions
        SET
            session_end = NOW(),
            session_duration = TIMESTAMPDIFF(
                SECOND,
                session_start,
                NOW()
            )
        WHERE session_id = :session_id
          AND session_end IS NULL
    ");

    return $stmt->execute([
        'session_id' => $session_id
    ]);
}


// Check if User Already Has an Active Session
function hasActiveUserSession($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT session_id
        FROM user_sessions
        WHERE user_id = :user_id
          AND session_end IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    return (bool) $stmt->fetchColumn();
}


// Check Session Timeout
function checkSessionTimeout()
{
    global $pdo;

    // 1800 seconds = 30 minutes
    $timeout = 1800;

    // Do nothing if the user is not logged in
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    // Initialize last activity if it does not exist
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
        return;
    }

    // Calculate inactivity duration
    $inactive = time() - $_SESSION['last_activity'];

    // Only end the session if the timeout is reached
    if ($inactive >= $timeout) {

        // Record the timeout in activity logs
        if (function_exists('logActivity')) {
            logActivity(
                $pdo,
                $_SESSION['user_id'],
                $_SESSION['user_email'] ?? null,
                'logout-session-timeout',
                'success'
            );
        }

        // End the database session
        endUserSession($pdo);

        // Destroy the PHP session
        session_unset();
        session_destroy();

        // Redirect to the login page
        header(
            'Location: ' . BASE_URL . '/index.php?timeout=1'
        );
        exit;
    }

    // Update last activity only if the session is still valid
    $_SESSION['last_activity'] = time();
}

?>