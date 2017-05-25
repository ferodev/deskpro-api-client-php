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
 * Returns a paginated collection of items of specific resource type.
 * Could be filtered, ordered and paginated by criteria.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $request = new FindRequest($context, 'tickets');
 *     $request
 *         ->criteria([
 *             'agent' => 1,
 *         ])
 *         ->orderBy('id')
 *         ->orderDir('desc')
 *         ->count(10)
 *         ->sideload('person')
 *     ;
 *
 *     $result = $request->send();
 * </code>
 */
class FindRequest extends AbstractRequest
{
    use SideloadsTrait;

    /**
     * @var array
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $orderBy;

    /**
     * @var string
     */
    protected $orderDir;

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $page;

    /**
     * Constructor.
     *
     * @param RequestContext $context  Keeps DeskPRO credentials and http client instance
     * @param string         $endpoint Endpoint relative url
     * @param array          $criteria Collection filters
     * @param string         $orderBy  Order by field name
     * @param string         $orderDir Order direction 'desc' or 'asc'
     * @param int            $count    Max results count
     * @param int            $page     Pagination page number
     */
    public function __construct(RequestContext $context, $endpoint, array $criteria = [], $orderBy = null, $orderDir = null, $count = null, $page = null)
    {
        parent::__construct($context, $endpoint);

        $this->criteria = $criteria;
        $this->orderBy  = $orderBy;
        $this->orderDir = $orderDir;
        $this->count    = $count;
        $this->page     = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return 'GET';
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        $url    = $this->context->getBaseUrl().'/'.$this->endpoint;
        $params = [];

        if ($this->criteria) {
            $params = array_merge($this->criteria, $params);
        }
        if ($this->orderBy) {
            $params['order_by'] = $this->orderBy;
        }
        if ($this->orderDir) {
            $params['order_dir'] = $this->orderDir;
        }
        if ($this->page) {
            $params['page'] = $this->page;
        }
        if ($this->count) {
            $params['count'] = $this->count;
        }
        if ($this->sideloads) {
            $params['include'] = $this->getSideloadsQueryParam();
        }
        if ($params) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param array $criteria
     *
     * @return $this
     */
    public function criteria(array $criteria = [])
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     *
     * @return $this
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDir()
    {
        return $this->orderDir;
    }

    /**
     * @param string $orderDir
     *
     * @return $this
     */
    public function orderDir($orderDir)
    {
        $this->orderDir = $orderDir;

        return $this;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function count($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function page($page)
    {
        $this->page = $page;

        return $this;
    }
}
