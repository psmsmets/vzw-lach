<?php

namespace App\Command;

use App\Entity\Associate;
use App\Service\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageAutorotate extends Command
{
    private $doctrine;
    private $entityManager;
    private $imageOptimizer;
    private $params;

    protected static $defaultName = 'app:img:autorotate';

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
            ->setDescription('Rotate all associate images')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Associate Image Autorotate');

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
                $io->text(sprintf("%s, portrait, %s", $name, $img));
                if ($exec) $this->imageOptimizer->autorotate($dirPortrait.$img);
                if ($exec) $this->imageOptimizer->autorotate($dirPortrait.'thumbs/'.$img);
                $count++;
            }

            if (($img = $associate->getImageEntire())) {
                $io->text(sprintf("%s, entire, %s", $name, $img));
                if ($exec) $this->imageOptimizer->autorotate($dirEntire.$img);
                if ($exec) $this->imageOptimizer->autorotate($dirEntire.'thumbs/'.$img);
                $count++;
            }

            if ($exec) $associate->setUpdatedAt();

        }

        if ($exec) $this->entityManager->flush();

        $io->success(sprintf('Autorotated "%d" images.', $count));

        return 0;
    }
}
