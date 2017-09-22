<?php

namespace Workshop;

use React\EventLoop\LoopInterface;
use Thruway\ClientSession;
use Thruway\Peer\Client;

/**
 * @package Workshop
 *
 * @todo In a Client, you can register new RPC endpoints, but you'll need to use the correct method where you have access to a session for this
 */
class EventHandler extends Client
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @inheritdoc
     */
    public function __construct($realm, LoopInterface $loop)
    {
        $this->loop = $loop;

        parent::__construct($realm, $loop);
    }
}