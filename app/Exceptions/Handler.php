<?php
namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];
    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {
            if ($request->acceptsJson() && false) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'You are not Authorized to perform this Action.',
                        'data' => $request->acceptsJson(),
                    ],200,
                );
            } else {
                return redirect()->route('welcome');
            }
        } elseif ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Opps! could not find this Record.',
            ]);
        } elseif (false && $exception instanceof \Illuminate\Contracts\Encryption\DecryptException) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Opps! We Failed to Decrypt the Id You Sent.',
                    'segment' => request()->segment(4),
                    'expeption' => $exception,
                ],
                403,
            );
        }
        return parent::render($request, $exception);
    }
}
