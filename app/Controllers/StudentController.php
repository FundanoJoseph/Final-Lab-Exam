<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\CourseModel;
use App\Models\StudentAcademicModel;
use App\Models\StudentModel;

/**
 * Student Information and Records Management.
 *
 * Covers: add, list, view details, academic history, update details,
 * add/edit/delete academic records, update status and full record deletion.
 */
class StudentController extends Controller
{
    public function __construct()
    {
        Auth::requireRole('admin');
    }

    /**
     * GET /students – list all students (with search).
     */
    public function index(array $params = []): void
    {
        $model = new StudentModel();
        $term  = (string) Request::query('q');

        $this->view('students/index', [
            'title'      => 'Students',
            'students'   => $model->search($term !== '' ? $term : null),
            'searchTerm' => $term,
        ]);
    }

    /**
     * GET /students/create – "Add New Student" form.
     */
    public function create(array $params = []): void
    {
        $this->view('students/form', [
            'title'      => 'Add New Student',
            'student'    => null,
            'courses'    => (new CourseModel())->all('name'),
        ]);
    }

    /**
     * POST /students – store a new student.
     */
    public function store(array $params = []): void
    {
        $this->requirePost();

        $data = $this->formData();
        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, 'students/create');
        }

        $this->assertCourseExists($data['course_id'], 'students/create', $data);

        $model = new StudentModel();
        if ($model->findByStudentNo($data['student_no'])) {
            Session::flash('error', 'A student with that student number already exists.');
            Session::flashOldInput($data);
            $this->redirect('students/create');
        }

        $id = $model->create($data);
        Session::flash('success', 'Student added successfully.');
        $this->redirect("students/{$id}");
    }

    /**
     * GET /students/{id} – view student details + academic history.
     */
    public function show(array $params = []): void
    {
        $model  = new StudentModel();
        $id     = (int) ($params['id'] ?? 0);
        $student = $model->findDetailed($id);

        if ($student === null) {
            $this->notFound('Student');
        }

        $academicModel = new StudentAcademicModel();

        $this->view('students/view', [
            'title'    => 'Student Details',
            'student'  => $student,
            'records'  => $academicModel->findByStudent($id),
            'courses'  => (new CourseModel())->search(null),
        ]);
    }

    /**
     * GET /students/{id}/edit – "Update Student Details" form.
     */
    public function edit(array $params = []): void
    {
        $model   = new StudentModel();
        $student = $model->findById((int) ($params['id'] ?? 0));

        if ($student === null) {
            $this->notFound('Student');
        }

        $this->view('students/form', [
            'title'   => 'Update Student Details',
            'student' => $student,
            'courses' => (new CourseModel())->all('name'),
        ]);
    }

    /**
     * POST /students/{id} – update student details.
     */
    public function update(array $params = []): void
    {
        $this->requirePost();

        $id    = (int) ($params['id'] ?? 0);
        $model = new StudentModel();
        if ($model->findById($id) === null) {
            $this->notFound('Student');
        }

        $data = $this->formData();
        $validator = $this->validate($data);
        if ($validator->fails()) {
            $this->failValidation($validator, "students/{$id}/edit");
        }

        $this->assertCourseExists($data['course_id'], "students/{$id}/edit", $data);

        if ($model->findByStudentNo($data['student_no'], $id)) {
            Session::flash('error', 'A student with that student number already exists.');
            Session::flashOldInput($data);
            $this->redirect("students/{$id}/edit");
        }

        $model->update($id, $data);
        Session::flash('success', 'Student details updated successfully.');
        $this->redirect("students/{$id}");
    }

    /**
     * POST /students/{id}/status – "Update Student's Status".
     */
    public function updateStatus(array $params = []): void
    {
        $this->requirePost();

        $id     = (int) ($params['id'] ?? 0);
        $model  = new StudentModel();
        $status = (string) Request::post('status');

        if ($model->findById($id) === null) {
            $this->notFound('Student');
        }

        if (!in_array($status, StudentModel::STATUSES, true)) {
            Session::flash('error', 'Please choose a valid student status.');
            $this->redirect("students/{$id}");
        }

        $model->updateStatus($id, $status);
        Session::flash('success', "Student status updated to \"{$status}\".");
        $this->redirect("students/{$id}");
    }

    /**
     * POST /students/{id}/delete – "Delete Student Information and Records".
     *
     * Academic records are removed by the database FK cascade.
     */
    public function delete(array $params = []): void
    {
        $this->requirePost();

        $id    = (int) ($params['id'] ?? 0);
        $model = new StudentModel();
        if ($model->findById($id) === null) {
            $this->notFound('Student');
        }

        $model->delete($id);
        Session::flash('success', 'Student information and records deleted successfully.');
        $this->redirect('students');
    }

    // ------------------------------------------------------------------
    //  Academic history actions
    // ------------------------------------------------------------------

    /**
     * POST /students/{id}/academics – "Add New Student Academic".
     */
    public function storeAcademic(array $params = []): void
    {
        $this->requirePost();

        $studentId = (int) ($params['id'] ?? 0);
        if ((new StudentModel())->findById($studentId) === null) {
            $this->notFound('Student');
        }

        $data = $this->academicFormData($studentId);
        $validator = $this->validateAcademic($data);
        if ($validator->fails()) {
            $this->failValidation($validator, "students/{$studentId}");
        }

        $this->assertCourseExists($data['course_id'], "students/{$studentId}", $data, true);

        (new StudentAcademicModel())->create($data);
        Session::flash('success', 'Academic record added successfully.');
        $this->redirect("students/{$studentId}");
    }

    /**
     * GET /students/{id}/academics/{aid}/edit – "Edit Student Academic".
     */
    public function editAcademic(array $params = []): void
    {
        $studentId = (int) ($params['id'] ?? 0);
        $recordId  = (int) ($params['aid'] ?? 0);

        $student = (new StudentModel())->findById($studentId);
        $record  = (new StudentAcademicModel())->findForStudent($studentId, $recordId);

        if ($student === null || $record === null) {
            $this->notFound('Academic record');
        }

        $this->view('students/academic_form', [
            'title'     => 'Edit Student Academic',
            'student'   => $student,
            'record'    => $record,
            'courses'   => (new CourseModel())->search(null),
        ]);
    }

    /**
     * POST /students/{id}/academics/{aid} – update an academic record.
     */
    public function updateAcademic(array $params = []): void
    {
        $this->requirePost();

        $studentId = (int) ($params['id'] ?? 0);
        $recordId  = (int) ($params['aid'] ?? 0);

        $academicModel = new StudentAcademicModel();
        $record = $academicModel->findForStudent($studentId, $recordId);
        if ((new StudentModel())->findById($studentId) === null || $record === null) {
            $this->notFound('Academic record');
        }

        $data = $this->academicFormData($studentId);
        $validator = $this->validateAcademic($data);
        if ($validator->fails()) {
            $this->failValidation($validator, "students/{$studentId}/academics/{$recordId}/edit");
        }

        $this->assertCourseExists($data['course_id'], "students/{$studentId}/academics/{$recordId}/edit", $data, true);

        $academicModel->update($recordId, $data);
        Session::flash('success', 'Academic record updated successfully.');
        $this->redirect("students/{$studentId}");
    }

    /**
     * POST /students/{id}/academics/{aid}/delete – "Delete Student Academic".
     */
    public function deleteAcademic(array $params = []): void
    {
        $this->requirePost();

        $studentId = (int) ($params['id'] ?? 0);
        $recordId  = (int) ($params['aid'] ?? 0);

        $academicModel = new StudentAcademicModel();
        if ($academicModel->findForStudent($studentId, $recordId) === null) {
            $this->notFound('Academic record');
        }

        $academicModel->delete($recordId);
        Session::flash('success', 'Academic record deleted successfully.');
        $this->redirect("students/{$studentId}");
    }

    // ------------------------------------------------------------------
    //  Helpers
    // ------------------------------------------------------------------

    /**
     * Referential check: the chosen course must exist (when provided).
     */
    private function assertCourseExists(?string $courseId, string $backTo, array $input, bool $required = false): void
    {
        $empty = ($courseId === null || $courseId === '');
        if ($empty && !$required) {
            return; // optional program field
        }
        if ($empty || (new \App\Models\CourseModel())->findById((int) $courseId) === null) {
            Session::flash('error', 'The selected course does not exist.');
            Session::flashOldInput($input);
            $this->redirect($backTo);
        }
    }

    /**
     * Collect + normalise student form fields.
     */
    private function formData(): array
    {
        return [
            'student_no'        => (string) Request::post('student_no'),
            'first_name'        => (string) Request::post('first_name'),
            'middle_name'       => (string) Request::post('middle_name'),
            'last_name'         => (string) Request::post('last_name'),
            'gender'            => (string) Request::post('gender'),
            'civil_status'      => (string) Request::post('civil_status'),
            'date_of_birth'     => (string) Request::post('date_of_birth'),
            'contact_no'        => (string) Request::post('contact_no'),
            'email'             => (string) Request::post('email'),
            'present_address'   => (string) Request::post('present_address'),
            'permanent_address' => (string) Request::post('permanent_address'),
            'course_id'         => Request::post('course_id') !== '' ? (string) Request::post('course_id') : null,
            'status'            => (string) Request::post('status'),
        ];
    }

    /**
     * Shared student validation rules.
     */
    private function validate(array $data): Validator
    {
        $validator = new Validator($data);
        $validator
            ->required('student_no', 'Student number')->maxLength('student_no', 30)
            ->pattern('student_no', '/^[A-Za-z0-9\-]+$/', 'Student number may only contain letters, numbers and dashes.')
            ->required('first_name', 'First name')->maxLength('first_name', 50)
            ->maxLength('middle_name', 50)
            ->required('last_name', 'Last name')->maxLength('last_name', 50)
            ->required('gender', 'Gender')->in('gender', ['Male', 'Female'], 'Gender')
            ->maxLength('civil_status', 20)
            ->in('civil_status', ['Single', 'Married', 'Widowed', 'Separated'], 'Civil status')
            ->required('date_of_birth', 'Date of birth')->date('date_of_birth', 'Date of birth')
            ->maxLength('contact_no', 20)
            ->email('email', 'Email address')->maxLength('email', 100)
            ->maxLength('present_address', 255)
            ->maxLength('permanent_address', 255)
            ->in('status', StudentModel::STATUSES, 'Status');
        return $validator;
    }

    /**
     * Collect + normalise academic-record form fields.
     */
    private function academicFormData(int $studentId): array
    {
        return [
            'student_id'   => (string) $studentId,
            'course_id'    => (string) Request::post('course_id'),
            'semester'     => (string) Request::post('semester'),
            'school_year'  => (string) Request::post('school_year'),
            'year_level'   => (string) Request::post('year_level'),
            'beg_status'   => (string) Request::post('beg_status'),
            'end_status'   => (string) Request::post('end_status'),
        ];
    }

    /**
     * Shared academic-record validation rules.
     */
    private function validateAcademic(array $data): Validator
    {
        $validator = new Validator($data);
        $validator
            ->required('course_id', 'Department / Course')->integer('course_id', 'Department / Course')
            ->required('semester', 'Semester')->in('semester', StudentAcademicModel::SEMESTERS, 'Semester')
            ->required('school_year', 'School year')
            ->pattern('school_year', '/^\d{4}-\d{4}$/', 'School year must look like 2025-2026.')
            ->required('year_level', 'Year level')->in('year_level', StudentAcademicModel::YEAR_LEVELS, 'Year level')
            ->required('beg_status', 'Beginning of semester status')->in('beg_status', StudentAcademicModel::BEG_STATUSES, 'Beginning of semester status')
            ->required('end_status', 'End of semester status')->in('end_status', StudentAcademicModel::END_STATUSES, 'End of semester status');
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
