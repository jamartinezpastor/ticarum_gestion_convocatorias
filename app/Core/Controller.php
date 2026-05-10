<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (!is_file($viewPath)) {
            throw new \RuntimeException("Vista no encontrada: {$view}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layout.php';
    }

    # 'See other' por GET
    protected function redirect(string $url, int $status = 303): never
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    protected function notFound(string $message = 'Recurso no encontrado.'): void
    {
        http_response_code(404);
        $this->render('error', ['title' => '404', 'message' => $message]);
    }
}