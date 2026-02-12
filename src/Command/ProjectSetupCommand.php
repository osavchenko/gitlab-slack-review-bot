<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Project;
use App\Factory\ProjectFactory;
use App\Repository\ProjectRepository;
use App\Repository\ReviewRepository;
use App\Service\WebhookService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gitlab\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:project:setup')]
class ProjectSetupCommand extends Command
{
    private Client $client;
    private WebhookService $webhookService;
    private ProjectRepository $projectRepository;
    private EntityManagerInterface $entityManager;
    private ProjectFactory $projectFactory;
    private LoggerInterface $logger;
    private ReviewRepository $reviewRepository;

    public function __construct(
        Client $client,
        WebhookService $webhookService,
        ProjectRepository $projectRepository,
        ReviewRepository $reviewRepository,
        EntityManagerInterface $entityManager,
        ProjectFactory $projectFactory,
        LoggerInterface $logger
    ) {
        parent::__construct();

        $this->client = $client;
        $this->webhookService = $webhookService;
        $this->projectRepository = $projectRepository;
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
        $this->projectFactory = $projectFactory;
        $this->logger = $logger;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = 1;

        do {
            $rawProjects = $this->client->projects()->all(['page' => $page]);
            $this->processRawProjects($rawProjects);

            $page++;
        } while (!empty($rawProjects));

        return Command::SUCCESS;
    }

    protected function processRawProjects(array $rawProjects): void
    {
        foreach ($rawProjects as $rawProject) {
            $project = $this->projectRepository->find($rawProject['id']);
            $existingReview = $this->reviewRepository->findOneBy(['project' => $rawProject['id']]);
            if ($project !== null && $existingReview !== null) {
                continue;
            }

            printf('Processing project %s' . PHP_EOL, $rawProject['web_url']);
            if ($project === null) {
                $project = $this->createProject($rawProject);
            }

            $this->createWebhooks($project);
        }

        $this->entityManager->flush();
    }

    private function createProject(array $rawProject): Project
    {
        $project = $this->projectFactory->create($rawProject['id'], $rawProject['web_url']);
        $this->entityManager->persist($project);

        return $project;
    }

    protected function createWebhooks(?Project $project): void
    {
        try {
            $this->webhookService->createForProject($project);
        } catch (Exception $exception) {
            $this->logger->info('Failed to add webhook to project', ['message' => $exception->getMessage()]);
        }
    }
}
