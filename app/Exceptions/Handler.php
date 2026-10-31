<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): Response
    {
        if ($this->isApiRequest($request)) {
            if ($e instanceof ValidationException) {
                return $this->invalidJson($request, $e);
            }

            if ($e instanceof AuthenticationException) {
                return $this->unauthenticated($request, $e);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return $this->apiError('Khong tim thay du lieu.', 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->apiError('Phuong thuc khong duoc ho tro.', 405);
            }

            if ($e instanceof HttpExceptionInterface) {
                return $this->apiError($this->apiMessage($e->getStatusCode()), $e->getStatusCode());
            }

            return $this->apiError('Co loi xay ra. Vui long thu lai sau.', 500);
        }

        return parent::render($request, $e);
    }

    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Du lieu khong hop le',
            'errors' => $exception->errors(),
            'data' => null,
            'meta' => (object) [],
            'links' => (object) [],
        ], $exception->status);
    }

    /**
     * Chưa đăng nhập: API trả JSON 401, trang web chuyển về trang đăng nhập.
     * (Laravel gọi hàm này cho mọi request, kể cả qua parent::render.)
     */
    protected function unauthenticated($request, AuthenticationException $exception): Response
    {
        if (!$this->isApiRequest($request)) {
            return redirect()->guest($exception->redirectTo() ?? route('admin.login'))
                ->with('error', 'Vui lòng đăng nhập.');
        }

        return response()->json([
            'success' => false,
            'message' => 'Chua dang nhap hoac token khong hop le',
            'errors' => [],
            'data' => null,
            'meta' => (object) [],
            'links' => (object) [],
        ], 401);
    }

    private function isApiRequest($request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }

    private function apiError(string $message, int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => [],
            'data' => null,
            'meta' => (object) [],
            'links' => (object) [],
        ], $status);
    }

    private function apiMessage(int $status): string
    {
        return match ($status) {
            400 => 'Yeu cau khong hop le.',
            401 => 'Chua dang nhap hoac token khong hop le.',
            403 => 'Ban khong co quyen thuc hien thao tac nay.',
            404 => 'Khong tim thay du lieu.',
            405 => 'Phuong thuc khong duoc ho tro.',
            429 => 'Ban thao tac qua nhanh. Vui long thu lai sau.',
            default => 'Co loi xay ra. Vui long thu lai sau.',
        };
    }
}
