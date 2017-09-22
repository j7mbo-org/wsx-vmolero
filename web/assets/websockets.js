var connection = new autobahn.Connection({url: 'ws://localhost:1338', realm: 'our-namespace'});

connection.onopen = function (session) {

    console.log('SESSION OPENED');

    /** @todo Subscribe to all twitter-based data **/


    /** @todo change-filter.php calls an RPC endpoint. Take it, then call() the RPC endpoint in your event handler
     * so you're effectively forwarding the RPC call. **/
};

connection.open();