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

use DeskPROClient\Api\Request\RequestContext;
use GuzzleHttp\Client;
use PhpSpec\ObjectBehavior;

/**
 * @mixin \DeskPROClient\Api\Request\CountRequest
 */
class CountRequestSpec extends ObjectBehavior
{
    public function let(RequestContext $context, Client $httpClient)
    {
        $this->beConstructedWith($context, 'my_endpoint');

        $context->getBaseUrl()->willReturn('http://helpdesk.url/api/v2');
        $context->getAuthHeader()->willReturn('my_token');
        $context->getHttpClient()->willReturn($httpClient);
    }

    public function it_returns_request_url_without_grouping()
    {
        $this->getUrl()->shouldReturn('http://helpdesk.url/api/v2/my_endpoint/counts');
    }

    public function it_returns_request_url_with_grouping()
    {
        $this->groupBy('id')->getUrl()->shouldReturn('http://helpdesk.url/api/v2/my_endpoint/counts?group_by=id');
    }
}
