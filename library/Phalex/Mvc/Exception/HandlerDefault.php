<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Phalex\Mvc\Exception;

use Phalex\Di\Di;
use Phalcon\Mvc\View\Simple as ViewSimple;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher\Exception as DispatchException;
use Phalcon\Mvc\View\Engine;

/**
 * Description of HandlerDefault
 *
 * @author quangtm
 */
class HandlerDefault implements HandlerInterface
{

    /**
     *
     * @var ViewSimple
     */
    protected $view;

    /**
     *
     * @var array
     */
    protected $options;

    /**
     *
     * @var Response
     */
    protected $response;

    /**
     *
     * @var Di
     */
    protected $di;

    /**
     *
     * @var string
     */
    protected $viewsDir;

    /**
     * Create error handler service
     * @param Di $di
     * @return \Phalex\Mvc\Exception\HandlerDefault
     * @throws InvalidArgumentException
     */
    public function createService(Di $di)
    {
        $required = [
            'views_dir'    => null,
            'template_500' => null,
            'template_404' => null,
        ];

        $config = $di->get('config')['error_handler']->toArray();
        $errMsg = sprintf('Cannot create error handler "%s"', __CLASS__);
        if (!isset($config['options'])) {
            throw new InvalidArgumentException($errMsg);
        }

        $options  = array_merge($required, $config['options']);
        if (empty($options['views_dir']) || empty($options['template_500']) || empty($options['template_404']) ||
                ($realPath = realpath($options['views_dir'])) === false) {
            throw new InvalidArgumentException($errMsg);
        }

        $this->options  = $options;
        $this->di       = $di;
        $this->viewsDir = $realPath;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     * @param type $errorCode
     * @param type $errorMessage
     * @param type $errorFile
     * @param type $errorLine
     */
    public function errorHandler($errorCode, $errorMessage, $errorFile, $errorLine)
    {
        $errMsg = sprintf('Error occurs in Phalex Application. "%s"', $errorMessage);
        $errMsg .= sprintf("<p>On file: %s, on line: %s</p>", $errorFile, $errorLine);
        $exc    = new RuntimeException($errMsg, $errorCode);
        $this->exceptionHandler($exc);
    }

    /**
     * @codeCoverageIgnore
     * @param \Exception $exception
     */
    public function exceptionHandler(\Exception $exception)
    {
        ob_clean();
        $view = new ViewSimple();
        $view->setViewsDir($this->viewsDir . DIRECTORY_SEPARATOR);

        $statusCode = 500;
        $message    = 'Internal Server Error';
        $template   = $this->options['template_500'];
        if ($exception instanceof DispatchException) {
            $template   = $this->options['template_404'];
            $statusCode = 404;
            $message    = 'Not Found';
        }
        $content = $view->render($template, [
            'message'  => $exception->getMessage(),
            'file'     => $exception->getFile(),
            'code'     => $exception->getCode(),
            'line'     => $exception->getLine(),
            'trace'    => $exception->getTrace(),
            'traceStr' => $exception->getTraceAsString(),
        ]);
        $response = new Response();
        $response->setContent($content)
                ->setStatusCode($statusCode, $message)
                ->send();

        exit;
    }
}
