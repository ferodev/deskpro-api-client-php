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

namespace DeskPROClient\Api\Request;

use DeskPROClient\Api\Exception\AbstractClientApiException;
use DeskPROClient\Api\Exception\AuthException;
use DeskPROClient\Api\Exception\BadRequestException;
use DeskPROClient\Api\Exception\ConnectException;
use DeskPROClient\Api\Exception\NoPermissionException;
use DeskPROClient\Api\Exception\NotFoundException;
use DeskPROClient\Api\Exception\RateLimitException;
use DeskPROClient\Api\Response\BatchResponse;
use DeskPROClient\Api\Response\DataResponse;
use DeskPROClient\Api\Response\ErrorResponse;
use DeskPROClient\Api\Response\NoContentResponse;
use DeskPROClient\Api\Response\ResponseInterface;
use GuzzleHttp\RequestOptions;

/**
 * Helper to execute API requests and parse raw http response to response objects.
 */
class RequestHelper
{
    /**
     * Performs an API request.
     *
     * @param RequestInterface $request
     *
     * @throws AbstractClientApiException
     *
     * @return ResponseInterface
     */
    public static function sendRequest(RequestInterface $request)
    {
        $response = null;

        try {
            $context = $request->getContext();
            $options = [
                RequestOptions::HEADERS => [
                    'Authorization' => $context->getAuthHeader(),
                ],
            ];

            if ($request instanceof RequestDataInterface && $request->getData()) {
                if ($request instanceof UploadRequest) {
                    $options[RequestOptions::MULTIPART] = [
                        [
                            'name'     => 'file',
                            'contents' => $request->getData(),
                            'filename' => $request->getFilename(),
                        ],
                    ];
                } else {
                    $options[RequestOptions::JSON] = $request->getData();
                }
            }

            $httpResponse = $context->getHttpClient()->request(
                $request->getMethod(),
                $request->getUrl(),
                $options
            );

            $response = self::parseResponse($httpResponse->getBody()->getContents());
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // handle 'timeout' and 'connect' exceptions
            throw new ConnectException($e->getMessage(), $request, null, $e);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // handle other 'bad request' exceptions
            if ($e->getResponse()) {
                $response = self::parseResponse($e->getResponse()->getBody()->getContents());
            }

            if ($response instanceof ErrorResponse) {
                switch ($e->getResponse()->getStatusCode()) {
                    case 401:
                        throw new AuthException($request, $response, $e);
                    case 403:
                        if ($response->getCode() === 'rate_limits') {
                            throw new RateLimitException($request, $response, $e);
                        }

                        throw new NoPermissionException($request, $response, $e);
                    case 404:
                        throw new NotFoundException($request, $response, $e);
                    case 400:
                    default:
                        throw new BadRequestException($response->getMessage(), $request, $response, $e);
                }
            }

            throw new BadRequestException($e->getMessage(), $request, null, $e);
        } catch (\Exception $e) {
            // handle unexpected exceptions
            throw new BadRequestException($e->getMessage(), $request, null, $e);
        } finally {
            // keep last request and response for debugging
            $context = $request->getContext();
            $context->setLastRequest($request);
            $context->setLastResponse($response);
        }

        return $response;
    }

    /**
     * Wraps a raw http response to ResponseInterface object.
     *
     * @param string|array $httpResponse
     *
     * @return ResponseInterface
     */
    public static function parseResponse($httpResponse)
    {
        if (is_string($httpResponse)) {
            $httpResponse = json_decode($httpResponse, true);
        } elseif (!is_array($httpResponse)) {
            $httpResponse = null;
        }

        $headers = [];
        if (isset($httpResponse['headers']) && is_array($httpResponse['headers'])) {
            $headers = $httpResponse['headers'];
        }

        // parse batch response
        if (isset($httpResponse['responses']) && is_array($httpResponse['responses'])) {
            $responses = [];
            foreach ($httpResponse['responses'] as $requestId => $subResponse) {
                $responses[$requestId] = self::parseResponse($subResponse);
            }

            return new BatchResponse($responses);
        }

        // parse error response
        if (isset($httpResponse['status'])) {
            $status  = $httpResponse['status'];
            $code    = null;
            $message = null;
            $errors  = [];

            if (isset($httpResponse['code'])) {
                $code = $httpResponse['code'];
            }
            if (isset($httpResponse['message'])) {
                $message = $httpResponse['message'];
            }
            if (isset($httpResponse['errors'])) {
                $errors = $httpResponse['errors'];
            }

            return new ErrorResponse($code, $status, $message, $errors, $headers);
        }

        // parse single endpoint response
        $data   = null;
        $meta   = [];
        $linked = [];
        if (isset($httpResponse['data'])) {
            $data = $httpResponse['data'];
        }
        if (isset($httpResponse['meta']) && is_array($httpResponse['meta'])) {
            $meta = $httpResponse['meta'];
        }
        if (isset($httpResponse['linked']) && is_array($httpResponse['linked'])) {
            $linked = $httpResponse['linked'];
        }

        if ($data) {
            return new DataResponse($data, $meta, $linked, $headers);
        }

        return new NoContentResponse($headers);
    }
}
