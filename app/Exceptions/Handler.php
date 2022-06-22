<?php

namespace App\Exceptions;

use App\Exceptions\Auth\RefreshTokenExpiredException;
use App\Exceptions\Auth\RefreshTokenNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && $this->shouldReport($e) && env('APP_ENV') == 'production') {
                app('sentry')->captureException($e);
            }
        });
    }

    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    public function render($request, $e)
    {
        // Validation Errors
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'Bad request',
                'message' => $e->errors(),
                'error_code' => -1100
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($e instanceof BadRequestException) {
            if ($e->getCode()) {
                return response()->json([
                    'error_code' => $e->getCode(),
                ], 401);
            } else {
                return response()->json([
                    'error' => 'Bad request',
                    'message' => $e->getMessage(),
                    'error_code' => -1400
                ], 400);
            }
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Entry for ' . str_replace('App\\', '', $e->getModel()) . ' not found',
                'error_code' => -1404
            ], 404);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Resource not found.',
                'error_code' => -1404
            ], 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'Method not allowed',
                'error_code' => -1405
            ], 405);
        }

        if ($e instanceof ForbiddenPermissionException) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => -1403
            ], $e->getCode());
        }

        if ($e instanceof QueryException) {
            $status = 500;
            $error = 'Internal Server Error';
            $message = $e->getMessage();
            if ($e->getCode() === '23000') {
                $status = 409;
                $error = 'Conflict';
                $message = $e->errorInfo[2];
            }
            return response()->json([
                'status' => $status,
                'error' => $error,
                'message' => $message,
            ], $status);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => -1401
            ], 401);
        }

        if ($e instanceof UserAlreadyActiveException) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => -1202,
            ], $e->getCode());
        }

        if ($e instanceof RefreshTokenNotFoundException) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => -1207
            ], $e->getCode());
        }

        if ($e instanceof RefreshTokenExpiredException) {
            return response()->json([
                'message' => $e->getMessage(),
                'error_code' => -1207,
            ], $e->getCode());
        }

        if ($e instanceof \Exception) {
            return response()->json([
                'error' => 'Internal Server Error',
                'body' => $e->getMessage(),
            ], 500);
        }

        return parent::render($request, $e);
    }
}
