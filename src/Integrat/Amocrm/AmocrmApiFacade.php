<?php

namespace Integrat\Amocrm;

use Integrat\Amocrm\Repositories\CompanyRepository;
use Integrat\Amocrm\Repositories\ContactRepository;
use Integrat\Amocrm\Repositories\LeadRepository;
use Integrat\Amocrm\Services\CallService;
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
    private CompanyRepository $companyRepository;
    private ContactRepository $contactRepository;
    private LeadRepository $leadRepository;
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
        $this->companyRepository = new CompanyRepository($this->request);
        $this->contactRepository = new ContactRepository($this->request);
        $this->leadRepository = new LeadRepository($this->request);
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

    public function companies(): CompanyRepository
    {
        return $this->companyRepository;
    }
    
    public function contacts(): ContactRepository
    {
        return $this->contactRepository;
    }

    public function leads(): LeadRepository
    {
        return $this->leadRepository;
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