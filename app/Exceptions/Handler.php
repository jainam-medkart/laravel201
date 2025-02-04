<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use App\Http\Responses\ApiErrorResponse;
use Symfony\Component\HttpFoundation\Response;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register()
    {
        $this->renderable(function (MissingAbilityException $e, $request) {
            return ApiErrorResponse::create(new \Exception('Token does not have the required abilities'), Response::HTTP_FORBIDDEN);
        });
    }
}