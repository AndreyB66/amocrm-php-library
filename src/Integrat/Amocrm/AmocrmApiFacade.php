<?php

namespace Integrat\Amocrm;

use Integrat\Amocrm\Services\CallService;
use Integrat\Amocrm\Services\CompanyService;
use Integrat\Amocrm\Services\ContactService;
use Integrat\Amocrm\Services\LeadService;
use Integrat\Amocrm\Services\LinkService;
use Integrat\Amocrm\Services\NoteService;
use Integrat\Amocrm\Services\PipelineService;
use Integrat\Amocrm\Services\TagService;
use Integrat\Amocrm\Services\TaskService;
use Integrat\Amocrm\Services\UserService;

class AmocrmApiFacade
{
    private Request $request;
    private CallService $callService;
    private CompanyService $companyService;
    private ContactService $contactService;
    private LeadService $leadService;
    private LinkService $linkService;
    private NoteService $noteService;
    private PipelineService $pipelineService;
    private TagService $tagService;
    private TaskService $taskService;
    private UserService $userService;
    
    public function __construct(string $domain, string $apiKey)
    {
        $this->request = new Request($domain, $apiKey);
        $this->callService = new CallService($this->request);
        $this->companyService = new CompanyService($this->request);
        $this->contactService = new ContactService($this->request);
        $this->leadService = new LeadService($this->request);
        $this->linkService = new LinkService($this->request);
        $this->noteService = new NoteService($this->request);
        $this->pipelineService = new PipelineService($this->request);
        $this->tagService = new TagService($this->request);
        $this->taskService = new TaskService($this->request);
        $this->userService = new UserService($this->request);
    }

    public function calls(): CallService
    {
        return $this->callService;
    }

    public function companies(): CompanyService
    {
        return $this->companyService;
    }
    
    public function contacts(): ContactService
    {
        return $this->contactService;
    }

    public function leads(): LeadService
    {
        return $this->leadService;
    }

    public function links(): LinkService
    {
        return $this->linkService;
    }

    public function notes(): NoteService
    {
        return $this->noteService;
    }

    public function tags(): TagService
    {
        return $this->tagService;
    }

    public function tasks(): TaskService
    {
        return $this->taskService;
    }

    public function users(): UserService
    {
        return $this->userService;
    }

    public function pipelines(): PipelineService
    {
        return $this->pipelineService;
    }
    
    // Прямой доступ к request для специфических запросов
    public function customRequest(): Request
    {
        return $this->request;
    }
}