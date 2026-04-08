<?php

require_once __DIR__ . '/../config.php';

use Integrat\Amocrm\Repositories\ContactRepository;
use Integrat\Amocrm\Request;
use Integrat\Amocrm\Models\LeadModel;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

$request = new Request($_ENV['AMOCRM_DOMAIN'], $_ENV['AMOCRM_ACCESS_TOKEN']);
$repo = new ContactRepository($request);

/** @var LeadModel[] $leads */
$leads = $repo->findAllLeads(47967303);

foreach ($leads as $lead) {
    echo $lead->id . PHP_EOL;
}