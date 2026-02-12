<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\AuthorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:author:sync')]
class AuthorSyncCommand extends Command
{
    private const ARGUMENT_NAME_GROUP = 'group';

    private AuthorService $authorService;

    public function __construct(
        AuthorService $reviewerService
    ) {
        parent::__construct();

        $this->authorService = $reviewerService;
    }

    protected function configure(): void
    {
        $this->addArgument(
            self::ARGUMENT_NAME_GROUP,
            InputArgument::REQUIRED,
            'For example: @swat/payments. The members of this scope will be synced into database'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scope = $input->getArgument(self::ARGUMENT_NAME_GROUP);
        $this->authorService->syncReviewersByScope($scope);

        return Command::SUCCESS;
    }
}
