<?php

namespace App\Command;

use App\Entity\Associate;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\ImageOptimizer;

class ImageOptimization extends Command
{
    private $doctrine;
    private $entityManager;
    private $imageOptimizer;
    private $params;

    protected static $defaultName = 'app:img:optimize';

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        ImageOptimizer $imageOptimizer,
        ParameterBagInterface $parameterBag,
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->imageOptimizer = $imageOptimizer;
        $this->params = $parameterBag;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Optimizes all associate images')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $exec = !($input->getOption('dry-run'));
        if (!$exec) $io->note('Dry mode enabled');

        $count = 0;

        $root = $this->params->get('kernel.project_dir').$this->params->get('app.path.public');
        $dirPortrait = $root.$this->params->get('app.path.associates.portrait').'/';
        $dirEntire = $root.$this->params->get('app.path.associates.entire').'/';

        $associates = $this->doctrine->getRepository(Associate::class)->findAll();

        foreach ($associates as $associate) {

            $name = $associate->getFullName();

            if (($img = $associate->getImagePortrait())) {
                $io->writeln(sprintf("%s, portrait, %s", $name, $img));
                if ($exec) $this->imageOptimizer->resize($dirPortrait.$img);
                $count++;
            }

            if (($img = $associate->getImageEntire())) {
                $io->writeln(sprintf("%s, entire, %s", $name, $img));
                if ($exec) $this->imageOptimizer->resize($dirEntire.$img);
                $count++;
            }

            if ($exec) $associate->setUpdatedAt();

        }

        if ($exec) $this->entityManager->flush();

        $io->success(sprintf('Optimized "%d" images.', $count));

        return 0;
    }
}
