<?php

require_once __DIR__ . '/../vendor/autoload.php';

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "VXD22AD9kcNyNgsfW6cwkWRkw");
define("TWITTER_CONSUMER_SECRET", "y0k3z9Y46V0DMAKGe4Az2aDtqNt9aXjg3ssCMCldUheGBT0YL9");
// The OAuth data for the twitter account
define("OAUTH_TOKEN", "3232926711-kvMvNK5mFJlUFzCdtw3ryuwZfhIbLJtPX9e8E3Y");
define("OAUTH_SECRET", "EYrFp0lfNajBslYV3WgAGmpHqYZvvNxP5uxxSq8Dbs1wa");

class TweetFilterConsumer extends OauthPhirehose
{
    // @todo You fill this in
}

/**
 * Connecting to RabbitMQ correctly has been done for you. You publish() on an exchange
 *
 * @return \AMQPExchange
 */
function createExchange(): \AMQPExchange
{
    $connection = new \AMQPConnection(
        [
            'host'     => 'rabbitmq',
            'port'     => 5672,
            'login'    => 'rabbitmq',
            'password' => 'rabbitmq',
            'vhost'    => '/'
        ]
    );

    $connection->connect();

    $channel = new \AMQPChannel($connection);

    $exchange = new \AMQPExchange($channel);
    $exchange->setName('twitter_exchange');
    $exchange->setType(AMQP_EX_TYPE_DIRECT);
    $exchange->declareExchange();

    $queue = new \AMQPQueue($channel);
    $queue->setName('twitter_queue');
    $queue->setArgument('x-max-length', 10); // messages will be dropped or dead-lettered to make space for new ones
    $queue->setFlags(AMQP_DURABLE);
    $queue->declareQueue();

    $queue->bind('twitter_exchange');

    return $exchange;
}

/** Start streaming with Phirehose **/
$sc = new TweetFilterConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_FILTER);
$sc->setTrack(['morning', 'goodnight', 'hello']);
$sc->consume();