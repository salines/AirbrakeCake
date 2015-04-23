<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 4/23/2015
 * Time: 10:54 AM
 */
namespace chrisShick\AirbrakeCake\Error;
namespace App\Error;

use Cake\Core\Configure;
use Cake\Error\ErrorHandler;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Airbrake\Configuration as AirbrakeConfiguration;
use Airbrake\Client as AirbrakeClient;
use Airbrake\Notice as AirbrakeNotice;


class AirbrakeHandler extends ErrorHandler
{
    protected $_airbrake;

    /**
     * Constructor
     *
     * @param array $options The options for error handling.
     */
    public function __construct($options = [])
    {
        $options['AirbrakeCake.apiKey'] = Configure::read('AirbrakeCake.apiKey');
        $options['AirbrakeCake.options'] = Configure::read('AirbrakeCake.options');
        parent::__construct($options);
        
        $this->_airbrake = $this->getAirbrake();
    }

    /**
     * Creates a new Airbrake instance, or returns an instance created earlier.
     * You can pass options to Airbrake\Configuration by setting the AirbrakeCake.options
     * configuration property.
     *
     * For example to set the environment name:
     *
     * ```
     * Configure::write('AirbrakeCake.options', array(
     * 	'environmentName' => 'staging'
     * ));
     * ```
     *
     * @return Airbrake\Client
     */
    public function getAirbrake()
    {
        if (empty($this->_airbrake)) {
            $apiKey = $this->_options['AirbrakeCake.apiKey'];
            $options = $this->_options['AirbrakeCake.options'];
            if (!$options) {
                $options = array();
            }
            $request = Router::getRequest();
            if ($request) {
                $options['component'] = $request->params['controller'];
                $options['action'] = $request->params['action'];
            }
            $session = $request->session();
            if (!empty($session)) {
                $options['extraParameters'] = Hash::get($options, 'extraParameters', array());
                $options['extraParameters']['User']['id'] = $session->read('Auth.User.id');
            }
            $config = new AirbrakeConfiguration($apiKey, $options);
            return new AirbrakeClient($config);
        }
        return $this->_airbrake;
    }

    /**
     * {@inheritDoc}
     */
    public function handleError($code, $description, $file = null, $line = null)
    {
        list($error) = self::mapErrorCode($code);
        $backtrace = debug_backtrace();
        if (count($backtrace) > 1) {
            array_shift($backtrace);
        }
        $notice = new AirbrakeNotice();
        $notice->load(array(
            'errorClass' => $error,
            'backtrace' => $backtrace,
            'errorMessage' => $description,
            'extraParams' => null
        ));

        $this->_airbrake->notify($notice);
        return parent::handleError($code, $description, $file, $line);
    }

    /**
     * {@inheritDoc}
     */
    public function handleException(\Exception $exception)
    {
        $this->_airbrake->notifyOnException($exception);
        parent::handleException($exception);
    }
}