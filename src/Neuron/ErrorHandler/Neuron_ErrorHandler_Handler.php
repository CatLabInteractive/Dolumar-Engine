<?php

class Neuron_ErrorHandler_Handler
{
    /**
     * @return Neuron_ErrorHandler_Handler
     */
    public static function getInstance()
    {
        static $in;
        if (!isset($in)) {
            $in = new self();
        }
        return $in;
    }

    /**
     * Neuron_ErrorHandler_Handler constructor.
     */
    protected function __construct()
    {

    }

    /**
     * @param Exception $e
     */
    public function notify(Exception $e)
    {
        if (class_exists(\Airbrake\Instance::class)) {
            \Airbrake\Instance::notify($e);
        } else {
            error_log ((string)$e);
        }
    }
}