
<?php

require '../../config/config.php';

requireRole('admin');

$section = $_GET['section'] ?? 'activity-logs';

$action = $_GET['action'] ?? '';


// Fetch Activity Logs
if ($section === 'activity-logs') {

    $stmt = $pdo->query("
        SELECT *
        FROM activity_logs
        ORDER BY activity_log_created_at DESC
    ");

    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

}


// Fetch User Sessions
elseif ($section === 'user-sessions') {

    $stmt = $pdo->query("
        SELECT
            us.sessions_id AS session_id,
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


// Export CSV
if ($action === 'export') {

    // Export Activity Logs
    if ($section === 'activity-logs') {

        $stmt = $pdo->query("
            SELECT
                activity_log_id,
                user_id,
                user_email,
                activity_log_action,
                activity_log_status,
                activity_log_ip_address,
                activity_log_user_agent,
                activity_log_created_at
            FROM activity_logs
            ORDER BY activity_log_created_at DESC
        ");

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            $_SESSION['user_email'],
            'export_activity_logs',
            'success'
        );

        exportCsv(
            'activity_logs',
            [
                'Record ID',
                'User ID',
                'User Email',
                'Action',
                'Status',
                'IP Address',
                'User Agent',
                'Date & Time'
            ],
            $rows
        );
    }


    // Export User Sessions
    if ($section === 'user-sessions') {

        $stmt = $pdo->query("
            SELECT
                us.sessions_id AS session_id,
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

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {

            $r['session_end'] = $r['session_end'] ?: 'Active';

            if ($r['session_duration'] !== null) {

                $s = (int) $r['session_duration'];

                $hours = floor($s / 3600);
                $minutes = floor(($s % 3600) / 60);
                $seconds = $s % 60;

                $r['session_duration'] = sprintf(
                    '%02d hr %02d min %02d sec',
                    $hours,
                    $minutes,
                    $seconds
                );

            } else {

                $r['session_duration'] = 'Active';

            }
        }

        unset($r);

        logActivity(
            $pdo,
            $_SESSION['user_id'],
            $_SESSION['user_email'],
            'export_user_sessions',
            'success'
        );

        exportCsv(
            'user_sessions',
            [
                'Session ID',
                'User ID',
                'Username',
                'Email',
                'Role',
                'Session Start',
                'Session End',
                'Duration'
            ],
            $rows
        );
    }
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

        <!-- Admin Header -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h1 class="h4 mb-0">Welcome, Admin!</h1>

                <form method="POST" action="../../auth/signout.php" class="mb-0">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(generateCsrfToken()) ?>"
                    >

                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        Sign Out
                    </button>

                </form>

            </div>

        </div>


        <!-- Navigation -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <nav class="d-flex flex-wrap gap-2">

                <a
                    href="index.php?section=activity-logs"
                    class="btn btn-outline-primary btn-sm"
                >
                    Activity Logs
                </a>

                <a
                    href="index.php?section=user-sessions"
                    class="btn btn-outline-primary btn-sm"
                >
                    User Sessions
                </a>

            </nav>

        </div>


        <!-- Activity Logs Section -->
        <?php if ($section === 'activity-logs'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="mb-0">Activity Logs</h4>

                    <a
                        href="index.php?section=activity-logs&amp;action=export"
                        class="btn btn-outline-success btn-sm"
                    >
                        Export CSV
                    </a>

                </div>

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
                                        <?= htmlspecialchars($activity['activity_log_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['user_id']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['user_email']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_action']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_status']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_ip_address']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_user_agent']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($activity['activity_log_created_at']) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        <?php endif; ?>


        <!-- User Sessions Section -->
        <?php if ($section === 'user-sessions'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="mb-0">User Sessions</h4>

                    <a
                        href="index.php?section=user-sessions&amp;action=export"
                        class="btn btn-outline-success btn-sm"
                    >
                        Export CSV
                    </a>

                </div>

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


        <!-- Default Dashboard Section -->
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
