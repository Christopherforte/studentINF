<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>🎓 Student Information System</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Search Form -->
    <div class="card p-4 mb-4">
        <form action="{{ route('students.index') }}" method="GET" class="row g-3 my-3">
            <div class="col-auto">
                <input type="text" name="search" class="form-control" placeholder="Search by name or course..." value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Add Student Form -->
    <div class="card p-4 mb-4">
        <h4>Add Student</h4>
        <form action="{{ route('students.store') }}" method="POST">
            @csrf
            <div class="row">
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
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Course</th>
                <th>Year Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->course }}</td>
                <td>{{ $student->year_level }}</td>
                <td>
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if(count($students) == 0)
        <p class="text-center text-muted">No students found.</p>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
