<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2>Edit Student Record</h2>
    
    <div class="card p-4 my-4">
        <form action="{{ route('students.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
            </div>

            <div class="mb-3">
                <label>Course</label>
                <input type="text" name="course" class="form-control" value="{{ $student->course }}" required>
            </div>

            <div class="mb-3">
                <label>Year Level</label>
                <input type="number" name="year_level" class="form-control" value="{{ $student->year_level }}" required>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Update Record</button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
