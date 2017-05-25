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

use PhpSpec\ObjectBehavior;

/**
 * @mixin \DeskPROClient\Api\Request\RequestContext
 */
class RequestContextSpec extends ObjectBehavior
{
    public function it_returns_base_url()
    {
        $this->beConstructedWith('https://your-deskpro.url/', 'key 1:my_key');
        $this->getBaseUrl()->shouldReturn('https://your-deskpro.url/api/v2');
    }

    public function it_returns_base_url_with_api_version()
    {
        $this->beConstructedWith('https://your-deskpro.url/', 'key 1:my_key', '20170401');
        $this->getBaseUrl()->shouldReturn('https://your-deskpro.url/api/v2/20170401');
    }

    public function it_returns_correct_base_url_if_helpdesk_url_provided_without_trailing_slash()
    {
        $this->beConstructedWith('https://your-deskpro.url', 'key 1:my_key');
        $this->getBaseUrl()->shouldReturn('https://your-deskpro.url/api/v2');
    }
}
