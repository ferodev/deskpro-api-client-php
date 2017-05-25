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

namespace DeskPROClient\Api;

use DeskPROClient\Api\Request\BatchRequest;
use DeskPROClient\Api\Request\GetRequest;
use DeskPROClient\Api\Request\RawRequest;
use DeskPROClient\Api\Request\RequestContext;
use DeskPROClient\Api\Request\RequestInterface;
use DeskPROClient\Api\Request\UploadRequest;
use DeskPROClient\Api\Resource\Chat\ChatDepartmentsResource;
use DeskPROClient\Api\Resource\Content\ArticleCategoriesResource;
use DeskPROClient\Api\Resource\Content\ArticlesResource;
use DeskPROClient\Api\Resource\Content\DownloadCategoriesResource;
use DeskPROClient\Api\Resource\Content\DownloadsResource;
use DeskPROClient\Api\Resource\Content\NewsCategoriesResource;
use DeskPROClient\Api\Resource\Content\NewsResource;
use DeskPROClient\Api\Resource\Feedback\FeedbackCategoriesResource;
use DeskPROClient\Api\Resource\Feedback\FeedbackResource;
use DeskPROClient\Api\Resource\Feedback\FeedbackTypesResource;
use DeskPROClient\Api\Resource\Organization\OrganizationFieldsResource;
use DeskPROClient\Api\Resource\Organization\OrganizationGetRequest;
use DeskPROClient\Api\Resource\Organization\OrganizationsResource;
use DeskPROClient\Api\Resource\Person\PeopleResource;
use DeskPROClient\Api\Resource\Person\PersonFieldsResource;
use DeskPROClient\Api\Resource\Person\PersonGetRequest;
use DeskPROClient\Api\Resource\Ticket\TicketCategoriesResource;
use DeskPROClient\Api\Resource\Ticket\TicketDepartmentsResource;
use DeskPROClient\Api\Resource\Ticket\TicketFieldsResource;
use DeskPROClient\Api\Resource\Ticket\TicketFormsResource;
use DeskPROClient\Api\Resource\Ticket\TicketGetRequest;
use DeskPROClient\Api\Resource\Ticket\TicketPrioritiesResource;
use DeskPROClient\Api\Resource\Ticket\TicketProductsResource;
use DeskPROClient\Api\Resource\Ticket\TicketsResource;
use DeskPROClient\Api\Resource\Ticket\TicketWorkflowsResource;
use DeskPROClient\Api\Response\DataResponse;
use DeskPROClient\Api\Response\ResponseInterface;
use GuzzleHttp\Client;

/**
 * DeskPRO API client.
 *
 * How to configure:
 *
 * <code>
 *     $helpdeskUrl = 'https://your-deskpro.url/';
 *     $authHeader  = 'token 1:AWJ2BQ7WG589PQ6S862TCGY4'; // auth via token (format "token {person_id}:{token_string}")
 *     $authHeader  = 'key 1:dev-code'; // auth via key (format "key {person_id}:{key_code_string}")
 *
 *     // min requirements
 *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
 *
 *     // with api version (to support breaking changes, see http://api.deskpro.com/#api-versions-and-backwards-compatibility)
 *     // e.g. the date when you started using the API
 *     // format YYYYMMMDD
 *     $apiVersion = '20170605';
 *
 *     $client = new DeskPROApi($helpdeskUrl, $authHeader, $apiVersion);
 *
 *     // setup custom middleware
 *     $stack = GuzzleHttp\HandlerStack;::create();
 *     $stack->push(
 *         GuzzleHttp\Middleware::log(
 *             new Monolog\Logger('Logger'),
 *             new GuzzleHttp\MessageFormatter('{req_body} - {res_body}')
 *         )
 *     );
 *
 *     $httpClient = new GuzzleHttp\Client([
 *         'timeout' => 60,
 *         'handler' => $stack,
 *     ]);
 *
 *     $client = new DeskPROApi($helpdeskUrl, $authHeader, $apiVersion, $httpClient);
 * </code>
 */
class DeskPROApi
{
    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * Constructor.
     *
     * @param string $helpdeskUrl Your DeskPRO url
     * @param string $authHeader  'Authorization' header value, could be token or key
     * @param int    $apiVersion  Set specific api version
     * @param Client $httpClient  Allows to override http client instance to set middleware (e.g. for extra logging)
     */
    public function __construct($helpdeskUrl, $authHeader, $apiVersion = null, Client $httpClient = null)
    {
        $this->context = new RequestContext($helpdeskUrl, $authHeader, $apiVersion, $httpClient);
    }

    /**
     * Get tickets resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count tickets
     *     $count = $client->tickets()->count()->send();
     *
     *     // get paginated list of tickets
     *     $tickets = $client->tickets()->find()->send();
     *
     *     // get a single ticket
     *     $ticket = $client->tickets(1)->send();
     *
     *     // create a ticket
     *     $client->tickets()->create(['subject' => 'My ticket')->send();
     *
     *     // update ticket
     *     $client->tickets()->update(1, ['subject' => 'My ticket'])->send();
     *
     *     // delete ticket
     *     $client->tickets()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return TicketGetRequest|TicketsResource
     */
    public function tickets($id = null)
    {
        $resource = new TicketsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket forms resource (with layout support).
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // create a ticket
     *     $ticket = $client->ticketForms()->create(['subject' => 'My ticket')->send();
     *
     *     // update ticket
     *     $client->ticketForms()->update(1, ['subject' => 'My ticket'])->send();
     * </code>
     *
     * @param string $layoutContext
     *
     * @return TicketFormsResource
     */
    public function ticketForms($layoutContext)
    {
        return new TicketFormsResource($this->context, $layoutContext);
    }

    /**
     * Get ticket departments resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count ticket departments
     *     $count = $client->ticketDepartments()->count()->send();
     *
     *     // get paginated list of ticket departments
     *     $ticketDepartments = $client->ticketDepartments()->find()->send();
     *
     *     // get a single ticket department
     *     $ticketDepartment = $client->ticketDepartments(1)->send();
     *
     *     // create a ticket department
     *     $client->ticketDepartments()->create(['title' => 'My department')->send();
     *
     *     // update ticket department
     *     $client->ticketDepartments()->update(1, ['title' => 'My modified department'])->send();
     *
     *     // delete ticket department
     *     $client->ticketDepartments()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketDepartmentsResource
     */
    public function ticketDepartments($id = null)
    {
        $resource = new TicketDepartmentsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket custom fields resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // get paginated list of ticket custom fields
     *     $ticketFields = $client->ticketFields()->find()->send();
     *
     *     // get a single ticket custom field
     *     $ticketField = $client->ticketFields(1)->send();
     *
     *     // update ticket custom field
     *     $client->ticketFields()->update(1, ['is_enabled' => true])->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketFieldsResource
     */
    public function ticketFields($id = null)
    {
        $resource = new TicketFieldsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket categories resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count ticket categories
     *     $count = $client->ticketCategories()->count()->send();
     *
     *     // get paginated list of ticket categories
     *     $ticketCategories = $client->ticketCategories()->find()->send();
     *
     *     // get a single ticket category
     *     $ticketCategory = $client->ticketCategories(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketCategoriesResource
     */
    public function ticketCategories($id = null)
    {
        $resource = new TicketCategoriesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket priorities resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count ticket priorities
     *     $count = $client->ticketPriorities()->count()->send();
     *
     *     // get paginated list of ticket priorities
     *     $ticketPriorities = $client->ticketPriorities()->find()->send();
     *
     *     // get a single ticket priority
     *     $ticketPriority = $client->ticketPriorities(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketPrioritiesResource
     */
    public function ticketPriorities($id = null)
    {
        $resource = new TicketPrioritiesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket products resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count ticket products
     *     $count = $client->ticketProducts()->count()->send();
     *
     *     // get paginated list of ticket products
     *     $ticketProducts = $client->ticketProducts()->find()->send();
     *
     *     // get a single ticket product
     *     $ticketProduct = $client->ticketProducts(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketProductsResource
     */
    public function ticketProducts($id = null)
    {
        $resource = new TicketProductsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get ticket workflows resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count ticket workflows
     *     $count = $client->ticketWorkflows()->count()->send();
     *
     *     // get paginated list of ticket workflows
     *     $ticketWorkflows = $client->ticketWorkflows()->find()->send();
     *
     *     // get a single ticket workflow
     *     $ticketWorkfow = $client->ticketWorkflows(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|TicketWorkflowsResource
     */
    public function ticketWorkflows($id = null)
    {
        $resource = new TicketWorkflowsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get people resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count people
     *     $count = $client->people()->count()->send();
     *
     *     // get paginated list of people
     *     $people = $client->people()->find()->send();
     *
     *     // get a single person
     *     $person = $client->people(1)->send();
     *
     *     // create a person
     *     $client->people()->create(['name' => 'Lacey McKenzie')->send();
     *
     *     // update person
     *     $client->people()->update(1, ['name' => 'Lacey McKenzie'])->send();
     *
     *     // delete person
     *     $client->people()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return PersonGetRequest|PeopleResource
     */
    public function people($id = null)
    {
        $resource = new PeopleResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get person custom fields resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // get paginated list of person custom fields
     *     $personFields = $client->personFields()->find()->send();
     *
     *     // get a single person custom field
     *     $personField = $client->personFields(1)->send();
     *
     *     // update person custom field
     *     $client->personFields()->update(1, ['is_enabled' => true])->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|PersonFieldsResource
     */
    public function personFields($id = null)
    {
        $resource = new PersonFieldsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get organizations resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count organizations
     *     $count = $client->organizations()->count()->send();
     *
     *     // get paginated list of organizations
     *     $organizations = $client->organizations()->find()->send();
     *
     *     // get a single organization
     *     $organization = $client->organizations(1)->send();
     *
     *     // create a organization
     *     $client->organizations()->create(['name' => 'My Organization')->send();
     *
     *     // update organization
     *     $client->organizations()->update(1, ['name' => 'My Organization'])->send();
     *
     *     // delete organization
     *     $client->organizations()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return OrganizationGetRequest|OrganizationsResource
     */
    public function organizations($id = null)
    {
        $resource = new OrganizationsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get organization custom fields resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // get paginated list of organization custom fields
     *     $orgFields = $client->organizationFields()->find()->send();
     *
     *     // get a single organization custom field
     *     $orgField = $client->organizationFields(1)->send();
     *
     *     // update organization custom field
     *     $client->organizationFields()->update(1, ['is_enabled' => true])->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|OrganizationFieldsResource
     */
    public function organizationFields($id = null)
    {
        $resource = new OrganizationFieldsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get chat departments resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count chat departments
     *     $count = $client->chatDepartments()->count()->send();
     *
     *     // get paginated list of chat departments
     *     $chatDepartments = $client->chatDepartments()->find()->send();
     *
     *     // get a single chat department
     *     $chatDepartment = $client->chatDepartments(1)->send();
     *
     *     // create a chat department
     *     $client->chatDepartments()->create(['title' => 'My department')->send();
     *
     *     // update chat department
     *     $client->chatDepartments()->update(1, ['title' => 'My modified department'])->send();
     *
     *     // delete chat department
     *     $client->chatDepartments()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|ChatDepartmentsResource
     */
    public function chatDepartments($id = null)
    {
        $resource = new ChatDepartmentsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get articles resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count articles
     *     $count = $client->articles()->count()->send();
     *
     *     // get paginated list of articles
     *     $articles = $client->articles()->find()->send();
     *
     *     // get a single article
     *     $article = $client->articles(1)->send();
     *
     *     // create an article
     *     $client->articles()->create(['title' => 'My Article')->send();
     *
     *     // update article
     *     $client->articles()->update(1, ['title' => 'My Article'])->send();
     *
     *     // delete article
     *     $client->articles()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|ArticlesResource
     */
    public function articles($id = null)
    {
        $resource = new ArticlesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get article categories resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count articles categories
     *     $count = $client->articleCategories()->count()->send();
     *
     *     // get paginated list of article categories
     *     $articleCategories = $client->articleCategories()->find()->send();
     *
     *     // get a single article category
     *     $articleCategory = $client->articleCategories(1)->send();
     *
     *     // create an article category
     *     $client->articleCategories()->create(['title' => 'My Article Category')->send();
     *
     *     // update article category
     *     $client->articleCategories()->update(1, ['title' => 'My Article Category'])->send();
     *
     *     // delete article category
     *     $client->articleCategories()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|ArticleCategoriesResource
     */
    public function articleCategories($id = null)
    {
        $resource = new ArticleCategoriesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get news resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count news
     *     $count = $client->news()->count()->send();
     *
     *     // get paginated list of news
     *     $news = $client->news()->find()->send();
     *
     *     // get a single news item
     *     $newsItem = $client->news(1)->send();
     *
     *     // create a news item
     *     $client->news()->create(['title' => 'My News Item')->send();
     *
     *     // update news item
     *     $client->news()->update(1, ['title' => 'My News Item'])->send();
     *
     *     // delete news item
     *     $client->news()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|NewsResource
     */
    public function news($id = null)
    {
        $resource = new NewsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get news categories resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count news categories
     *     $count = $client->newsCategories()->count()->send();
     *
     *     // get paginated list of news categories
     *     $newsCategories = $client->newsCategories()->find()->send();
     *
     *     // get a single news category
     *     $newsCategory = $client->newsCategories(1)->send();
     *
     *     // create an news category
     *     $client->newsCategories()->create(['title' => 'My News Category')->send();
     *
     *     // update news category
     *     $client->newsCategories()->update(1, ['title' => 'My News Category'])->send();
     *
     *     // delete news category
     *     $client->newsCategories()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|NewsCategoriesResource
     */
    public function newsCategories($id = null)
    {
        $resource = new NewsCategoriesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get feedback resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count feedback
     *     $count = $client->feedback()->count()->send();
     *
     *     // get paginated list of feedback
     *     $feedback = $client->feedback()->find()->send();
     *
     *     // get a single feedback item
     *     $feedbackItem = $client->feedback(1)->send();
     *
     *     // create a feedback item
     *     $client->feedback()->create(['title' => 'My Feedback')->send();
     *
     *     // update feedback item
     *     $client->feedback()->update(1, ['title' => 'My Feedback'])->send();
     *
     *     // delete feedback item
     *     $client->feedback()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|FeedbackResource
     */
    public function feedback($id = null)
    {
        $resource = new FeedbackResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get feedback categories resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // get paginated list of feedback categories
     *     $feedbackCategories = $client->feedbackCategories()->find()->send();
     * </code>
     *
     * @return FeedbackCategoriesResource
     */
    public function feedbackCategories()
    {
        return new FeedbackCategoriesResource($this->context);
    }

    /**
     * Get feedback types resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count feedback types
     *     $count = $client->feedbackTypes()->count()->send();
     *
     *     // get paginated list of feedback types
     *     $feedbackTypes = $client->feedbackTypes()->find()->send();
     *
     *     // get a single feedback type
     *     $feedbackType = $client->feedbackTypes(1)->send();
     *
     *     // create a feedback type
     *     $client->feedbackTypes()->create(['title' => 'My Feedback Type')->send();
     *
     *     // update feedback type
     *     $client->feedbackTypes()->update(1, ['title' => 'My Feedback Type'])->send();
     *
     *     // delete feedback type
     *     $client->feedbackTypes()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|FeedbackTypesResource
     */
    public function feedbackTypes($id = null)
    {
        $resource = new FeedbackTypesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get downloads resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count downloads
     *     $count = $client->downloads()->count()->send();
     *
     *     // get paginated list of downloads
     *     $downloads = $client->downloads()->find()->send();
     *
     *     // get a single download
     *     $download = $client->downloads(1)->send();
     *
     *     // create a download
     *     $client->downloads()->create(['title' => 'My Download')->send();
     *
     *     // update download
     *     $client->downloads()->update(1, ['title' => 'My Download'])->send();
     *
     *     // delete download
     *     $client->downloads()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|DownloadsResource
     */
    public function downloads($id = null)
    {
        $resource = new DownloadsResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Get download categories resource.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // count download categories
     *     $count = $client->downloadCategories()->count()->send();
     *
     *     // get paginated list of download categories
     *     $downloadCategories = $client->downloadCategories()->find()->send();
     *
     *     // get a single download category
     *     $downloadCategory = $client->downloadCategories(1)->send();
     *
     *     // create a download category
     *     $client->downloadCategories()->create(['title' => 'My Download Category')->send();
     *
     *     // update download category
     *     $client->downloadCategories()->update(1, ['title' => 'My Download Category'])->send();
     *
     *     // delete download category
     *     $client->downloadCategories()->delete(1)->send();
     * </code>
     *
     * @param int|null $id
     *
     * @return GetRequest|DownloadCategoriesResource
     */
    public function downloadCategories($id = null)
    {
        $resource = new DownloadCategoriesResource($this->context);
        if ($id) {
            $resource = $resource->get($id);
        }

        return $resource;
    }

    /**
     * Create a batch request object.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *     $result = $client
     *         ->batch([
     *             'people'   => $client->people()->find(),
     *             'tickets'  => $client->tickets()->find(),
     *             'articles' => $client->articles()->find(),
     *         ])
     *         ->send()
     *     ;
     * </code>
     *
     * @param RequestInterface[] $requests
     *
     * @return BatchRequest
     */
    public function batch(array $requests)
    {
        return new BatchRequest($this->context, 'batch', $requests);
    }

    /**
     * Send GET raw api request.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *     $result = $client->sendGet('tickets', ['count' => 10]);
     * </code>
     *
     * @param string $endpoint
     * @param array  $queryParams
     *
     * @return ResponseInterface
     */
    public function sendGet($endpoint, array $queryParams = [])
    {
        $request = new RawRequest($this->context, $endpoint, 'GET', $queryParams);
        $result  = $request->send();

        return $result;
    }

    /**
     * Send POST raw api request.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *     $result = $client->sendPost('tickets', ['subject' => 'My ticket']);
     * </code>
     *
     * @param string $endpoint
     * @param mixed  $data
     * @param array  $queryParams
     *
     * @return ResponseInterface
     */
    public function sendPost($endpoint, $data, array $queryParams = [])
    {
        $request = new RawRequest($this->context, $endpoint, 'POST', $queryParams, $data);
        $result  = $request->send();

        return $result;
    }

    /**
     * Send PUT raw api request.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *     $result = $client->sendPut('tickets/1', ['subject' => 'Modified ticket']);
     * </code>
     *
     * @param string $endpoint
     * @param array  $queryParams
     * @param mixed  $data
     *
     * @return ResponseInterface
     */
    public function sendPut($endpoint, $data, array $queryParams = [])
    {
        $request = new RawRequest($this->context, $endpoint, 'PUT', $queryParams, $data);
        $result  = $request->send();

        return $result;
    }

    /**
     * Send DELETE raw api request.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *     $result = $client->sendDelete('tickets/1');
     * </code>
     *
     * @param string $endpoint
     * @param array  $queryParams
     * @param mixed  $data
     *
     * @return ResponseInterface
     */
    public function sendDelete($endpoint, array $queryParams = [], $data = null)
    {
        $request = new RawRequest($this->context, $endpoint, 'DELETE', $queryParams, $data);
        $result  = $request->send();

        return $result;
    }

    /**
     * Create a file blob from a file on fs, or by passing in a data string.
     *
     * Usage:
     *
     * <code>
     *     $client = new DeskPROApi($helpdeskUrl, $authHeader);
     *
     *     // from a file on fs
     *     $result = $client->createFile('../my_file.txt');
     *
     *     // from a data string
     *     $result = $client->createFile('my_file.txt', 'file content');
     * </code>
     *
     * @param string      $pathOrFilename
     * @param string|null $content
     *
     * @return DataResponse
     */
    public function createFile($pathOrFilename, $content = null)
    {
        if (!$content) {
            // get from file
            if (!file_exists($pathOrFilename) || !is_readable($pathOrFilename)) {
                throw new \RuntimeException('File is not readable');
            }

            $content  = file_get_contents($pathOrFilename);
            $filename = basename($pathOrFilename);
        } else {
            $filename = $pathOrFilename;
        }

        if (!$filename) {
            throw new \RuntimeException('No filename');
        }
        if (!$content) {
            throw new \RuntimeException('No file content');
        }

        $request = new UploadRequest($this->context, 'blobs/temp', $filename, $content);
        $result  = $request->send();

        return $result;
    }

    /**
     * Returns last api request for debugging.
     *
     * @return RequestInterface
     */
    public function getLastRequest()
    {
        return $this->context->getLastRequest();
    }

    /**
     * Returns last api response for debugging.
     *
     * @return ResponseInterface
     */
    public function getLastResponse()
    {
        return $this->context->getLastResponse();
    }
}
