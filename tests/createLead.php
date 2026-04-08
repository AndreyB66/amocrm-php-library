<?php

require_once __DIR__ . '/../config.php';

use Integrat\Amocrm\Repositories\ContactRepository;
use Integrat\Amocrm\Request;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

$request = new Request($_ENV['AMOCRM_DOMAIN'], $_ENV['AMOCRM_ACCESS_TOKEN']);
$repo = new ContactRepository($request);

$res = $repo->createLead(47967303, [[
    'pipeline_id' => 6725478,
    'status_id' => 56919070
]]);

error_log(print_r($res, true));