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
 * Request helper to sideload related resources.
 * Available for 'find', 'get', 'create' and 'update' requests.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *
 *     // raw request
 *     $request = new RawRequest($context, 'tickets', 'GET', ['agent' => 1]);
 *     $request->sideload('person');
 *
 *     $result = $request->send();
 *
 *     // find request
 *     $request = new FindRequest($context, 'tickets');
 *     $request->sideload('person');
 *
 *     $result = $request->send();
 *
 *     // get request
 *     $request = new GetRequest($context, 'tickets', 1);
 *     $request->sideload('person');
 *
 *     $result = $request->send();
 *
 *     // create request
 *     $request = new CreateRequest($context, 'tickets', ['subject' => 'My ticket']);
 *     $request->sideload('person');
 *
 *     $result = $request->send();
 *
 *     // update request
 *     $request = new UpdateRequest($context, 'tickets', 1, ['subject' => 'My ticket']);
 *     $request->sideload('person');
 *
 *     $result = $request->send();
 * </code>
 */
trait SideloadsTrait
{
    /**
     * @var array
     */
    protected $sideloads = [];

    /**
     * Get requested sideload types.
     *
     * @return array
     */
    public function getSideloads()
    {
        return $this->sideloads;
    }

    /**
     * Adds sideloads query param 'include' to the request url.
     * E.g. https://your-deskpro.url/api/v2/endpoint?include=person,organization.
     *
     * @param array $sideloads
     *
     * @return $this
     */
    public function sideload($sideloads)
    {
        if (is_string($sideloads)) {
            $this->sideloads = [$sideloads];
        } elseif (is_array($sideloads)) {
            $this->sideloads = $sideloads;
        }

        return $this;
    }

    /**
     * Converts sideloads to query param value.
     *
     * @return string
     */
    protected function getSideloadsQueryParam()
    {
        return $this->sideloads ? implode(',', $this->sideloads) : null;
    }
}
