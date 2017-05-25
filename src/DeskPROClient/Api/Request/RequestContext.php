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

use DeskPROClient\Api\Response\ResponseInterface;
use GuzzleHttp\Client;

/**
 * Request context, keeps DeskPRO credentials and Guzzle http client instance.
 */
class RequestContext
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $helpdeskUrl;

    /**
     * @var string
     */
    protected $authHeader;

    /**
     * @var int
     */
    protected $apiVersion;

    /**
     * @var RequestInterface
     */
    protected $lastRequest;

    /**
     * @var ResponseInterface
     */
    protected $lastResponse;

    /**
     * Constructor.
     *
     * @param string $helpdeskUrl Your DeskPRO url
     * @param string $authHeader  'Authorization' header value, could be token or key
     * @param int    $apiVersion  Set specific api version
     * @param Client $httpClient  Allows to override http client instance (e.g. for extra logging)
     */
    public function __construct($helpdeskUrl, $authHeader, $apiVersion = null, Client $httpClient = null)
    {
        $this->helpdeskUrl = $helpdeskUrl;
        $this->authHeader  = $authHeader;
        $this->apiVersion  = $apiVersion;
        $this->httpClient  = $httpClient ?: new Client([
            'timeout' => 30,
        ]);
    }

    /**
     * Guzzle http client instance.
     *
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Base DeskPRO helpdesk url.
     *
     * @return string
     */
    public function getHelpdeskUrl()
    {
        return $this->helpdeskUrl;
    }

    /**
     * 'Authorization' header value.
     * Could be an api token (e.g. 'token 1:2AWJ2BQ7WG589PQ6S862TCGY4') or key (e.g. 'key 1:dev-code').
     *
     * @return string
     */
    public function getAuthHeader()
    {
        return $this->authHeader;
    }

    /**
     * Specific API version based on current date (the version format is YYYYMMDD).
     *
     * @see http://api.deskpro.com/#api-versions-and-backwards-compatibility
     *
     * @return int
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Get base api url (e.g. https://your-deskpro.url/api/v2/20170401).
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $url = rtrim($this->helpdeskUrl, '/').'/api/v2';

        if ($this->apiVersion) {
            $url .= '/'.$this->apiVersion;
        }

        return $url;
    }

    /**
     * Get last performed request (for debugging).
     *
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Set last request for debug.
     *
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function setLastRequest(RequestInterface $request = null)
    {
        $this->lastRequest = $request;

        return $this;
    }

    /**
     * Get last api response (for debugging).
     *
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Set last response for debug.
     *
     * @param ResponseInterface $response
     *
     * @return $this
     */
    public function setLastResponse(ResponseInterface $response = null)
    {
        $this->lastResponse = $response;

        return $this;
    }
}
