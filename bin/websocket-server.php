<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\Stomp\Client;
use Thruway\Peer\Router;
use Thruway\Transport\RatchetTransportProvider;
use Thruway\Transport\RawSocketTransportProvider;
use Workshop\EventHandler;
use React\Stomp\Factory as StompFactory;

/** The loop is shared between multiple objects so we all use the same loop in different objects for timers etc **/
$loop = Factory::create();

/** React has a STOMP library for communicating with RabbitMQ (with it's STOMP plugin) within an event loop (async) **/
$stompFactory = new StompFactory($loop);
$client       = $stompFactory->createClient(
    [
        'host'     => 'rabbitmq',
        'port'     => 61613,
        'login'    => 'rabbitmq',
        'passcode' => 'rabbitmq',
        'vhost'    => '/'
    ]
);

$router       = new Router($loop);
$eventHandler = new EventHandler('our-namespace', $loop);

/** STOMP with RabbitMQ specifies /amqp/queue must be prefixed before an external queue name (it's in the docs) **/
/**
 * @todo Here, you should connect with the STOMP client. Then(), subscribe with aknowledgement and the callback should be
 * a function in your event handler, so everything is grouped together nicely and your business logic isn't in this
 * startup script! This will allow you to consume RabbitMQ messages async. You will need to read the docs!
 **/

/** The port we're handling websockets over **/
$router->addTransportProvider(new RatchetTransportProvider('0.0.0.0', 1338));

/** The port we're handling raw socket connections over **/
$router->registerModule(new RawSocketTransportProvider('0.0.0.0', 1339));

/** Add a Client to the router so we can register RPC endpoints **/
$router->addInternalClient($eventHandler);

$router->start();