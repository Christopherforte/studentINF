<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Display list (with search)
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $students = Student::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('course', 'like', "%{$search}%");
        })->get();

        return view('students.index', compact('students', 'search'));
    }

    // Store new student
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'course' => 'required',
            'year_level' => 'required|integer',
        ]);

        Student::create($request->all());

        return redirect()->back()->with('success', 'Student added successfully!');
    }

    // Show edit form
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    // Update student data
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required',
            'course' => 'required',
            'year_level' => 'required|integer',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')->with('success', 'Student updated successfully!');
    }

    // Delete student data
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->back()->with('success', 'Student deleted successfully!');
    }
}
