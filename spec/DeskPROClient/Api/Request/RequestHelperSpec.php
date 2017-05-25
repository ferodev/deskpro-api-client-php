<?php

/*
 * DeskPRO (r) has been developed by DeskPRO Ltd. https://www.deskpro.com/
 * a British company located in London, England.
 *
 * All source code and content Copyright (c) 2017, DeskPRO Ltd.
 *
 * The license agreement under which this software is released
 * can be found at https://www.deskpro.com/eula/
 *
 * By using this software, you acknowledge having read the license
 * and agree to be bound thereby.
 *
 * Please note that DeskPRO is not free software. We release the full
 * source code for our software because we trust our users to pay us for
 * the huge investment in time and energy that has gone into both creating
 * this software and supporting our customers. By providing the source code
 * we preserve our customers' ability to modify, audit and learn from our
 * work. We have been developing DeskPRO since 2001, please help us make it
 * another decade.
 *
 * Like the work you see? Think you could make it better? We are always
 * looking for great developers to join us: http://www.deskpro.com/jobs/
 *
 * ~ Thanks, Everyone at Team DeskPRO
 */

namespace spec\DeskPROClient\Api\Request;

use DeskPROClient\Api\Exception\AuthException;
use DeskPROClient\Api\Exception\BadRequestException;
use DeskPROClient\Api\Exception\ConnectException;
use DeskPROClient\Api\Exception\NoPermissionException;
use DeskPROClient\Api\Exception\NotFoundException;
use DeskPROClient\Api\Exception\RateLimitException;
use DeskPROClient\Api\Request\RequestContext;
use DeskPROClient\Api\Request\RequestDataInterface;
use DeskPROClient\Api\Request\RequestInterface;
use DeskPROClient\Api\Response\BatchResponse;
use DeskPROClient\Api\Response\DataResponse;
use DeskPROClient\Api\Response\ErrorResponse;
use DeskPROClient\Api\Response\NoContentResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;

/**
 * @mixin \DeskPROClient\Api\Request\RequestHelper
 */
class RequestHelperSpec extends ObjectBehavior
{
    public function let(
        RequestContext  $context,
        Client          $httpClient,
        Response        $httpResponse,
        StreamInterface $responseBody
    ) {
        $context->getAuthHeader()->willReturn('my_token');
        $context->getHttpClient()->willReturn($httpClient);

        $httpResponse->getBody()->willReturn($responseBody);
    }

    public function it_returns_no_content_response(
        RequestContext       $context,
        RequestDataInterface $request,
        Client               $httpClient,
        Response             $httpResponse,
        StreamInterface      $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(NoContentResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getData()->willReturn([
            'param' => 'value',
        ]);
        $request->getMethod()->willReturn('POST');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'POST',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
                'json' => [
                    'param' => 'value',
                ],
            ]
        )->willReturn($httpResponse);

        $httpResponse->getStatusCode()->willReturn(204);
        $responseBody->getContents()->willReturn(null);

        self::sendRequest($request);
    }

    public function it_returns_data_response(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(DataResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willReturn($httpResponse);

        $httpResponse->getStatusCode()->willReturn(200);
        $responseBody->getContents()->willReturn(json_encode([
            'data' => [
                'id' => 1,
            ],
            'meta' => [
                'count' => 100,
            ],
            'linked' => [
                'person' => [
                    [
                        'id' => 2,
                    ],
                ],
            ],
        ]));

        $response = self::sendRequest($request);
        $response->getData()->shouldReturn([
            'id' => 1,
        ]);
        $response->getMeta()->shouldReturn([
            'count' => 100,
        ]);
        $response->getLinked()->shouldReturn([
            'person' => [
                [
                    'id' => 2,
                ],
            ],
        ]);
    }

    public function it_returns_batch_response(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(BatchResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willReturn($httpResponse);

        $httpResponse->getStatusCode()->willReturn(200);
        $responseBody->getContents()->willReturn(json_encode([
            'responses' => [
                [
                    'data' => [
                        'id' => 1,
                    ],
                    'meta' => [
                        'count' => 100,
                    ],
                    'linked' => [
                        'person' => [
                            [
                                'id' => 2,
                            ],
                        ],
                    ],
                    'headers' => [
                        'Content-Type'   => 'application/json',
                        'Content-Length' => 100,
                    ],
                ],
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ],
                [
                    'status'  => 400,
                    'code'    => 'invalid_input',
                    'message' => 'Request input is invalid.',
                    'errors'  => [
                        'fields' => [
                            'person' => [
                                'errors' => [
                                    'code'    => 'bad_choice',
                                    'message' => 'One or more of the given values is invalid.',
                                ],
                            ],
                        ],
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ],
            ],
        ]));

        $response = self::sendRequest($request);

        // data response
        $response->getResponses()[0]->shouldBeAnInstanceOf(DataResponse::class);
        $response->getResponses()[0]->getData([
            'id' => 1,
        ]);
        $response->getResponses()[0]->getMeta([
            'count' => 100,
        ]);
        $response->getResponses()[0]->getLinked([
            'person' => [
                [
                    'id' => 2,
                ],
            ],
        ]);
        $response->getResponses()[0]->getHeaders()->shouldReturn([
            'Content-Type'   => 'application/json',
            'Content-Length' => 100,
        ]);

        // no content response
        $response->getResponses()[1]->shouldBeAnInstanceOf(NoContentResponse::class);
        $response->getResponses()[1]->getHeaders()->shouldReturn([
            'Content-Type' => 'application/json',
        ]);

        // error response
        $response->getResponses()[2]->shouldBeAnInstanceOf(ErrorResponse::class);
        $response->getResponses()[2]->getHeaders()->shouldReturn([
            'Content-Type' => 'application/json',
        ]);
        $response->getResponses()[2]->getStatusCode()->shouldReturn(400);
        $response->getResponses()[2]->getCode()->shouldReturn('invalid_input');
        $response->getResponses()[2]->getMessage()->shouldReturn('Request input is invalid.');
        $response->getResponses()[2]->getErrors()->shouldReturn([
            'fields' => [
                'person' => [
                    'errors' => [
                        'code'    => 'bad_choice',
                        'message' => 'One or more of the given values is invalid.',
                    ],
                ],
            ],
        ]);
    }

    public function it_throws_bad_request_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        ClientException  $httpException,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(ErrorResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $httpException->getResponse()->willReturn($httpResponse);
        $httpResponse->getStatusCode()->willReturn(400);
        $responseBody->getContents()->willReturn(json_encode([
            'status'  => 400,
            'code'    => 'invalid_input',
            'message' => 'Request input is invalid.',
        ]));

        $this->shouldThrow(BadRequestException::class)->during('sendRequest', [$request]);
    }

    public function it_throws_auth_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        ClientException  $httpException,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(ErrorResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $httpException->getResponse()->willReturn($httpResponse);
        $httpResponse->getStatusCode()->willReturn(401);
        $responseBody->getContents()->willReturn(json_encode([
            'status'  => 401,
            'code'    => 'unauthorized',
            'message' => 'You must be authenticated to make this request.',
        ]));

        $this->shouldThrow(AuthException::class)->during('sendRequest', [$request]);
    }

    public function it_throws_not_found_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        ClientException  $httpException,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(ErrorResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $httpException->getResponse()->willReturn($httpResponse);
        $httpResponse->getStatusCode()->willReturn(404);
        $responseBody->getContents()->willReturn(json_encode([
            'status'  => 404,
            'code'    => 'not_found',
            'message' => 'Not Found',
        ]));

        $this->shouldThrow(NotFoundException::class)->during('sendRequest', [$request]);
    }

    public function it_throws_connect_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        \GuzzleHttp\Exception\ConnectException $httpException
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(null)->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $this->shouldThrow(ConnectException::class)->during('sendRequest', [$request]);
    }

    public function it_throws_no_permission_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        RequestException $httpException,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(ErrorResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $httpException->getResponse()->willReturn($httpResponse);
        $httpResponse->getStatusCode()->willReturn(403);
        $responseBody->getContents()->willReturn(json_encode([
            'status'  => 403,
            'code'    => 'access_denied',
            'message' => 'Access Denied',
        ]));

        $this->shouldThrow(NoPermissionException::class)->during('sendRequest', [$request]);
    }

    public function it_throws_rate_limits_exception(
        RequestContext   $context,
        RequestInterface $request,
        Client           $httpClient,
        RequestException $httpException,
        Response         $httpResponse,
        StreamInterface  $responseBody
    ) {
        $context->setLastRequest($request)->shouldBeCalled();
        $context->setLastResponse(Argument::type(ErrorResponse::class))->shouldBeCalled();

        $request->getContext()->willReturn($context);
        $request->getMethod()->willReturn('GET');
        $request->getUrl()->willReturn('some_url');

        $httpClient->request(
            'GET',
            'some_url',
            [
                'headers' => [
                    'Authorization' => 'my_token',
                ],
            ]
        )->willThrow($httpException->getWrappedObject());

        $httpException->getResponse()->willReturn($httpResponse);
        $httpResponse->getStatusCode()->willReturn(403);
        $responseBody->getContents()->willReturn(json_encode([
            'status'  => 403,
            'code'    => 'rate_limits',
            'message' => 'Your limit for api calls is exhausted.',
        ]));

        $this->shouldThrow(RateLimitException::class)->during('sendRequest', [$request]);
    }
}
