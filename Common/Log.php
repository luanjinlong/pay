<?php

namespace Common;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 * @package Common
 */
class Log
{
    /**
     * The underlying logger implementation.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /***
     * 日志数组
     * @var array
     */
    protected $log_arr = [];

    /**
     * Log constructor.
     * @param $name
     * @throws \Exception
     */
    public function __construct($name)
    {
        $this->logger = $this->getLog($name);
    }

    /**
     * 获取某个文件的 LOG 日志类
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    private function getLog($name)
    {
        if (in_array($name, $this->log_arr)) {
            return $this->log_arr[$name];
        }

        $this->log_arr[$name] = new \Monolog\Logger($name);
        $this->log_arr[$name]->pushHandler(new StreamHandler(BASEDIR . '/storage/logs/' . $name . '.log'));
        return $this->log_arr[$name];
    }

    /**
     * Log an emergency message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an alert message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function alert($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a critical message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function critical($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an error message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function error($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a warning message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function warning($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a notice to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function notice($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log an informational message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function info($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a debug message to the logs.
     *
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function debug($message, array $context = [])
    {
        $this->writeLog(__FUNCTION__, $message, $context);
    }

    /**
     * Log a message to the logs.
     *
     * @param  string $level
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Dynamically pass log calls into the writer.
     *
     * @param  string $level
     * @param  string $message
     * @param  array $context
     * @return void
     */
    public function write($level, $message, array $context = [])
    {
        $this->writeLog($level, $message, $context);
    }

    /**
     * Write a message to the log.
     *
     * @param  string $level
     * @param  string $message
     * @param  array $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        $this->logger->{$level}($message, $context);
    }

    /**
     * Format the parameters for the logger.
     *
     * @param  mixed $message
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        }
        return [$message];
    }

    /**
     * Dynamically proxy method calls to the underlying logger.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->logger->{$method}(...$parameters);
    }
}
