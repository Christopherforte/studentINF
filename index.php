<?php
session_start();

// Simple SQLite database setup
$db_file = __DIR__ . '/students.db';
$pdo = new PDO('sqlite:' . $db_file);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create table if it doesn't exist
$pdo->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    course TEXT NOT NULL,
    year_level INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$action = $_GET['action'] ?? 'list';
$search = $_GET['search'] ?? '';
$id = $_GET['id'] ?? null;

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'store') {
            $stmt = $pdo->prepare("INSERT INTO students (name, course, year_level) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['course'], $_POST['year_level']]);
            $_SESSION['success'] = 'Student added successfully!';
            header('Location: index.php');
            exit;
        }
        
        if ($action === 'update') {
            $stmt = $pdo->prepare("UPDATE students SET name = ?, course = ?, year_level = ? WHERE id = ?");
            $stmt->execute([$_POST['name'], $_POST['course'], $_POST['year_level'], $_POST['id']]);
            $_SESSION['success'] = 'Student updated successfully!';
            header('Location: index.php');
            exit;
        }
        
        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $_SESSION['success'] = 'Student deleted successfully!';
            header('Location: index.php');
            exit;
        }
    }
}

// Fetch students with search
if ($action === 'list') {
    if ($search) {
        $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR course LIKE ? ORDER BY id DESC");
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch single student for edit
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'edit' ? 'Edit Student' : 'Student Information System'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 1.5rem 0;
        }
        
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        
        .container {
            max-width: 1200px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }
        
        .card h4 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #52c41a 0%, #13c2c2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(82, 196, 26, 0.3);
            background: linear-gradient(135deg, #52c41a 0%, #13c2c2 100%);
            border-color: transparent;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #faad14 0%, #ff7a45 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(250, 173, 20, 0.3);
            background: linear-gradient(135deg, #faad14 0%, #ff7a45 100%);
            color: white;
            border-color: transparent;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff4d4f 0%, #ff7a7a 100%);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 77, 79, 0.3);
            background: linear-gradient(135deg, #ff4d4f 0%, #ff7a7a 100%);
            border-color: transparent;
        }
        
        .btn-secondary {
            background: #bfbfbf;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background: #8c8c8c;
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(82, 196, 26, 0.1) 0%, rgba(19, 194, 194, 0.1) 100%);
            border: 2px solid #52c41a;
            border-radius: 10px;
            color: #155724;
            font-weight: 500;
        }
        
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9ff;
            transform: scale(1.01);
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table tbody td:last-child {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .text-muted {
            color: #999 !important;
            font-style: italic;
        }
        
        h2 {
            color: white;
            font-weight: 700;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .search-section {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-section input {
            flex: 1;
            min-width: 250px;
        }
        
        form.row {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
        }
        
        .form-label {
            color: #333;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1"><i class="fas fa-graduation-cap"></i> Student Management</span>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Search Form -->
            <div class="card p-4 mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or course..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="index.php" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Add Student Form -->
            <div class="card p-4 mb-4">
                <h4>Add Student</h4>
                <form method="POST" class="row g-3">
                    <input type="hidden" name="action" value="store">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="course" class="form-control" placeholder="Course" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="year_level" class="form-control" placeholder="Year Level" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">Add Record</button>
                    </div>
                </form>
            </div>

            <!-- Students Table -->
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td><?php echo htmlspecialchars($student['course']); ?></td>
                            <td><?php echo $student['year_level']; ?></td>
                            <td>
                                <a href="index.php?action=edit&id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($action === 'edit' && isset($student)): ?>
            <div class="card p-4" style="max-width: 600px;">
                <h2>Edit Student Record</h2>
                <form method="POST" class="mt-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <input type="text" name="course" class="form-control" value="<?php echo htmlspecialchars($student['course']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Year Level</label>
                        <input type="number" name="year_level" class="form-control" value="<?php echo $student['year_level']; ?>" required>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Update Record</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
