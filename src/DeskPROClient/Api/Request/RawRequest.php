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

/**
 * Sends raw request to the DeskPRO API.
 * You can specify any method, endpoint, query params and payload.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *
 *     // GET request
 *     $request = new RawRequest($context, 'tickets', 'GET', ['agent' => 1]);
 *     $result  = $request->send();
 *
 *     // POST request
 *     $request = new RawRequest($context, 'tickets', 'POST', [], ['subject' => 'My ticket']);
 *     $result  = $request->send();
 * </code>
 */
class RawRequest extends AbstractRequest implements RequestDataInterface
{
    use SideloadsTrait;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $queryParams;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param RequestContext $context     Keeps DeskPRO credentials and http client instance
     * @param string         $endpoint    Endpoint relative url
     * @param string         $method      Request method (e.g. 'GET', 'POST')
     * @param array          $queryParams Query params
     * @param mixed          $data        Request payload
     */
    public function __construct(RequestContext $context, $endpoint, $method, array $queryParams = [], $data = null)
    {
        parent::__construct($context, $endpoint);

        $this->method      = $method;
        $this->queryParams = $queryParams;
        $this->data        = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url    = $this->context->getBaseUrl().'/'.$this->endpoint;
        $params = $this->queryParams;

        if ($this->sideloads) {
            $params['include'] = $this->getSideloadsQueryParam();
        }
        if ($params) {
            $url .= '?'.http_build_query($this->queryParams);
        }

        return $url;
    }

    /**
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array $queryParams
     *
     * @return $this
     */
    public function queryParams(array $queryParams = [])
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
