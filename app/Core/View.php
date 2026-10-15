<?php

namespace App\Core;

/**
 * View renderer – keeps presentation strictly separate from logic.
 *
 * Views live in app/Views/<name>.php and receive an extracted $data array.
 * Layout pieces (header / sidebar / footer) wrap the page content.
 */
class View
{
    /**
     * Render a view inside the application layout.
     *
     * @param string $view Dot-path below app/Views, e.g. 'students/view'
     * @param array  $data Variables made available to the view
     */
    public static function render(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        $content = self::partial($view, $data);

        if ($layout !== '') {
            echo self::partial($layout, array_merge($data, ['content' => $content]));
        } else {
            echo $content;
        }
    }

    /**
     * Render a view fragment and return the HTML (used for nested views).
     */
    public static function partial(string $view, array $data = []): string
    {
        $path = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($path)) {
            Logger::error('view.missing', ['view' => $view, 'path' => $path]);
            throw new \RuntimeException("View '{$view}' not found.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string) ob_get_clean();
    }
}
