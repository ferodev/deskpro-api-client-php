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
use DeskPROClient\Api\Response\ResponseInterface;

/**
 * Request interface to perform a DeskPRO API request.
 */
interface RequestInterface
{
    /**
     * Keeps DeskPRO API credentials (e.g. DeskPRO url, api version and 'Authorization' header value).
     *
     * @return RequestContext
     */
    public function getContext();

    /**
     * Resource path (e.g. 'tickets', 'people').
     *
     * @return string
     */
    public function getEndpoint();

    /**
     * Absolute url of the API action (e.g. 'https://your-deskpro.url/api/v2/tickets', 'https://your-deskpro.url/api/v2/people').
     *
     * @return string
     */
    public function getUrl();

    /**
     * Request method (e.g. 'GET', 'POST', 'PUT' and 'DELETE').
     *
     * @return string
     */
    public function getMethod();

    /**
     * Performs API request and returns API response object or throws a DeskPRO client exception on failure.
     *
     * @throws AbstractClientApiException
     *
     * @return ResponseInterface
     */
    public function send();
}
