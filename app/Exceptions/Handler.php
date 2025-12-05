<?php
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

public function render($request, Throwable $e) {
    if ($request->is('api/*') || $request->wantsJson()) {
        if ($e instanceof ValidationException) {
            return response()->json(['message'=>'Validation failed','errors'=>$e->errors()], 422);
        }
        if ($e instanceof AuthenticationException) {
            return response()->json(['message'=>'Unauthenticated'], 401);
        }
        if ($e instanceof NotFoundHttpException) {
            return response()->json(['message'=>'Not Found'], 404);
        }
        return response()->json(['message'=>$e->getMessage()], 500);
    }

    return parent::render($request, $e);
}
