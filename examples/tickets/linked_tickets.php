<?php

require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/../config.php';

use DeskPROClient\Api\DeskPROApi;

$client = new DeskPROApi($helpdeskUrl, $authHeader);

// create linked ticket
$result = $client->sendPost('/tickets/1/links', [
    'link_ticket'  => 2,
    'parent'       => true,
]);

// delete linked ticket
$result = $client->sendDelete('/tickets/1/links', [], [
    'link_ticket'  => 2,
    'link_type'    => 'child',
]);
