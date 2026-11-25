<?php
$currentSection = $_GET['section'] ?? 'dashboard';

function navActive($section, $currentSection)
{
    return $section === $currentSection ? 'active' : '';
}
?>

<!-- Material Icon -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    .app-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
        font-family: Roboto, system-ui, -apple-system, sans-serif;
    }

    .app-header h1 {
        font-weight: 400;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #212121 margin:0;
    }

    .app-header h1 .material-icons {
        font-size: 2.2rem;
        color: #1976d2;
        /* Primary Blue */
    }

    .nav-links {
        display: flex;
        gap: 0.25rem;
        background: transparent;
    }

    .nav-links a {
        text-decoration: none;
        color: #616161;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.6rem 1.2rem;
        border-radius: 4px;
        transition: background-color 0.2s, color 0.2s;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .nav-links a .material-icon {
        font-size: 1.2rem;
    }


    .nav-links a:hover {
        background: #e3f2fd;
        color: #1976d2;
    }

    .nav-links a.active {
        color: #1976d2;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 3px 1px -2px rgba(0, 0, 0, 0.12), 0 1px 5px 0 rgba(0, 0, 0, 0.2);
    }

    .nav-links a.active .material-icons {
        color: #1976d2;
    }

    @media (max-width:760px) {
        .app-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .nav-links {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
        }

        .nav-links a {
            padding: 0.5rem 0.8rem;
            font-size: .8rem;
            white-space: nowrap;
        }
    }
    
</style>

<header class="app-header">
    <h1>
        <span class="material-icons">local_library</span>
        Library Dashboard
    </h1>

    <nav class="nav-links">
        <a href="index.php?section=dashboard" class="<?= navActive('dashboard', $currentSection) ?>">
            <span class="material-icons">dashboard</span>
            Dashboard
            <a />

            <a href="index.php?section=students" class="<?= navActive('students', $currentSection) ?>">
                <span class="material-icons">people</span>
                Students
                <a />

                <a href="index.php?section=books" class="<?= navActive('books', $currentSection) ?>">
                    <span class="material-icons">menu_book</span>
                    Books
                    <a />

                    <a href="index.php?section=borrow" class="<?= navActive('borrow', $currentSection) ?>">
                        <span class="material-icons">swap_horiz</span>
                        Borrow
                        <a />
    </nav>

    <header />


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Materialize Base Typography */
        body {
            background: #f5f5f5;
            /* Material Grey 100 */
            font-family: Roboto, system-ui, -apple-system, sans-serif;
            color: #212121;
            /* Material Grey 900 */
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard {
            max-width: 1300px;
            width: 100%;
        }

        /* Materialize Standard Card */
        .stat-card,
        .chart-card,
        .recent-borrows {
            background: #ffffff;
            border-radius: 2px;
            box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.14),
                0 3px 1px -2px rgba(0, 0, 0, 0.12),
                0 1px 5px 0 rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s;
        }

        .stat-card:hover,
        .chart-card:hover,
        .recent-borrows:hover {
            box-shadow: 0 4px 5px 0 rgba(0, 0, 0, 0.14),
                0 1px 10px 0 rgba(0, 0, 0, 0.12),
                0 2px 4px -1px rgba(0, 0, 0, 0.3);
        }

        /* ---- KPI CARDS ---- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            border-top: 3px solid #1976d2;
            /* Material Blue Accent */
        }

        .stat-card:nth-child(2) {
            border-top-color: #4caf50;
        }

        /* Green */
        .stat-card:nth-child(3) {
            border-top-color: #ff9800;
        }

        /* Orange */
        .stat-card:nth-child(4) {
            border-top-color: #f44336;
        }

        /* Red */

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            color: #757575;
            /* Material Grey 600 */
        }

        .stat-header .material-icons {
            font-size: 1.8rem;
            color: #bdbdbd;
            /* Material Grey 400 */
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 400;
            color: #212121;
            line-height: 1.2;
        }

        .stat-sub {
            font-size: 0.85rem;
            color: #9e9e9e;
            margin-top: 0.3rem;
        }

        /* ---- CHARTS ---- */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            padding: 1.5rem;
        }

        .chart-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212121;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.8rem;
        }

        .chart-title span {
            font-weight: 500;
            color: #1976d2;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .chart-title span .material-icons {
            font-size: 1rem;
        }

        canvas {
            display: block;
            width: 100% !important;
            height: auto !important;
            max-height: 200px;
        }

        /* ---- RECENT BORROWS ---- */
        .recent-borrows {
            padding: 1.5rem;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.8rem;
        }

        .recent-header h3 {
            font-weight: 500;
            font-size: 1.1rem;
            color: #212121;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .recent-header h3 .material-icons {
            color: #1976d2;
        }

        .recent-header a {
            font-size: 0.85rem;
            color: #1976d2;
            font-weight: 500;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .recent-header a:hover {
            text-decoration: underline;
        }

        .recent-header a .material-icons {
            font-size: 1rem;
        }

        .borrow-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eeeeee;
            font-size: 0.95rem;
        }

        .borrow-row:last-child {
            border-bottom: none;
        }

        .borrow-student {
            font-weight: 500;
            color: #424242;
            width: 30%;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .borrow-book {
            color: #616161;
            width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .borrow-student .material-icons,
        .borrow-book .material-icons {
            font-size: 1.1rem;
            color: #9e9e9e;
        }

        /* Material Design Chips */
        .borrow-status {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
            border-radius: 16px;
            /* Chip border radius */
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .borrow-status .material-icons {
            font-size: 0.9rem;
        }

        .borrow-status.returned {
            background: #e8f5e9;
            /* Green 50 */
            color: #2e7d32;
            /* Green 800 */
        }

        .borrow-status.borrowed {
            background: #fff3e0;
            /* Orange 50 */
            color: #ef6c00;
            /* Orange 800 */
        }

        .footer-note {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #9e9e9e;
            letter-spacing: 0.5px;
        }

        @media (max-width: 760px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .borrow-student,
            .borrow-book {
                width: auto;
                flex: 1;
            }

            .borrow-row {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
        }
    </style>