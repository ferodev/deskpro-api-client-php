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
 * Returns total collection count of specific resource type.
 *
 * Can be grouped by some resource props (depends on resource type,
 * described in https://support.deskpro.com/api/v2/doc, see 'group_by' query param,
 * for example https://support.deskpro.com/api/v2/doc#get--api-v2-articles-counts).
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $request = new CountRequest($context, 'articles');
 *     $request->groupBy('author');
 *
 *     $result = $request->send();
 * </code>
 *
 * Response w/o grouping:
 *
 * ```
 * {
 *     "data": {
 *         "count": 101,
 *         "id": null,
 *         "value": null,
 *         "type": null,
 *         "title": null,
 *         "grouped_by": null,
 *         "nested": []
 *     },
 *     "meta": {},
 *     "linked": {}
 * }
 * ```
 *
 * Response w/ grouping:
 *
 * ```
 * {
 *     "data": {
 *         "count": 101,
 *         "id": null,
 *         "value": null,
 *         "type": null,
 *         "title": null,
 *         "grouped_by": "author",
 *         "nested": [
 *             {
 *                 "count": 10,
 *                 "id": 1,
 *                 "value": null,
 *                 "type": "author",
 *                 "title": "Lacey McKenzie",
 *                 "grouped_by": null,
 *                 "nested": []
 *             },
 *             {
 *                 "count": 15,
 *                 "id": 9,
 *                 "value": null,
 *                 "type": "author",
 *                 "title": "Loraine Keeling",
 *                 "grouped_by": null,
 *                 "nested": []
 *             },
 *             ...
 *         ]
 *     },
 *     "meta": {},
 *     "linked": {}
 * }
 * ```
 */
class CountRequest extends AbstractRequest
{
    /**
     * Represents 'group_by' query param.
     *
     * @var string
     */
    protected $groupBy;

    /**
     * Constructor.
     *
     * @param RequestContext $context  Keeps DeskPRO credentials and http client instance
     * @param string         $endpoint Endpoint relative url
     * @param string         $groupBy  Specifies 'group_by' query param
     */
    public function __construct(RequestContext $context, $endpoint, $groupBy = null)
    {
        parent::__construct($context, $endpoint);

        $this->groupBy = $groupBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'GET';
    }

    /**
     * Get 'group_by' query param.
     *
     * @return string
     */
    public function getGroupBy()
    {
        return $this->groupBy;
    }

    /**
     * Set 'group_by' query param.
     *
     * @param string $groupBy
     *
     * @return $this
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url = $this->context->getBaseUrl().'/'.$this->endpoint.'/counts';
        if ($this->groupBy) {
            $url .= '?group_by='.$this->groupBy;
        }

        return $url;
    }
}
