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
 * Error API response.
 * Returns if it's unable to perform request, e.g. validation errors on create or update any resource, no permissions errors.
 */
class ErrorResponse extends AbstractResponse
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Constructor.
     *
     * @param string $code       Error code (e.g. no_permission, bad_request)
     * @param int    $statusCode Http status code
     * @param string $message    Error message
     * @param array  $errors     Form submission errors
     * @param array  $headers    Response headers
     */
    public function __construct($code, $statusCode, $message, array $errors = [], array $headers = [])
    {
        parent::__construct($headers);

        $this->code       = $code;
        $this->statusCode = $statusCode;
        $this->message    = $message;
        $this->errors     = $errors;
    }

    /**
     * Error code (e.g. no_permission, bad_request).
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Http status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Error message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Form submission errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
