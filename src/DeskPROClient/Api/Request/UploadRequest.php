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
 * Uploads file to the DeskPRO blobs storage.
 * Returns created blob info (blob id, blob auth code).
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $result  = new UploadRequest($context, 'blobs/temp', 'my_file.txt', 'file content');
 * </code>
 */
class UploadRequest extends AbstractRequest implements RequestDataInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $content;

    /**
     * Constructor.
     *
     * @param RequestContext $context  Keeps DeskPRO credentials and http client instance
     * @param string         $endpoint Endpoint relative url
     * @param string         $filename Uploading filename
     * @param string         $content  Uploading file content
     */
    public function __construct(RequestContext $context, $endpoint, $filename, $content)
    {
        parent::__construct($context, $endpoint);

        $this->filename = $filename;
        $this->content  = $content;
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
        return $this->context->getBaseUrl().'/'.$this->endpoint;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->content;
    }

    /**
     * Get uploading filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}
