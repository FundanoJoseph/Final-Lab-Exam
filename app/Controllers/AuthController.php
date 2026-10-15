<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;

/**
 * Login / logout controller.
 */
class AuthController extends Controller
{
    /**
     * GET /login – show the login form.
     */
    public function index(array $params = []): void
    {
        if (Auth::check()) {
            $this->redirect('');
        }
        $this->view('auth/login', ['title' => 'Sign In'], 'layout/guest');
    }

    /**
     * POST /login – verify credentials.
     */
    public function login(array $params = []): void
    {
        $this->requirePost();

        $username = (string) Request::post('username');
        $password = (string) ($_POST['password'] ?? ''); // never sanitise passwords

        $validator = new Validator(['username' => $username]);
        $validator->required('username', 'Username');
        if ($validator->fails()) {
            $this->failValidation($validator, 'login');
        }

        $result = Auth::attempt($username, $password);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            Session::flashOldInput(['username' => $username]);
            $this->redirect('login');
        }

        Session::flash('success', $result['message']);
        $this->redirect('');
    }

    /**
     * POST /logout – destroy the session.
     */
    public function logout(array $params = []): void
    {
        $this->requirePost();
        Auth::logout();
        Session::flash('success', 'You have been logged out successfully.');
        $this->redirect('login');
    }
}
