# AirbrakeCake
A plugin to seamlessly integrate Airbrake with CakePHP 3 for errors and exceptions.

# Installation via Composer
composer require chrisshick/cakephp-airbrake

# Setup
You don't have to enable the Plugin because it uses an error handler. Therefore, all you have to do is replace this line in the 
app/Config/bootstrap.php :
```php
(new ErrorHandler(Configure::read('Error')))->register();
```
with this line:
```php
(new \chrisShick\AirbrakeCake\Error\AirbrakeHandler(Configure::consume('Error')))->register();
```

Then, set up the configuration in the app/Config/app.php file: 

```php
 'AirbrakeCake'=> [
        'apiKey'=>'<YOUR AIRBRAKE API KEY>',
        'options'=>[],
        'debugOption'=>false
  ]
```

#Configuration explained

The configuration takes the following keys: 
```php
  'apiKey', 'options', 'debugOption'
```
The apiKey is the api key that Airbrake generates for you.

The options array is the additional Airbrake parameters you want to add. You can view the additional parameters here:
[PHPAirbrake](https://github.com/dbtlr/php-airbrake#configuration-options)

The debugOption key expects a true or false value that lets you set whether or not you want to log exceptions and errors when debug is on or off.
If you want to log the errors to Airbrake when debug is on then set the debugOption key to true.
