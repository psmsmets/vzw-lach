<?php

namespace App\Command;

use App\Entity\Event;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ImportEvents extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:events:import';
    private $categoryRepository;
    private $eventRepository;
    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository, SluggerInterface $slugger)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->slugger = $slugger;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import calendar events from a csv list.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command imports calendar events given an escaped csv list with events.

            Columns:
                * [0] : date
                * [1] : starttime
                * [2] : endtime
                * [3] : title
                * [4] : category
                * [5] : location
                * [6] : body

A new event is created only if it does not yet exist (given the starttime and endtime and the title).

Dates are formatted dd/mm/yy (format d/m/Y).
Times are formatted HH:MM (format H:I).

CSV lines are processed by php fgetcsv with default parameters (comma separated: \",\", '\"' , '\"').
No escaping is required converting the .xls to .csv.

By default no data is persisted to the database.

"
            )

            ->addArgument('csv_file', InputArgument::REQUIRED, 'An escaped csv eventlist')
            ->addOption('skip', 's', InputOption::VALUE_OPTIONAL, 'Number of header lines to skip.', 1 )
            ->addOption('persist', 'p', InputOption::VALUE_NONE, 'Persist events, disables test run.' )
            ->addOption('debug', 'd', InputOption::VALUE_NONE, 'Output csv lines to stdout.' )
            ->addOption('publish', 'u', InputOption::VALUE_NONE, 'Publish events.' )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);

        $io->title('Events :: Import');

        /*
            Columns:
                * [0] : date
                * [1] : starttime
                * [2] : endtime
                * [3] : title
                * [4] : category
                * [5] : location
                * [6] : body
        */

        $cnt = -abs( (int) $input->getOption('skip') );
        $new = 0;

        $persist = $input->getOption('persist');
        $debug = $input->getOption('debug');
        $publish = $input->getOption('publish');
        
        $io->note(sprintf("Persist = %s", $persist ? 'true' : 'false (test mode)'));
        $io->note(sprintf("Publish = %s\n", $publish ? 'true' : 'false'));

        // Open the file for reading
        if ( ($h = fopen($input->getArgument('csv_file'), "r")) !== false ) 
        {
            // Convert each line into the local $data variable
            while ( ($data = fgetcsv($h, 0, ",", '"' , '"')) !== false ) 
            {
                $cnt++;
                if ($cnt <= 0 ) continue;

                if ($debug) $io->info(print_r($data));

                // extract and convert?
                $allDay = ($data[1] === '' and $data[2] === '');

                if ($allDay) {
                    $startTime = \DateTimeImmutable::createFromFormat('d/m/Y', $data[0]);
                    $endTime = null;
                } else {
                    $startTime = \DateTimeImmutable::createFromFormat(
                        'd/m/Y H:i', sprintf('%s %s', $data[0], $data[1])
                    );
                    $endTime = $data[2] === '' ? null : \DateTimeImmutable::createFromFormat(
                        'd/m/Y H:i', sprintf('%s %s', $data[0], $data[2])
                    );
                }

                if ($startTime === false or $endTime === false) continue;

                $event = new Event();

                $event->setPublished($publish);
                $event->setAllday($allDay);
                $event->setStartTime($startTime);
                $event->setEndTime($endTime);
                $event->setTitle($data[3]);
                $event->setSlug($this->slugger->slug($data[3]));
                if ($data[5]) $event->setLocation($data[5]);
                if ($data[6]) $event->setBody($data[6]);

                foreach (explode(',', $data[4]) as $cat) {

                    $category = $this->categoryRepository->find((int) trim($cat));
                    if (!is_null($category)) $event->addCategory($category);

                }

                if ($debug) $io->info($event);

                if ($persist) {

                    $this->em->persist($event);
                    $this->em->flush();
                }

                $io->text(sprintf('%s created with id:%s.', $event, $event->getId()));

                $new++;

            }

            // Close the file
            fclose($h);
        }

        $io->success(sprintf('%d events found of which %d imported.', $cnt, $new));

        return 0;

    }
}
