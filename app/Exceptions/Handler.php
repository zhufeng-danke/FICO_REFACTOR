<?php namespace App\Exceptions;

use Exception;
use ErrorMessageException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        TokenMismatchException::class,
        NotFoundHttpException::class,
        ErrorMessageException::class,

        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * 在 CLI 模式下仍然报告的异常, 优先级高于上方 $dontReport
     * 
     * @var array
     */
    private $reportInCli = [
        ErrorMessageException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * 以下情况不通知
     *  - 非线上
     *  - 上面列表里的异常
     *  - 定义过常量 `ERROR_NOT_REPORT`
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        if (defined('ERROR_NOT_REPORT')) {
            return;
        }

        if (isProduction() && ($this->shouldReport($e) || $this->reportInCli($e))) {
            parent::report($e);
        }
    }

    private function reportInCli(Exception $e)
    {
        return PHP_SAPI === 'cli' && in_array(get_class($e), $this->reportInCli);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if($e instanceof NotFoundHttpException){
            return redirect('http://www.dankegongyu.com/404');
        }

        // 系统预定义的错误信息提示。
        if ($e instanceof \ErrorMessageException) {
            return $e->response();
        }

        return isProduction()
            ? $this->renderInProduction($e)
            : $this->renderExceptionWithWhoops($e);
    }

    private function renderInProduction(Exception $e)
    {
        // 支付相关的报错
        if ($e instanceof \Pingpp\Error\Base) {
            return response()->view('errors.message', [
                'message' => '支付系统繁忙，请稍后再试。',
                'code' => 202,
            ], 202);
        }

        // Http Exception
        if ($e instanceof HttpException) {
            $view = 'errors.' . $e->getStatusCode();
            if (view()->exists($view)) {
                return response()->view($view, [], $e->getStatusCode());
            }
        }

        return response()->view('errors.500', [], 500);
    }

    /**
     * Render an exception using Whoops.
     *
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    private function renderExceptionWithWhoops(Exception $e)
    {
        $handler = new \Whoops\Handler\PrettyPageHandler();
        $handler->setEditor('phpstorm');

        $whoops = new \Whoops\Run;
        $whoops->pushHandler($handler);

        return new \Illuminate\Http\Response(
            $whoops->handleException($e),
            500
        );
    }
}
