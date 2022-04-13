<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        // ValidationException error handler
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return $this->generateResponse($e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY, 'ValidationException');
            }
        });

        // AuthenticationException error handler
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return $this->generateResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED, 'AuthenticationException');
            }
        });

        // ModelNotFoundException error handler
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return $this->generateResponse($e->getMessage(), Response::HTTP_NOT_FOUND, 'ModelNotFoundException');
            }
        });

        // GeneralException error handler
        
        $this->renderable(function (Exception $e, $request) {
            if ($request->is('api/*')) {
                return $this->generateResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, 'GeneralException');
            }
        });
    }

    /**
     * Generate API error response
     * 
     * @param mixed $message
     * @param int   $code
     * @param string $type
     * 
     * @return \Response 
     */

    private function generateResponse($message, $code = 500, $type = 'Exception')
    {
        return response()->json([
            'type' => $type,
            'code' => $code,
            'message' => $message,
        ], $code);
    }
}
