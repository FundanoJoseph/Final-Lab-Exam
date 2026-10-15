<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\CourseModel;
use App\Models\DepartmentModel;

/**
 * Manage Department: list / view / create / update / delete.
 */
class DepartmentController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('admin');
    }

    /**
     * GET /departments – list all departments (with search).
     */
    public function index(array $params = []): void
    {
        $model = new DepartmentModel();
        $term  = (string) Request::query('q');

        $this->view('departments/index', [
            'title'       => 'Departments',
            'departments' => $model->search($term !== '' ? $term : null),
            'searchTerm'  => $term,
        ]);
    }

    /**
     * GET /departments/create – "Add New Department" form.
     */
    public function create(array $params = []): void
    {
        $this->view('departments/form', [
            'title'      => 'Add New Department',
            'department' => null,
        ]);
    }

    /**
     * POST /departments – store a new department.
     */
    public function store(array $params = []): void
    {
        $this->requirePost();

        $data = [
            'code'        => (string) Request::post('code'),
            'name'        => (string) Request::post('name'),
            'description' => (string) Request::post('description'),
        ];

        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, 'departments/create');
        }

        $model = new DepartmentModel();
        if ($model->findByCode($data['code'])) {
            \App\Core\Session::flash('error', 'A department with that code already exists.');
            \App\Core\Session::flashOldInput($data);
            $this->redirect('departments/create');
        }

        $model->create($data);
        \App\Core\Session::flash('success', 'Department added successfully.');
        $this->redirect('departments');
    }

    /**
     * GET /departments/{id} – view one department + its courses.
     */
    public function show(array $params = []): void
    {
        $model = new DepartmentModel();
        $id    = (int) ($params['id'] ?? 0);
        $dept  = $model->findById($id);

        if ($dept === null) {
            $this->notFound('Department');
        }

        $courses = (new CourseModel())->findByDepartment($id);

        $this->view('departments/view', [
            'title'      => 'Department Details',
            'department' => $dept,
            'courses'    => $courses,
        ]);
    }

    /**
     * GET /departments/{id}/edit – "Update Department" form.
     */
    public function edit(array $params = []): void
    {
        $model = new DepartmentModel();
        $dept  = $model->findById((int) ($params['id'] ?? 0));

        if ($dept === null) {
            $this->notFound('Department');
        }

        $this->view('departments/form', [
            'title'      => 'Update Department',
            'department' => $dept,
        ]);
    }

    /**
     * POST /departments/{id} – update an existing department.
     */
    public function update(array $params = []): void
    {
        $this->requirePost();

        $id   = (int) ($params['id'] ?? 0);
        $model = new DepartmentModel();
        if ($model->findById($id) === null) {
            $this->notFound('Department');
        }

        $data = [
            'code'        => (string) Request::post('code'),
            'name'        => (string) Request::post('name'),
            'description' => (string) Request::post('description'),
        ];

        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, "departments/{$id}/edit");
        }

        if ($model->findByCode($data['code'], $id)) {
            \App\Core\Session::flash('error', 'A department with that code already exists.');
            \App\Core\Session::flashOldInput($data);
            $this->redirect("departments/{$id}/edit");
        }

        $model->update($id, $data);
        \App\Core\Session::flash('success', 'Department updated successfully.');
        $this->redirect("departments/{$id}");
    }

    /**
     * POST /departments/{id}/delete – delete a department.
     */
    public function delete(array $params = []): void
    {
        $this->requirePost();

        $id    = (int) ($params['id'] ?? 0);
        $model = new DepartmentModel();
        if ($model->findById($id) === null) {
            $this->notFound('Department');
        }

        // Referential integrity: refuse while courses still belong to it.
        if ($model->courseCount($id) > 0) {
            \App\Core\Session::flash('error', 'Cannot delete: this department still has courses assigned to it.');
            $this->redirect("departments/{$id}");
        }

        $model->delete($id);
        \App\Core\Session::flash('success', 'Department deleted successfully.');
        $this->redirect('departments');
    }

    /**
     * Shared validation rules.
     */
    private function validate(array $data): Validator
    {
        $validator = new Validator($data);
        $validator
            ->required('code', 'Department code')->maxLength('code', 20)
            ->pattern('code', '/^[A-Za-z0-9\-]+$/', 'Department code may only contain letters, numbers and dashes.')
            ->required('name', 'Department name')->maxLength('name', 100)
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
