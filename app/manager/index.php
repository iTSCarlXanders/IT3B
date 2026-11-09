
<?php

// ==========================================
// DATABASE CONNECTION
// ==========================================

$host = 'localhost';
$db = 'it3b_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


// ==========================================
// SESSION
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ==========================================
// DETERMINE CURRENT SECTION AND ACTION
// ==========================================

$section = $_GET['section'] ?? 'students';
$action = $_GET['action'] ?? '';


// ==========================================
// STUDENTS
// ==========================================

// Create Student
if ($section === 'students' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['student_first_name'] ?? '');
        $lastName = trim($_POST['student_last_name'] ?? '');
        $course = trim($_POST['student_course'] ?? '');

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $stmt = $pdo->prepare("
                INSERT INTO students (
                    student_first_name,
                    student_last_name,
                    student_course
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $firstName,
                $lastName,
                $course
            ]);

            $_SESSION['alert'] = 'Student added successfully.';

            header("Location: index.php?section=students");
            exit;

        } else {

            $_SESSION['alert'] = 'Please complete all student information.';

        }
    }
}


// Update Student
if ($section === 'students' && $action === 'update') {

    $studentId = (int) ($_GET['id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT *
        FROM students
        WHERE student_id = ?
    ");

    $stmt->execute([$studentId]);

    $student = $stmt->fetch();

    if (!$student) {
        die("Student Not Found");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['student_first_name'] ?? '');
        $lastName = trim($_POST['student_last_name'] ?? '');
        $course = trim($_POST['student_course'] ?? '');

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $stmt = $pdo->prepare("
                UPDATE students
                SET
                    student_first_name = ?,
                    student_last_name = ?,
                    student_course = ?
                WHERE student_id = ?
            ");

            $stmt->execute([
                $firstName,
                $lastName,
                $course,
                $studentId
            ]);

            $_SESSION['alert'] = 'Student updated successfully.';

            header("Location: index.php?section=students");
            exit;

        } else {

            $_SESSION['alert'] = 'Please complete all student information.';

        }
    }
}


// Fetch Students
if ($section === 'students' && $action !== 'create' && $action !== 'update') {

    $stmt = $pdo->query("
        SELECT *
        FROM students
        ORDER BY student_id DESC
    ");

    $students = $stmt->fetchAll();
}


// ==========================================
// BOOKS
// ==========================================

// Create Book
if ($section === 'books' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookTitle = trim($_POST['book_title'] ?? '');
        $bookAuthor = trim($_POST['book_author'] ?? '');
        $bookCategory = trim($_POST['book_category'] ?? '');

        if ($bookTitle !== '' && $bookAuthor !== '' && $bookCategory !== '') {

            $stmt = $pdo->prepare("
                INSERT INTO books (
                    book_title,
                    book_author,
                    book_category
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $bookTitle,
                $bookAuthor,
                $bookCategory
            ]);

            $_SESSION['alert'] = 'Book added successfully.';

            header("Location: index.php?section=books");
            exit;

        } else {

            $_SESSION['alert'] = 'Please complete all book information.';

        }
    }
}


// Update Book
if ($section === 'books' && $action === 'update') {

    $bookId = (int) ($_GET['id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT *
        FROM books
        WHERE book_id = ?
    ");

    $stmt->execute([$bookId]);

    $book = $stmt->fetch();

    if (!$book) {
        die("Book Not Found");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $bookTitle = trim($_POST['book_title'] ?? '');
        $bookAuthor = trim($_POST['book_author'] ?? '');
        $bookCategory = trim($_POST['book_category'] ?? '');

        if ($bookTitle !== '' && $bookAuthor !== '' && $bookCategory !== '') {

            $stmt = $pdo->prepare("
                UPDATE books
                SET
                    book_title = ?,
                    book_author = ?,
                    book_category = ?
                WHERE book_id = ?
            ");

            $stmt->execute([
                $bookTitle,
                $bookAuthor,
                $bookCategory,
                $bookId
            ]);

            $_SESSION['alert'] = 'Book updated successfully.';

            header("Location: index.php?section=books");
            exit;

        } else {

            $_SESSION['alert'] = 'Please complete all book information.';

        }
    }
}


// Fetch Books
if ($section === 'books' && $action !== 'create' && $action !== 'update') {

    $stmt = $pdo->query("
        SELECT *
        FROM books
        ORDER BY book_id DESC
    ");

    $books = $stmt->fetchAll();
}


// ==========================================
// BORROW
// ==========================================

// Borrow a Book
if ($section === 'borrow' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $bookId = (int) ($_POST['book_id'] ?? 0);

        if ($studentId > 0 && $bookId > 0) {

            // Check if student has an unreturned book
            $stmt = $pdo->prepare("
                SELECT borrow_id
                FROM borrow
                WHERE student_id = ?
                AND borrow_return_date IS NULL
                LIMIT 1
            ");

            $stmt->execute([$studentId]);

            $studentBorrow = $stmt->fetch();

            if ($studentBorrow) {

                $_SESSION['alert'] = 'This student cannot borrow another book because a previous book has not been returned.';

            } else {

                // Check if book is already borrowed
                $stmt = $pdo->prepare("
                    SELECT borrow_id
                    FROM borrow
                    WHERE book_id = ?
                    AND borrow_return_date IS NULL
                    LIMIT 1
                ");

                $stmt->execute([$bookId]);

                $bookBorrow = $stmt->fetch();

                if ($bookBorrow) {

                    $_SESSION['alert'] = 'This book cannot be borrowed because it has not been returned.';

                } else {

                    // Create Borrow Record
                    $stmt = $pdo->prepare("
                        INSERT INTO borrow (
                            student_id,
                            book_id
                        )
                        VALUES (?, ?)
                    ");

                    $stmt->execute([
                        $studentId,
                        $bookId
                    ]);

                    $_SESSION['alert'] = 'Book borrowed successfully.';
                }
            }

        } else {

            $_SESSION['alert'] = 'Please select a student and a book.';

        }

        header("Location: index.php?section=borrow");
        exit;
    }
}


// Return a Book
if ($section === 'borrow' && $action === 'return') {

    $borrowId = (int) ($_GET['id'] ?? 0);

    $stmt = $pdo->prepare("
        UPDATE borrow
        SET borrow_return_date = CURRENT_TIMESTAMP
        WHERE borrow_id = ?
        AND borrow_return_date IS NULL
    ");

    $stmt->execute([$borrowId]);

    if ($stmt->rowCount() > 0) {

        $_SESSION['alert'] = 'Book returned successfully.';

    } else {

        $_SESSION['alert'] = 'Borrow record not found or book already returned.';

    }

    header("Location: index.php?section=borrow");
    exit;
}


// Fetch Borrow Records and Form Options
if ($section === 'borrow') {

    // Fetch Students
    $stmt = $pdo->query("
        SELECT
            student_id,
            student_first_name,
            student_last_name
        FROM students
        ORDER BY student_last_name, student_first_name
    ");

    $students = $stmt->fetchAll();


    // Fetch Books
    $stmt = $pdo->query("
        SELECT
            book_id,
            book_title,
            book_author
        FROM books
        ORDER BY book_title
    ");

    $books = $stmt->fetchAll();


    // Fetch Borrow Records
    $stmt = $pdo->query("
        SELECT
            borrow.borrow_id,
            borrow.borrow_date,
            borrow.borrow_return_date,
            students.student_first_name,
            students.student_last_name,
            books.book_title,
            books.book_author
        FROM borrow
        INNER JOIN students
            ON borrow.student_id = students.student_id
        INNER JOIN books
            ON borrow.book_id = books.book_id
        ORDER BY borrow.borrow_id DESC
    ");

    $borrowRecords = $stmt->fetchAll();
}


// ==========================================
// FLASH ALERT
// ==========================================

$alert = $_SESSION['alert'] ?? '';
unset($_SESSION['alert']);


// ==========================================
// HELPER FUNCTION
// ==========================================

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manager Dashboard</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>

<body class="bg-light">

    <div class="container py-4">

        <!-- MANAGER HEADER -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                <h1 class="h4 mb-0">Welcome, Manager!</h1>

                <a
                    href="../../auth/signout.php"
                    class="btn btn-outline-danger btn-sm"
                >
                    Sign Out
                </a>

            </div>

        </div>


        <!-- NAVIGATION -->
        <div class="container bg-white border rounded shadow-sm p-3 mb-4">

            <nav class="d-flex flex-wrap gap-2">

                <a
                    href="index.php?section=students"
                    class="btn <?= $section === 'students' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm"
                >
                    Students
                </a>

                <a
                    href="index.php?section=books"
                    class="btn <?= $section === 'books' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm"
                >
                    Books
                </a>

                <a
                    href="index.php?section=borrow"
                    class="btn <?= $section === 'borrow' ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm"
                >
                    Borrow
                </a>

            </nav>

        </div>


        <!-- ALERT MESSAGE -->
        <?php if ($alert !== ''): ?>

            <div class="alert alert-info alert-dismissible fade show" role="alert">

                <?= e($alert) ?>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>

        <?php endif; ?>


        <!-- ================================== -->
        <!-- STUDENTS SECTION -->
        <!-- ================================== -->

        <?php if ($section === 'students'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="mb-0">Students</h4>

                    <a
                        href="index.php?section=students&action=create"
                        class="btn btn-primary btn-sm"
                    >
                        + Add Student
                    </a>

                </div>


                <!-- CREATE STUDENT -->
                <?php if ($action === 'create'): ?>

                    <div class="border rounded p-3">

                        <h5 class="mb-3">Create Student</h5>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">First Name</label>

                                <input
                                    type="text"
                                    name="student_first_name"
                                    class="form-control"
                                    placeholder="Enter first name"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Last Name</label>

                                <input
                                    type="text"
                                    name="student_last_name"
                                    class="form-control"
                                    placeholder="Enter last name"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Course</label>

                                <input
                                    type="text"
                                    name="student_course"
                                    class="form-control"
                                    placeholder="Enter course"
                                    required
                                >

                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                Save Student
                            </button>

                            <a
                                href="index.php?section=students"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>


                <!-- UPDATE STUDENT -->
                <?php elseif ($action === 'update'): ?>

                    <div class="border rounded p-3">

                        <h5 class="mb-3">Update Student Information</h5>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">First Name</label>

                                <input
                                    type="text"
                                    name="student_first_name"
                                    class="form-control"
                                    value="<?= e($student['student_first_name']) ?>"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Last Name</label>

                                <input
                                    type="text"
                                    name="student_last_name"
                                    class="form-control"
                                    value="<?= e($student['student_last_name']) ?>"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Course</label>

                                <input
                                    type="text"
                                    name="student_course"
                                    class="form-control"
                                    value="<?= e($student['student_course']) ?>"
                                    required
                                >

                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                Update Student
                            </button>

                            <a
                                href="index.php?section=students"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>


                <!-- STUDENTS TABLE -->
                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover table-sm align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Course</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($students)): ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            No students found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($students as $student): ?>

                                        <tr>

                                            <td><?= e($student['student_id']) ?></td>

                                            <td><?= e($student['student_first_name']) ?></td>

                                            <td><?= e($student['student_last_name']) ?></td>

                                            <td><?= e($student['student_course']) ?></td>

                                            <td><?= e($student['student_created_at']) ?></td>

                                            <td>

                                                <a
                                                    href="index.php?section=students&action=update&id=<?= e($student['student_id']) ?>"
                                                    class="btn btn-outline-primary btn-sm"
                                                >
                                                    Edit
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- ================================== -->
        <!-- BOOKS SECTION -->
        <!-- ================================== -->

        <?php if ($section === 'books'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="mb-0">Books</h4>

                    <a
                        href="index.php?section=books&action=create"
                        class="btn btn-primary btn-sm"
                    >
                        + Add Book
                    </a>

                </div>


                <!-- CREATE BOOK -->
                <?php if ($action === 'create'): ?>

                    <div class="border rounded p-3">

                        <h5 class="mb-3">Create Book</h5>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">Book Title</label>

                                <input
                                    type="text"
                                    name="book_title"
                                    class="form-control"
                                    placeholder="Enter book title"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Book Author</label>

                                <input
                                    type="text"
                                    name="book_author"
                                    class="form-control"
                                    placeholder="Enter book author"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Book Category</label>

                                <input
                                    type="text"
                                    name="book_category"
                                    class="form-control"
                                    placeholder="Enter book category"
                                    required
                                >

                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                Save Book
                            </button>

                            <a
                                href="index.php?section=books"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>


                <!-- UPDATE BOOK -->
                <?php elseif ($action === 'update'): ?>

                    <div class="border rounded p-3">

                        <h5 class="mb-3">Update Book Information</h5>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">Book Title</label>

                                <input
                                    type="text"
                                    name="book_title"
                                    class="form-control"
                                    value="<?= e($book['book_title']) ?>"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Book Author</label>

                                <input
                                    type="text"
                                    name="book_author"
                                    class="form-control"
                                    value="<?= e($book['book_author']) ?>"
                                    required
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Book Category</label>

                                <input
                                    type="text"
                                    name="book_category"
                                    class="form-control"
                                    value="<?= e($book['book_category']) ?>"
                                    required
                                >

                            </div>

                            <button type="submit" class="btn btn-primary btn-sm">
                                Update Book
                            </button>

                            <a
                                href="index.php?section=books"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>


                <!-- BOOKS TABLE -->
                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover table-sm align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>ID</th>
                                    <th>Book Title</th>
                                    <th>Book Author</th>
                                    <th>Book Category</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($books)): ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            No books found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($books as $book): ?>

                                        <tr>

                                            <td><?= e($book['book_id']) ?></td>

                                            <td><?= e($book['book_title']) ?></td>

                                            <td><?= e($book['book_author']) ?></td>

                                            <td><?= e($book['book_category']) ?></td>

                                            <td><?= e($book['book_created_at']) ?></td>

                                            <td>

                                                <a
                                                    href="index.php?section=books&action=update&id=<?= e($book['book_id']) ?>"
                                                    class="btn btn-outline-primary btn-sm"
                                                >
                                                    Edit
                                                </a>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- ================================== -->
        <!-- BORROW SECTION -->
        <!-- ================================== -->

        <?php if ($section === 'borrow'): ?>

            <div class="container bg-white border rounded shadow-sm p-3 mb-4">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <h4 class="mb-0">Borrow Records</h4>

                    <a
                        href="index.php?section=borrow&action=create"
                        class="btn btn-primary btn-sm"
                    >
                        + Borrow a Book
                    </a>

                </div>


                <!-- CREATE BORROW -->
                <?php if ($action === 'create'): ?>

                    <div class="border rounded p-3">

                        <h5 class="mb-3">Borrow a Book</h5>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">Select Student</label>

                                <select
                                    name="student_id"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Select Student --
                                    </option>

                                    <?php foreach ($students as $student): ?>

                                        <option value="<?= e($student['student_id']) ?>">

                                            <?= e($student['student_first_name'] . ' ' . $student['student_last_name']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <div class="mb-3">

                                <label class="form-label">Select Book</label>

                                <select
                                    name="book_id"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Select Book --
                                    </option>

                                    <?php foreach ($books as $book): ?>

                                        <option value="<?= e($book['book_id']) ?>">

                                            <?= e($book['book_title'] . ' - ' . $book['book_author']) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <button type="submit" class="btn btn-primary btn-sm">
                                Confirm Borrow
                            </button>

                            <a
                                href="index.php?section=borrow"
                                class="btn btn-outline-secondary btn-sm"
                            >
                                Cancel
                            </a>

                        </form>

                    </div>


                <!-- BORROW RECORDS TABLE -->
                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-hover table-sm align-middle">

                            <thead class="table-dark">

                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Book</th>
                                    <th>Borrow Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php if (empty($borrowRecords)): ?>

                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            No borrow records found.
                                        </td>
                                    </tr>

                                <?php else: ?>

                                    <?php foreach ($borrowRecords as $borrow): ?>

                                        <tr>

                                            <td>
                                                <?= e($borrow['borrow_id']) ?>
                                            </td>

                                            <td>
                                                <?= e($borrow['student_first_name'] . ' ' . $borrow['student_last_name']) ?>
                                            </td>

                                            <td>
                                                <?= e($borrow['book_title'] . ' - ' . $borrow['book_author']) ?>
                                            </td>

                                            <td>
                                                <?= e($borrow['borrow_date']) ?>
                                            </td>

                                            <td>

                                                <?php if ($borrow['borrow_return_date']): ?>

                                                    <?= e($borrow['borrow_return_date']) ?>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        Not returned
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <?php if ($borrow['borrow_return_date']): ?>

                                                    <span class="badge text-bg-success">
                                                        Returned
                                                    </span>

                                                <?php else: ?>

                                                    <span class="badge text-bg-warning">
                                                        Borrowed
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td>

                                                <?php if (!$borrow['borrow_return_date']): ?>

                                                    <a
                                                        href="index.php?section=borrow&action=return&id=<?= e($borrow['borrow_id']) ?>"
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="return confirm('Mark this book as returned?');"
                                                    >
                                                        Return
                                                    </a>

                                                <?php else: ?>

                                                    <span class="text-muted">
                                                        Completed
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        <?php endif; ?>


        <!-- INVALID SECTION -->
        <?php if (!in_array($section, ['students', 'books', 'borrow'], true)): ?>

            <div class="container bg-white border rounded shadow-sm p-4">

                <h4>Page Not Found</h4>

                <p class="text-muted mb-0">
                    The selected manager section does not exist.
                </p>

                <a
                    href="index.php?section=students"
                    class="btn btn-primary btn-sm mt-3"
                >
                    Back to Students
                </a>

            </div>

        <?php endif; ?>


    </div>


    <!-- BOOTSTRAP JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
