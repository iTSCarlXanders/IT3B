
<?php
require '../../config/config.php';

requireRole('admin');

$section = $_GET['section'] ?? '';

$action = $_GET['action'] ?? '';

if ($section === 'activity-logs') {

    // Activity Logs Query
    $stmt = $pdo->query("
        SELECT *
        FROM activity_logs
        ORDER BY activity_log_created_at DESC
    ");

    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($section === 'user-sessions') {

    // User Sessions Query
    $stmt = $pdo->query("
        SELECT
            us.session_id,
            us.user_id,
            u.user_username,
            u.user_email,
            u.user_role,
            us.session_start,
            us.session_end,
            us.session_duration
        FROM user_sessions us
        INNER JOIN users u
            ON us.user_id = u.user_id
        ORDER BY us.session_start DESC
    ");

    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <div class="container py-4">

        <!-- Admin Header Container -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h1 class="h4 mb-0">Welcome, Admin!</h1>

                <a href="../../auth/signout.php" class="btn btn-outline-danger btn-sm">
                    Sign Out
                </a>

            </div>

        </div>

        <!-- Navigation Container -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <nav class="d-flex flex-wrap gap-2">

                <a href="index.php?section=activity-logs"
                   class="btn btn-outline-primary btn-sm">
                    Activity Logs
                </a>

                <a href="index.php?section=user-sessions"
                   class="btn btn-outline-primary btn-sm">
                    User Sessions
                </a>

            </nav>

        </div>

        <!-- Activity Logs Container -->
        <?php if ($section === 'activity-logs'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <h4 class="mb-3">Activity Logs</h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover table-sm align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Record ID</th>
                                <th>User ID</th>
                                <th>User Email</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>IP Address</th>
                                <th>User Agent</th>
                                <th>Date &amp; Time</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($activities as $activity): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_id'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['user_id'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['user_email'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_action'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_status'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_ip_address'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_user_agent'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_created_at'] ?? '') ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        <?php endif; ?>

        <!-- User Sessions Container -->
        <?php if ($section === 'user-sessions'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <h4 class="mb-3">User Sessions</h4>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped table-hover table-sm align-middle">

                        <thead class="table-dark">
                            <tr>
                                <th>Session ID</th>
                                <th>User ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Session Start</th>
                                <th>Session End</th>
                                <th>Duration</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($sessions as $session): ?>

                                <tr>

                                    <td>
                                        <?= htmlspecialchars($session['session_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($session['user_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($session['user_username']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($session['user_email']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($session['user_role']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($session['session_start']) ?>
                                    </td>

                                    <td>
                                        <?= $session['session_end']
                                            ? htmlspecialchars($session['session_end'])
                                            : 'Active'
                                        ?>
                                    </td>

                                    <td>

                                        <?php

                                        if ($session['session_duration'] !== null) {

                                            $seconds = (int) $session['session_duration'];

                                            $hours = floor($seconds / 3600);
                                            $minutes = floor(($seconds % 3600) / 60);
                                            $seconds = $seconds % 60;

                                            echo sprintf(
                                                '%02d hr %02d min %02d sec',
                                                $hours,
                                                $minutes,
                                                $seconds
                                            );

                                        } else {

                                            echo 'Active';

                                        }

                                        ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        <?php endif; ?>

        <!-- Default Container -->
        <?php if ($section === ''): ?>

            <div class="container bg-white border rounded shadow-sm p-4">

                <h4>Admin Dashboard</h4>

                <p class="text-muted mb-0">
                    Select Activity Logs or User Sessions above to view records.
                </p>

            </div>

        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
