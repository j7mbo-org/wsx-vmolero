<?php

require_once __DIR__ . '/../vendor/autoload.php';

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Thruway\Transport\RawSocketClientTransportProvider;

if (!isset($argv[1])) {
    die('You must pass an argument to the script, ie: php bin/change-filter.php #heavyMetal' . PHP_EOL);
}

$filterName = $argv[1];

$loop = Factory::create();

$client = new class('our-namespace', $loop, $filterName) extends \Thruway\Peer\Client
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var string
     */
    private $filterName;

    /**
     * @inheritdoc
     *
     * @param string $filterName
     */
    public function __construct($realm, $loop, string $filterName) {

        $this->loop       = $loop;
        $this->filterName = $filterName;

        parent::__construct($realm, $loop);
    }

    /**
     * @inheritdoc
     */
    public function onSessionStart($session, $transport)
    {
        $session->call('rpc.terminal.send.filter', [], ['filter-name' => $this->filterName], ['acknowledge' => true])
            ->then(function() use ($session) {
            /** Stop the loop (otherwise the script will keep running forever) **/
            $session->close();
            $this->loop->stop();
        });
    }
};

/** How our client interacts with others (in this case, a raw socket!) **/
$client->addTransportProvider(new RawSocketClientTransportProvider('localhost', 1339));

$client->start();

