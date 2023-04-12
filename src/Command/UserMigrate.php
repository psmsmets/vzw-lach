<?php

namespace App\Command;

use App\Entity\Associate;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserMigrate extends Command
{
    protected static $defaultName = 'app:user:migrate';

    public function __construct(
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Migrate associate user relations from OneToMany to ManyToMany.')
            ->addOption('persist', 'p', InputOption::VALUE_NONE, 'Persist associates, disables test run.' )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Migrate :: Associates');

        $persist = $input->getOption('persist');
        $io->note(sprintf("Persist = %s", $persist ? 'true' : 'false (test mode)'));

        $associates = $this->doctrine->getRepository(Associate::class)->findAll();
        $count = 0; 

        foreach ($associates as $associate)
        {
            $associate->addUser($associate->getUser());
            $count++;

            if ($persist) $this->entityManager->persist($associate);
        }

        if ($persist) $this->entityManager->flush();

        $io->success(sprintf('Migrated "%d" associates.', $count));

        return 0;

    }
}
