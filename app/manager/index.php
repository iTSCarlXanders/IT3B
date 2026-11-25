
<?php

// Database connection
$host = 'localhost';
$db = 'it30b_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host; dbname=$db; charset=$charset";

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

// Session
session_start();

// Determine current section
$section = $_GET['section'] ?? 'students';

// Determine CRUD Operation
$action = $_GET['action'] ?? '';


// RETURN BOOK
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
        $_SESSION['alert'] = "Book returned successfully!";
    } else {
        $_SESSION['alert'] = "Book already returned or borrow record not found.";
    }

    header("Location: index.php?section=borrow");
    exit;
}


// FETCH STUDENTS
if ($section === 'students') {

    $stmt = $pdo->query("
        SELECT *
        FROM students
        ORDER BY student_id DESC
    ");

    $students = $stmt->fetchAll();
}


// CREATE STUDENT
if ($section === 'students' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $firstName = trim($_POST['student_first_name'] ?? '');
        $lastName = trim($_POST['student_last_name'] ?? '');
        $course = trim($_POST['student_course'] ?? '');

        if ($firstName !== '' && $lastName !== '' && $course !== '') {

            $sql = "
                INSERT INTO students (
                    student_first_name,
                    student_last_name,
                    student_course
                )
                VALUES (?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $firstName,
                $lastName,
                $course
            ]);

            header("Location: index.php?section=students");
            exit;
        }
    }
}


// UPDATE STUDENT
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

        $sql = "
            UPDATE students
            SET
                student_first_name = ?,
                student_last_name = ?,
                student_course = ?
            WHERE student_id = ?
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $firstName,
            $lastName,
            $course,
            $studentId
        ]);

        header("Location: index.php?section=students");
        exit;
    }
}


// FETCH BOOKS
if ($section === 'books') {

    $stmt = $pdo->query("
        SELECT *
        FROM books
        ORDER BY book_id DESC
    ");

    $books = $stmt->fetchAll();
}


// CREATE BOOK
if ($section === 'books' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $title = trim($_POST['book_title'] ?? '');
        $author = trim($_POST['book_author'] ?? '');
        $category = trim($_POST['book_category'] ?? '');

        if ($title !== '' && $author !== '' && $category !== '') {

            $sql = "
                INSERT INTO books (
                    book_title,
                    book_author,
                    book_category
                )
                VALUES (?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $title,
                $author,
                $category
            ]);

            header("Location: index.php?section=books");
            exit;
        }
    }
}


// UPDATE BOOK
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

        $title = trim($_POST['book_title'] ?? '');
        $author = trim($_POST['book_author'] ?? '');
        $category = trim($_POST['book_category'] ?? '');

        $sql = "
            UPDATE books
            SET
                book_title = ?,
                book_author = ?,
                book_category = ?
            WHERE book_id = ?
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $title,
            $author,
            $category,
            $bookId
        ]);

        header("Location: index.php?section=books");
        exit;
    }
}


// BORROW A BOOK
if ($section === 'borrow' && $action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $bookId = (int) ($_POST['book_id'] ?? 0);

        if ($studentId > 0 && $bookId > 0) {

            // Check if student exists
            $stmt = $pdo->prepare("
                SELECT student_id
                FROM students
                WHERE student_id = ?
            ");

            $stmt->execute([$studentId]);
            $studentExists = $stmt->fetch();

            // Check if book exists
            $stmt = $pdo->prepare("
                SELECT book_id
                FROM books
                WHERE book_id = ?
            ");

            $stmt->execute([$bookId]);
            $bookExists = $stmt->fetch();

            if ($studentExists && $bookExists) {

                $stmt = $pdo->prepare("
                    INSERT INTO borrow (
                        student_id,
                        book_id,
                        borrow_date,
                        borrow_return_date
                    )
                    VALUES (?, ?, CURRENT_TIMESTAMP, NULL)
                ");

                $stmt->execute([
                    $studentId,
                    $bookId
                ]);

                $_SESSION['alert'] = "Book borrowed successfully!";

                header("Location: index.php?section=borrow");
                exit;

            } else {
                $_SESSION['alert'] = "Invalid student or book.";
                header("Location: index.php?section=borrow&action=create");
                exit;
            }

        } else {
            $_SESSION['alert'] = "Please select a student and a book.";
            header("Location: index.php?section=borrow&action=create");
            exit;
        }
    }
}


// RETRIEVE BORROW RECORDS
if ($section === 'borrow') {

    $stmt = $pdo->query("
        SELECT
            br.borrow_id,
            CONCAT(
                s.student_first_name,
                ' ',
                s.student_last_name
            ) AS student_name,
            b.book_title,
            br.borrow_date,
            br.borrow_return_date,
            CASE
                WHEN br.borrow_return_date IS NULL THEN 'Borrowed'
                ELSE 'Returned'
            END AS borrow_status
        FROM borrow br
        INNER JOIN students s
            ON br.student_id = s.student_id
        INNER JOIN books b
            ON br.book_id = b.book_id
        ORDER BY br.borrow_id DESC
    ");

    $borrows = $stmt->fetchAll();


    // Fetch students for the borrow form
    $stmt = $pdo->query("
        SELECT
            student_id,
            student_first_name,
            student_last_name
        FROM students
        ORDER BY student_last_name, student_first_name
    ");

    $students = $stmt->fetchAll();


    // Fetch books for the borrow form
    $stmt = $pdo->query("
        SELECT
            book_id,
            book_title,
            book_author
        FROM books
        ORDER BY book_title
    ");

    $books = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
</head>

<body>

    <h1>Simple Library System</h1>

    <nav>
        <a href="index.php?section=students">Students</a>
        <a href="index.php?section=books">Books</a>
        <a href="index.php?section=borrow">Borrow</a>
    </nav>

    <hr>


    <!-- STUDENTS SECTION -->
    <?php if ($section === 'students'): ?>

        <h1>Students</h1>

        <p>
            <a href="index.php?section=students&action=create">
                Add Student
            </a>
        </p>

        <?php if ($action === 'create'): ?>

            <h2>Create Student</h2>

            <form method="POST">

                <p>
                    <label>First Name:</label>
                    <br>
                    <input type="text" name="student_first_name" required>
                </p>

                <p>
                    <label>Last Name:</label>
                    <br>
                    <input type="text" name="student_last_name" required>
                </p>

                <p>
                    <label>Course:</label>
                    <br>
                    <input type="text" name="student_course" required>
                </p>

                <button type="submit">Save</button>

                <a href="index.php?section=students">Cancel</a>

            </form>

        <?php elseif ($action === 'update'): ?>

            <h2>Update Student Info</h2>

            <form method="POST">

                <p>
                    <label>First Name:</label>
                    <br>
                    <input
                        type="text"
                        name="student_first_name"
                        value="<?= htmlspecialchars($student['student_first_name']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Last Name:</label>
                    <br>
                    <input
                        type="text"
                        name="student_last_name"
                        value="<?= htmlspecialchars($student['student_last_name']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Course:</label>
                    <br>
                    <input
                        type="text"
                        name="student_course"
                        value="<?= htmlspecialchars($student['student_course']) ?>"
                        required
                    >
                </p>

                <button type="submit">Update</button>

                <a href="index.php?section=students">Cancel</a>

            </form>

        <?php else: ?>

            <table border="1">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Course</th>
                        <th>Created at</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($students as $student): ?>

                        <tr>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['student_first_name']) ?></td>
                            <td><?= htmlspecialchars($student['student_last_name']) ?></td>
                            <td><?= htmlspecialchars($student['student_course']) ?></td>
                            <td><?= htmlspecialchars($student['student_created_at']) ?></td>

                            <td>
                                <a href="index.php?section=students&action=update&id=<?= (int) $student['student_id'] ?>">Edit</a>
                                |
                                <a href="#">Delete</a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    <?php endif; ?>


    <!-- BOOKS SECTION -->
    <?php if ($section === 'books'): ?>

        <h1>Books</h1>

        <p>
            <a href="index.php?section=books&action=create">
                Add Book
            </a>
        </p>

        <?php if ($action === 'create'): ?>

            <h2>Create Book</h2>

            <form method="POST">

                <p>
                    <label>Book Title:</label>
                    <br>
                    <input type="text" name="book_title" required>
                </p>

                <p>
                    <label>Book Author:</label>
                    <br>
                    <input type="text" name="book_author" required>
                </p>

                <p>
                    <label>Book Category:</label>
                    <br>
                    <input type="text" name="book_category" required>
                </p>

                <button type="submit">Save</button>

                <a href="index.php?section=books">Cancel</a>

            </form>

        <?php elseif ($action === 'update'): ?>

            <h2>Update Book Info</h2>

            <form method="POST">

                <p>
                    <label>Book Title:</label>
                    <br>
                    <input
                        type="text"
                        name="book_title"
                        value="<?= htmlspecialchars($book['book_title']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Book Author:</label>
                    <br>
                    <input
                        type="text"
                        name="book_author"
                        value="<?= htmlspecialchars($book['book_author']) ?>"
                        required
                    >
                </p>

                <p>
                    <label>Book Category:</label>
                    <br>
                    <input
                        type="text"
                        name="book_category"
                        value="<?= htmlspecialchars($book['book_category']) ?>"
                        required
                    >
                </p>

                <button type="submit">Update</button>

                <a href="index.php?section=books">Cancel</a>

            </form>

        <?php else: ?>

            <table border="1">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Created at</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($books as $book): ?>

                        <tr>
                            <td><?= htmlspecialchars($book['book_id']) ?></td>
                            <td><?= htmlspecialchars($book['book_title']) ?></td>
                            <td><?= htmlspecialchars($book['book_author']) ?></td>
                            <td><?= htmlspecialchars($book['book_category']) ?></td>
                            <td><?= htmlspecialchars($book['book_created_at']) ?></td>

                            <td>
                                <a href="index.php?section=books&action=update&id=<?= (int) $book['book_id'] ?>">Edit</a>
                                |
                                <a href="#">Delete</a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    <?php endif; ?>


    <!-- BORROW SECTION -->
    <?php if ($section === 'borrow'): ?>

        <h1>Borrow</h1>

        <p>
            <a href="index.php?section=borrow&action=create">
                Borrow a Book
            </a>
        </p>

        <?php if ($action === 'create'): ?>

            <h2>Borrow a Book</h2>

            <form method="POST">

                <label>Student</label>
                <br>

                <select name="student_id" required>
                    <option value="" selected disabled>Select Student</option>

                    <?php foreach ($students as $student): ?>

                        <option value="<?= (int) $student['student_id'] ?>">
                            <?= htmlspecialchars(
                                $student['student_first_name'] . ' ' .
                                $student['student_last_name']
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <br>
                <br>

                <label>Book</label>
                <br>

                <select name="book_id" required>
                    <option value="" selected disabled>Select Book</option>

                    <?php foreach ($books as $book): ?>

                        <option value="<?= (int) $book['book_id'] ?>">
                            <?= htmlspecialchars(
                                $book['book_title'] . ' - ' .
                                $book['book_author']
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <br>
                <br>

                <button type="submit">Borrow</button>

                <a href="index.php?section=borrow">Cancel</a>

            </form>

        <?php else: ?>

            <table border="1">

                <thead>
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

                    <?php if (empty($borrows)): ?>

                        <tr>
                            <td colspan="7">No borrow records found.</td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($borrows as $br): ?>

                            <tr>
                                <td><?= htmlspecialchars($br['borrow_id']) ?></td>

                                <td><?= htmlspecialchars($br['student_name']) ?></td>

                                <td><?= htmlspecialchars($br['book_title']) ?></td>

                                <td><?= htmlspecialchars($br['borrow_date'] ?? '') ?></td>

                                <td>
                                    <?= htmlspecialchars(
                                        $br['borrow_return_date'] ?? 'Not returned'
                                    ) ?>
                                </td>

                                <td><?= htmlspecialchars($br['borrow_status']) ?></td>

                                <td>
                                    <?php if ($br['borrow_return_date'] === null): ?>

                                        <a
                                            href="index.php?section=borrow&action=return&id=<?= (int) $br['borrow_id'] ?>"
                                            onclick="return confirm('Are you sure you want to return this book?');"
                                        >
                                            Return
                                        </a>

                                    <?php else: ?>

                                        Returned

                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        <?php endif; ?>

    <?php endif; ?>


    <?php if (isset($_SESSION['alert'])): ?>

        <script>
            alert(<?= json_encode($_SESSION['alert']) ?>);
        </script>

        <?php unset($_SESSION['alert']); ?>

    <?php endif; ?>

</body>
</html>