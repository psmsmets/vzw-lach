<?php

namespace App\Service;

use App\Service\SpreadsheetService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class AssociateExport {

    public function __construct(
        private LoggerInterface $logger,
        public Security $security,
        private SpreadsheetService $spreadsheet,
    )
    {}

    public function exportBdays(array $associates) : void
    {
        $user = $this->security->getUser();
        $this->logger->info(sprintf("User %s (%s) requested to export %d associate birthdays.",
                                    $user, $user->getEmail(), count($associates)));

        $headers = ['verjaardag', 'naam', 'voornaam', 'leeftijd'];
        $datas = [];
        $refdate = new \DateTimeImmutable('this year 12/31');

        foreach ($associates as $associate) {

            if (is_null($associate->getDetails()->getBirthdate()) or !$associate->isEnabled()) continue;

            $data = [
                $associate->getDetails()->getBirthday()->format('Y-m-d'),
                $associate->getLastname(),
                $associate->getFirstname(),
                $associate->getDetails()->getAge($refdate),
            ];

            $datas[] = $data;
        }

        asort($datas);

        $this->spreadsheet->export('HGCVHKV verjaardagen', $datas, $headers);

        return;
    }

    public function exportDetails(array $associates) : void 
    {
        $user = $this->security->getUser();
        $this->logger->info(sprintf("User %s (%s) requested to export %d associate details.",
                                    $user, $user->getEmail(), count($associates)));

        $headers = ['naam', 'voornaam', 'adres', 'geboortedatum', 'functieomschrijving', 'groep(en)'];
        $datas = [];

        foreach ($associates as $associate) {

            if (!$associate->isEnabled() or count($associate->getCategories()) == 0) continue;

            $bday = $associate->getDetails()->getBirthdate();

            $data = [
                $associate->getLastname(),
                $associate->getFirstname(),
                $associate->getAddress()->getAddress(),
                $bday ? $bday->format('Y-m-d') : 'n/a',
                $associate->isOnstage() ? 'acteur/figurant' : 'vrijwilliger',
                $associate->getCategoryNames(),
            ];

            $datas[] = $data;
        }

        asort($datas);

        $this->spreadsheet->export('HGCVHKV ledendetails', $datas, $headers);

        return;
    }
}
