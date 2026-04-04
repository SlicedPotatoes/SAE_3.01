<?php

use Uphf\GestionAbsence\Database\Select\SelectBuilder\JustificationSelectBuilder;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\When;
use Behat\Step\Then;
use PHPUnit\Framework\TestCase;
use Uphf\GestionAbsence\Database\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Database\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Service\JustificationService;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private Exception $exp;
    public function __construct()
    {
    }

    #[\Behat\Hook\BeforeFeature]
    public static function prepareForTheFeature()
    {
        $abs = [];
        $abs[] = new Absence(StudentSelector::getStudentById(1),
            DateTime::createFromFormat("Y-m-d H:i:s", "2026-04-04 08:00:00"),
            "0 years 0 mons 0 days 1 hours 30 mins 0.0 secs",
            false,
            true,
            null,
            StateAbs::NotJustified,
            CourseType::BEN,
            new Resource(1,"idk"),
            null
        );
        $abs[] = new Absence(StudentSelector::getStudentById(1),
            DateTime::createFromFormat("Y-m-d H:i:s", "2026-03-04 08:00:00"),
            "0 years 0 mons 0 days 1 hours 30 mins 0.0 secs",
            false,
            true,
            null,
            StateAbs::NotJustified,
            CourseType::BEN,
            new Resource(1,"idk"),
            null
        );
        $abs[] = new Absence(StudentSelector::getStudentById(1),
            DateTime::createFromFormat("Y-m-d H:i:s", "2026-02-04 08:00:00"),
            "0 years 0 mons 0 days 1 hours 30 mins 0.0 secs",
            false,
            true,
            null,
            StateAbs::NotJustified,
            CourseType::BEN,
            new Resource(1,"idk"),
            null
        );
        $abs[] = new Absence(StudentSelector::getStudentById(1),
            DateTime::createFromFormat("Y-m-d H:i:s", "2026-01-04 08:00:00"),
            "0 years 0 mons 0 days 1 hours 30 mins 0.0 secs",
            false,
            true,
            null,
            StateAbs::NotJustified,
            CourseType::BEN,
            new Resource(1,"idk"),
            null
        );
        AbsenceInsertor::addAbsences($abs);
    }

    #[Given('que je suis étudiant et que je suis sur la page de dépôt d’un justificatif')]
    public function prepareContext(){
        $this->exp = null;
    }

    #[When('renseigne correctement les informations')]
    public function renseigneCorrect(){
        try {
            $data = [];
            $data['absenceReason'] = "test";
            $data['startDate'] = DateTime::createFromFormat("Y-m-d H:i:s", "2026-04-01 08:00:00");
            $data['endDate'] = DateTime::createFromFormat("Y-m-d H:i:s", "2026-04-10 08:00:00");
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    #[Then('le justificatif est enregistré correctement dans la base de données')]
    public function checkOutcome(){
        $justsifications = new JustificationSelectBuilder()->dateStart("2026-04-01 08:00:00")->execute();
        TestCase::assertEquals(1,count($justsifications));
    }
}