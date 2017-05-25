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

namespace DeskPROClient\Api\Response;

/**
 * Data API response.
 * Returns response data on get, find or create requests.
 */
class DataResponse extends AbstractResponse
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $meta;

    /**
     * @var array
     */
    protected $linked;

    /**
     * Constructor.
     *
     * @param array $data    Response data, resource item or collection of resource items
     * @param array $meta    Response meta data (e.g. current page, total items count)
     * @param array $linked  Side-loading data (linked related resources)
     * @param array $headers Response headers
     */
    public function __construct($data = null, array $meta = [], array $linked = [], array $headers = [])
    {
        parent::__construct($headers);

        $this->data   = $data;
        $this->meta   = $meta;
        $this->linked = $linked;
    }

    /**
     * Response data, fetched resource item or collection of resource items.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get response meta data (e.g. current page, total items count).
     *
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get side-loading data (linked related resources).
     *
     * @return array
     */
    public function getLinked()
    {
        return $this->linked;
    }
}
