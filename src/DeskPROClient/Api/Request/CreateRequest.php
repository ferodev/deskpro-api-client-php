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
 * Creates a new resource of specific type.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $request = new CreateRequest($context, 'tickets', [
 *         'subject' => 'My ticket',
 *     ]);
 *
 *     $result = $request->send();
 * </code>
 */
class CreateRequest extends AbstractRequest implements RequestDataInterface
{
    use SideloadsTrait;

    /**
     * Request payload.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param RequestContext $context  Keeps DeskPRO credentials and http client instance
     * @param string         $endpoint Endpoint relative url
     * @param array          $data     Request payload
     */
    public function __construct(RequestContext $context, $endpoint, array $data)
    {
        parent::__construct($context, $endpoint);

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url    = $this->context->getBaseUrl().'/'.$this->endpoint;
        $params = [];

        if ($this->sideloads) {
            $params['include'] = $this->getSideloadsQueryParam();
        }
        if ($params) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }
}
