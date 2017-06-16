# DeskPRO PHP API Client Library

[![Build Status](https://travis-ci.org/DeskPRO/deskpro-api-client-php.svg?branch=master)](https://travis-ci.org/DeskPRO/deskpro-api-client-php)

## Requirements

* PHP 5.5+
* Guzzlehttp/guzzle >= 6.2

## Configuration

Basic parameters (min requirements):

```php
$helpdeskUrl = 'https://your-deskpro.url/';

// auth via token (format "token {person_id}:{token_string}")
$authHeader  = 'token 1:AWJ2BQ7WG589PQ6S862TCGY4';
// auth via key (format "key {person_id}:{key_code_string}")
$authHeader  = 'key 1:dev-code';

$client = new DeskPROApi($helpdeskUrl, $authHeader);
```

With api version (to support breaking changes, see http://api.deskpro.com/#api-versions-and-backwards-compatibility):

```php
// e.g. the date when you started using the API
// format YYYYMMMDD
$apiVersion = '20170605';

$client = new DeskPROApi($helpdeskUrl, $authHeader, $apiVersion);
```

Set up custom middleware:

```php
$stack = GuzzleHttp\HandlerStack;::create();
$stack->push(
    GuzzleHttp\Middleware::log(
        new Monolog\Logger('Logger'),
        new GuzzleHttp\MessageFormatter('{req_body} - {res_body}')
    )
);

$httpClient = new GuzzleHttp\Client([
    'timeout' => 60,
    'handler' => $stack,
]);

$client = new DeskPROApi($helpdeskUrl, $authHeader, $apiVersion, $httpClient);
```

## Usage

### Find request

Fetch paginated list of resource.

**$resource->find(array $criteria = [], $orderBy = null, $orderDir = null, $count = null, $page = null)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns FindRequest object
$request = $client->find();

// returns DataResponse
$result = $request->send();
```

You can also use chain request builder:

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns FindRequest object
$request = $client->find();
$request
    ->criteria([
        'agent' => 1,
    ])
    ->orderBy('person')
    ->orderDir('desc')
    ->count(10)
    ->paget(2)
;

// returns DataResponse
$result = $request->send();
```

### Get request

Get a single resource item.

**$resource->get($id)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns GetRequest object
$request = $resource->get(1);
// or short alias
$request = $client->tickets(1);

// returns DataResponse
$result = $request->send();
```

Some of resources can have related data (sub resources), to get it needs to choose resource item:

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns TicketGetRequest object (extended from basic GetRequest)
$request = $resource->get(1);

// returns FindRequest object
$request = $request->messages()->find();

// returns DataResponse
$result = $request->send();
```

### Count request

Count resource items, can be grouped by some of resource properties.

**$resource->count($groupBy)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns CountRequest object
$request = $resource->count();

// returns DataResponse
$result = $request->send();
```

### Create request

Create a new resource item.

**$resource->create(array $data)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns CreateRequest object
$request = $resource->create([
    'subject' => 'My ticket',
]);

// returns DataResponse
$result = $request->send();
```

### Update request

Update an existing resource item.

**$resource->update($id, array $data)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns UpdateRequest object
$request = $resource->update(1, [
    'subject' => 'Change ticket subject',
]);

// returns NoContentResponse
$result = $request->send();
```

Update requests return `204 NoContent` by default but you can force data response with `follow_location` query param:

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns UpdateRequest object
$request = $resource
    ->update(1, [
        'subject' => 'Change ticket subject',
    ])
    ->followLocation(true)
;

// returns DataResponse
$result = $request->send();
```

### Delete request

Delete an existing resource item.

**$resource->delete($id)**

```php
// returns TicketsResource object
$resource = $client->tickets();

// returns DeleteRequest object
$request = $resource->delete(1);

// returns NoContentResponse
$result = $request->send();
```

### Batch request

Allows to wrap several API requests and execute them within a single API request,
i.e. could be useful for app bootstrap actions to pre-load common data from the API.

**$client->batch(array $requests)**

```php
$result = $client
    ->batch([
        'people'   => $client->people()->find(),
        'tickets'  => $client->tickets()->find(),
        'articles' => $client->articles()->find(),
    ])
    ->send()
;
```

### Raw request

You can send API requests w/o specific resource wrappers using following methods:

**$client->sendGet($endpoint, array $queryParams = [])**

```php
// returns DataResponse
$result = $client->sendGet('tickets', ['count' => 10]);
```

**$client->sendPost($endpoint, $data, array $queryParams = [])**

```php
// returns DataResponse
$result = $client->sendPost('tickets', [
    'subject' => 'My ticket',
]);
```

**$client->sendPut($endpoint, $data, array $queryParams = [])**

```php
// returns NoContentResponse
$result = $client->sendPut('tickets/1', [
    'subject' => 'Modified ticket',
]);
```

**$client->sendDelete($endpoint, array $queryParams = [], $data = null)**

```php
// returns NoContentResponse
$result = $client->sendDelete('tickets/1');
```

### Side-loading

```php
$request = $client->tickets()->find()
$request->sideload(['person', 'organization']);

// returns DataResponse
$result = $request->send();
```

### Uploading files

Upload from file on file system:

```php
// returns DataResponse
$result = $client->createFile('../my_file.txt');
```

Upload from data string:

```php
// returns DataResponse
$result = $client->createFile('my_file.txt', 'file content');
```

Create a ticket message with attachments:

```php
$blob    = $client->createFile('my_file.txt', 'file_content')->getData();
$request = $client->tickets(1)->messages()->create([
    'message'     => 'My message',
    'attachments' => [
        [
            'blob_auth' => $blob['blob_auth'],
        ],
    ],
]);

// returns DataResponse
$result = $request->send();
```
