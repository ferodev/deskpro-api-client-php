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
 * Allows to wrap several API requests and execute them within a single API request.
 * I.e. could be useful for app bootstrap actions to pre-load common data from the API.
 *
 * Usage:
 *
 * <code>
 *     $context = new RequestContext($helpdeskUrl, $authHeader);
 *     $request = new BatchRequest($context, 'batch', [
 *         'people'   => new FindRequest($context, 'people'),
 *         'tickets'  => new FindRequest($context, 'tickets'),
 *         'articles' => new FindRequest($context, 'articles'),
 *     ]);
 *
 *     $result = $request->send();
 * </code>
 *
 * Response structure:
 *
 * ```
 * {
 *     "responses": {
 *         "people": {
 *             "data": [
 *                 {
 *                     "id": 1,
 *                     "name": "Lacey McKenzie"
 *                     ...
 *                 },
 *                 ...
 *             ],
 *             "meta": {},
 *             "linked": {},
 *             "headers": {
 *                 "Content-Type": "application/json"
 *             }
 *         },
 *         "tickets": {
 *             "data": [
 *                 {
 *                     "id": 1,
 *                     "name": "My ticket"
 *                     ...
 *                 },
 *                 ...
 *             ],
 *             "meta": {},
 *             "linked": {},
 *             "headers": {
 *                 "Content-Type": "application/json"
 *             }
 *         },
 *         "articles": {
 *             "data": [
 *                 {
 *                     "id": 1,
 *                     "title": "My article"
 *                     ...
 *                 },
 *                 ...
 *             ],
 *             "meta": {},
 *             "linked": {},
 *             "headers": {
 *                 "Content-Type": "application/json"
 *             }
 *         },
 *     }
 * }
 * ```
 */
class BatchRequest extends AbstractRequest implements RequestDataInterface
{
    /**
     * Collection of sub requests.
     *
     * @var RequestInterface[]
     */
    protected $requests = [];

    /**
     * Constructor.
     *
     * @param RequestContext     $context  Keeps DeskPRO credentials and http client instance
     * @param string             $endpoint Endpoint relative url
     * @param RequestInterface[] $requests Collection of sub requests
     */
    public function __construct(RequestContext $context, $endpoint, array $requests)
    {
        parent::__construct($context, $endpoint);

        $this->requests = $requests;
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
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
    public function getMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $batchRequests = [];
        foreach ($this->requests as $requestId => $request) {
            $batchRequest = [
                'method' => $request->getMethod(),
                'url'    => $request->getEndpoint(),
            ];

            if ($request instanceof RequestDataInterface) {
                $batchRequest['data'] = $request->getData();
            }

            $batchRequests[$requestId] = $batchRequest;
        }

        return [
            'requests' => $batchRequests,
        ];
    }
}
