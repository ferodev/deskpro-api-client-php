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
 * Modifies an existing resource of specific type by id.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $request = new UpdateRequest($context, 'tickets', 1, [
 *         'subject' => 'Modified ticket',
 *     ]);
 *
 *     $request->send();
 * </code>
 */
class UpdateRequest extends AbstractSingleObjectRequest implements RequestDataInterface
{
    use SideloadsTrait;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected $followLocation = false;

    /**
     * Constructor.
     *
     * @param RequestContext $context
     * @param string         $endpoint
     * @param int            $id
     * @param array          $data
     */
    public function __construct(RequestContext $context, $endpoint, $id, array $data)
    {
        parent::__construct($context, $endpoint, $id);

        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'PUT';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url    = $this->context->getBaseUrl().'/'.$this->endpoint.'/'.$this->id;
        $params = [];

        if ($this->followLocation) {
            $params['follow_location'] = $this->followLocation;
        }
        if ($this->sideloads) {
            // force follow location if sideloads are defined
            $params['follow_location'] = true;
            $params['include']         = $this->getSideloadsQueryParam();
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

    /**
     * Is 'follow_location' query parameter set.
     *
     * @return bool
     */
    public function isFollowLocation()
    {
        return $this->followLocation;
    }

    /**
     * Update request returns 204 No content by default.
     * But you can force return response using 'follow_location' query parameter.
     *
     * @param bool $followLocation
     *
     * @return $this
     */
    public function followLocation($followLocation)
    {
        $this->followLocation = (bool) $followLocation;

        return $this;
    }
}
