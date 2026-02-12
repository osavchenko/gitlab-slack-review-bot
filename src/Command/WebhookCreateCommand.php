<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\WebhookService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:webhook:create')]
class WebhookCreateCommand extends Command
{
    private WebhookService $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        parent::__construct();

        $this->webhookService = $webhookService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->webhookService->createForAllProjects();

        return Command::SUCCESS;
    }
}
