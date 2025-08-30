<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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

        $this->renderable(function (Throwable $e, $request) {
            // Only handle API / JSON requests
            if ($request->expectsJson()) {
                $status = 500;

                // If it's an HTTP exception (like 404, 401, 403), use its status code
                if ($e instanceof HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $status);
            }
        });

        // $this->renderable(function (Throwable $e) {
        //     return response(['error' => $e->getMessage()], $e->getCode() ?: 400);
        // });
    }
}
