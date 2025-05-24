<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\Response as LaravelResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //TODO: not now
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $authenticationException, Request $request){
            return response()->json([
                'status' => LaravelResponse::HTTP_FORBIDDEN,
                'message' => $authenticationException->getMessage()
            ]);
        });

        $exceptions->render(function (UploadException $uploadException, Request $request){
            return response()->json([
                'status' => LaravelResponse::HTTP_REQUEST_TIMEOUT,
                'message' => $uploadException->getMessage()
            ]);
        });
        $exceptions->render(function (UnauthorizedException $e, Request $request){
            return response()->json([
                'status' => LaravelResponse::HTTP_FORBIDDEN,
                'message' => 'Permission Denied!'
            ]);
        });
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if($e->getPrevious() instanceof RecordsNotFoundException) {
                return response()->json([
                    'status' => LaravelResponse::HTTP_NOT_FOUND,
                    'message' => $e->getPrevious()->getMessage(),
                ]);
            }
            return response()->json([
                'status' => LaravelResponse::HTTP_NOT_FOUND,
                'message' => 'Not Found',
            ]);
        });
    })->create();
