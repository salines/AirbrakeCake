<?php
/**
 * Bootstraps the Airbrake plugin.
 * Before loading the plugin, please set the required API key:
 *
 * Configure::write('AirbrakeCake.apiKey', '<API KEY>');
 */
use Cake\Core\Configure;
/**
 * Sets the ErrorHandler and ExceptionHandler to
 * AirbrakeError.
 */
Configure::write('Error', array(
    'handler' => 'AirbrakeHandler::handleError',
    'level' => E_ALL & ~E_DEPRECATED,
    'trace' => true
));
Configure::write('Exception', array(
    'handler' => 'AirbrakeHandler::handleException',
    'renderer' => 'ExceptionRenderer',
    'log' => true
));