<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;

/**
 * Account settings (change password).
 */
class AccountController extends Controller
{
    /**
     * GET /settings – account page with the change-password form.
     */
    public function index(array $params = []): void
    {
        Auth::requireLogin();
        $this->view('account/settings', ['title' => 'Settings']);
    }

    /**
     * POST /settings/password – change the current user's password.
     */
    public function changePassword(array $params = []): void
    {
        Auth::requireLogin();
        $this->requirePost();

        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        $result = Auth::changePassword($current, $new, $confirm);

        Session::flash($result['success'] ? 'success' : 'error', $result['message']);
        $this->redirect('settings');
    }
}
