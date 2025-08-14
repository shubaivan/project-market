<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;

#[AsCommand(
    name: 'app:generate-hash',
)]
class GenerateHashCommand extends Command
{
    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly UserPasswordHasherInterface $passwordHashes,
        readonly bool $requirePassword = true
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'psw generator',
            '============',
            '',
        ]);

        try {
            $inMemoryUser = new InMemoryUser('admin', $input->getArgument('password'), ['ROLE_ADMIN']);
            $hashedPassword = $this->passwordHashes->hashPassword(
                $inMemoryUser,
                $input->getArgument('password'),
            );

            $output->writeln($hashedPassword);
        } catch (\Throwable $exception) {
            $output->write(sprintf('<error>%s</error>', $exception->getMessage()));
        }


        $output->write('psw generator.');

        return Command::SUCCESS;
    }
}
