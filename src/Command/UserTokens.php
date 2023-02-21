<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserTokens extends Command
{
    private $doctrine;
    private $entityManager;

    protected static $defaultName = 'app:user:tokens';

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate user tokens if missing.')
            ->setHelp("This command generates a user ical and/or csrf token if any is missing.")
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('User :: Tokens');

        $exec = !($input->getOption('dry-run'));
        if (!$exec) $io->note('Dry mode enabled');

        $countIcal = 0;
        $countCsrf = 0;

        $users = $this->doctrine->getRepository(User::class)->findAll();

        foreach ($users as $user)
        {
            if (is_null($user->getIcalToken()) or $user->getIcalToken() === "")
            {
                $io->text(sprintf("Generate ical token for user %s", $user));
                if ($exec) $user->setIcalToken();
                $countIcal++;
            }
            if (is_null($user->getCsrfToken()) or $user->getCsrfToken() === "")
            {
                $io->text(sprintf("Generate csrf token for user %s", $user));
                if ($exec) $user->setCsrfToken();
                $countCsrf++;
            }
        }

        if ($exec) $this->entityManager->flush();

        $io->success(sprintf('Generated "%d" ical and %d csrf tokens.', $countIcal, $countCsrf));

        return 0;

    }
}
