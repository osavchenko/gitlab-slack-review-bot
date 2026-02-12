<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AuthorRepository;
use Gitlab\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:author:inconsistencies')]
class AuthorInconsistenciesCommand extends Command
{
    private Client $client;
    private AuthorRepository $authorRepository;

    public function __construct(
        Client $client,
        AuthorRepository $authorRepository
    ) {
        parent::__construct();

        $this->client = $client;
        $this->authorRepository = $authorRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $allAuthors = $this->authorRepository->findAll();
        foreach ($allAuthors as $author) {
            $user = $this->client->users()->show($author->getId());
            if ($user['username'] !== $author->getUsername()) {
                $output->writeln(
                    sprintf(
                        'Inconsistency found for author id %d: actual username is %s, when local username is %s',
                        $author->getId(),
                        $user['username'],
                        $author->getUsername()
                    )
                );
            }
        }

        return Command::SUCCESS;
    }
}

