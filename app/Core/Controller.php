<?php

namespace App\Core;

/**
 * Base controller.
 *
 * Provides the small set of tools every controller needs:
 * rendering views, redirects, POST/CSRF enforcement and
 * validation-error handling.
 */
abstract class Controller
{
    /**
     * Render a view inside the main layout.
     */
    protected function view(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Redirect (and stop) the current request.
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Ensure the request is a POST, protected by a valid CSRF token.
     * Called at the top of every state-changing action.
     */
    protected function requirePost(): void
    {
        if (!Request::isPost()) {
            http_response_code(405);
            $this->view('errors/403', [
                'title'   => 'Method Not Allowed',
                'message' => 'This action can only be performed through a form submission.',
            ]);
            exit;
        }
        Csrf::verifyRequest();
    }

    /**
     * Handle a failed validation: flash errors + old input and go back.
     */
    protected function failValidation(Validator $validator, string $backTo): void
    {
        foreach ($validator->messages() as $message) {
            Session::flash('error', $message);
        }
        Session::flashOldInput($_POST);
        Logger::debug('validation.failed', ['errors' => $validator->errors(), 'to' => $backTo]);
        $this->redirect($backTo);
    }
}
