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

class UserIcalToken extends Command
{
    private $doctrine;
    private $entityManager;

    protected static $defaultName = 'app:user:icaltoken';

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
            ->setDescription('Generate user ical token if missing.')
            ->setHelp("This command generates a user ical token if no token is currently set.")
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('User :: Ical Token');

        $exec = !($input->getOption('dry-run'));
        if (!$exec) $io->note('Dry mode enabled');

        $count = 0;

        $users = $this->doctrine->getRepository(User::class)->findAll();

        foreach ($users as $user)
        {
            if (is_null($user->getIcalToken()) or $user->getIcalToken() === "")
            {
                $io->text(sprintf("Generate token for user %s", $user));
                if ($exec) $user->setIcalToken();
                $count++;
            }
        }

        if ($exec) $this->entityManager->flush();

        $io->success(sprintf('Generated "%d" ical tokens.', $count));

        return 0;

    }
}
