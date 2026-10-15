<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\DepartmentModel;
use App\Models\StudentAcademicModel;
use App\Models\StudentModel;
use App\Models\CourseModel;

/**
 * Dashboard – summary of the stored lists.
 */
class DashboardController extends Controller
{
    /**
     * GET / – dashboard home.
     */
    public function index(array $params = []): void
    {
        Auth::requireLogin();

        $departments = new DepartmentModel();
        $courses     = new CourseModel();
        $students    = new StudentModel();
        $academics   = new StudentAcademicModel();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'counts' => [
                'departments' => $departments->count(),
                'courses'     => $courses->count(),
                'students'    => $students->count(),
                'active'      => $students->countByStatus('Active'),
                'academics'   => $academics->countAll(),
            ],
            'recentStudents' => $students->recent(5),
        ]);
    }
}
