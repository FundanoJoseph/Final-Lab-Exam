<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\CourseModel;
use App\Models\DepartmentModel;

/**
 * Course Management: list / view / create / update / delete.
 */
class CourseController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('admin');
    }

    /**
     * GET /courses – list all courses (with search).
     */
    public function index(array $params = []): void
    {
        $model = new CourseModel();
        $term  = (string) Request::query('q');

        $this->view('courses/index', [
            'title'      => 'Courses',
            'courses'    => $model->search($term !== '' ? $term : null),
            'searchTerm' => $term,
        ]);
    }

    /**
     * GET /courses/create – "Add New Course" form.
     */
    public function create(array $params = []): void
    {
        $this->view('courses/form', [
            'title'        => 'Add New Course',
            'course'       => null,
            'departments'  => (new DepartmentModel())->all('name'),
        ]);
    }

    /**
     * POST /courses – store a new course.
     */
    public function store(array $params = []): void
    {
        $this->requirePost();

        $data = $this->formData();
        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, 'courses/create');
        }

        // Referential check: the chosen department must exist.
        if ((new DepartmentModel())->findById((int) $data['department_id']) === null) {
            Session::flash('error', 'The selected department does not exist.');
            Session::flashOldInput($data);
            $this->redirect('courses/create');
        }

        $model = new CourseModel();
        if ($model->findByCode($data['code'])) {
            Session::flash('error', 'A course with that code already exists.');
            Session::flashOldInput($data);
            $this->redirect('courses/create');
        }

        $model->create($data);
        Session::flash('success', 'Course added successfully.');
        $this->redirect('courses');
    }

    /**
     * GET /courses/{id} – view course details.
     */
    public function show(array $params = []): void
    {
        $model = new CourseModel();
        $course = $model->findWithDepartment((int) ($params['id'] ?? 0));

        if ($course === null) {
            $this->notFound('Course');
        }

        $this->view('courses/view', [
            'title'  => 'Course Details',
            'course' => $course,
            'studentCount'    => $model->studentCount((int) $course['id']),
            'academicCount'   => $model->academicCount((int) $course['id']),
        ]);
    }

    /**
     * GET /courses/{id}/edit – "Update Course Details" form.
     */
    public function edit(array $params = []): void
    {
        $model = new CourseModel();
        $course = $model->findById((int) ($params['id'] ?? 0));

        if ($course === null) {
            $this->notFound('Course');
        }

        $this->view('courses/form', [
            'title'        => 'Update Course Details',
            'course'       => $course,
            'departments'  => (new DepartmentModel())->all('name'),
        ]);
    }

    /**
     * POST /courses/{id} – update course details.
     */
    public function update(array $params = []): void
    {
        $this->requirePost();

        $id    = (int) ($params['id'] ?? 0);
        $model = new CourseModel();
        if ($model->findById($id) === null) {
            $this->notFound('Course');
        }

        $data = $this->formData();
        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, "courses/{$id}/edit");
        }

        // Referential check: the chosen department must exist.
        if ((new DepartmentModel())->findById((int) $data['department_id']) === null) {
            Session::flash('error', 'The selected department does not exist.');
            Session::flashOldInput($data);
            $this->redirect("courses/{$id}/edit");
        }

        if ($model->findByCode($data['code'], $id)) {
            Session::flash('error', 'A course with that code already exists.');
            Session::flashOldInput($data);
            $this->redirect("courses/{$id}/edit");
        }

        $model->update($id, $data);
        Session::flash('success', 'Course updated successfully.');
        $this->redirect("courses/{$id}");
    }

    /**
     * POST /courses/{id}/delete – "Delete Course Details".
     */
    public function delete(array $params = []): void
    {
        $this->requirePost();

        $id    = (int) ($params['id'] ?? 0);
        $model = new CourseModel();
        if ($model->findById($id) === null) {
            $this->notFound('Course');
        }

        // Referential integrity: refuse while students/records reference it.
        if ($model->studentCount($id) > 0 || $model->academicCount($id) > 0) {
            Session::flash('error', 'Cannot delete: students or academic records still reference this course.');
            $this->redirect("courses/{$id}");
        }

        $model->delete($id);
        Session::flash('success', 'Course deleted successfully.');
        $this->redirect('courses');
    }

    /**
     * Collect + normalise the form fields.
     */
    private function formData(): array
    {
        return [
            'department_id'  => (string) Request::post('department_id'),
            'code'           => (string) Request::post('code'),
            'name'           => (string) Request::post('name'),
            'description'    => (string) Request::post('description'),
        ];
    }

    /**
     * Shared validation rules.
     */
    private function validate(array $data): Validator
    {
        $validator = new Validator($data);
        $validator
            ->required('department_id', 'Department')->integer('department_id', 'Department')
            ->required('code', 'Course code')->maxLength('code', 20)
            ->pattern('code', '/^[A-Za-z0-9\-]+$/', 'Course code may only contain letters, numbers and dashes.')
            ->required('name', 'Course name')->maxLength('name', 120)
            ->maxLength('description', 255);
        return $validator;
    }

    /**
     * Render the 404 page for a missing record.
     */
    private function notFound(string $what): void
    {
        http_response_code(404);
        $this->view('errors/404', [
            'title'   => "{$what} Not Found",
            'message' => "The requested {$what} record does not exist (or was already deleted).",
        ]);
        exit;
    }
}
