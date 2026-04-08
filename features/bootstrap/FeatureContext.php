<?php

use Dotenv\Dotenv;
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
    private ?string $start;
    private $end;
    private $etu;
    private $absence = [];
    private $commentaire;
    public function __construct()
    {
    }

    #[\Behat\Hook\BeforeFeature]
    public static function prepareForTheFeature()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__,2) , '/.env.test');
        $dotenv->load();
        $abs = [];
        $abs1 = ["Identifiant" => "22400227","Date"=>"04/04/2026","Heure"=>"08H00","Durée"=>"01H30",
            "Type"=>CourseType::BEN,"Matière"=>"Ressource 1","Groupes"=>"BUT INFO 2 Groupe A1","Profs"=>"","Contrôle"=>"Non",
            "Absent/Présent"=>"Absence"];
        $abs2 = ["Identifiant" => "22400227","Date"=>"04/03/2026","Heure"=>"08H00","Durée"=>"01H30",
            "Type"=>CourseType::BEN,"Matière"=>"Ressource 1","Groupes"=>"BUT INFO 2 Groupe A1","Profs"=>"","Contrôle"=>"Non",
            "Absent/Présent"=>"Absence"];
        $abs3 = ["Identifiant" => "22400227","Date"=>"04/02/2026","Heure"=>"08H00","Durée"=>"01H30",
            "Type"=>CourseType::BEN,"Matière"=>"Ressource 1","Groupes"=>"BUT INFO 2 Groupe A1","Profs"=>"","Contrôle"=>"Non",
            "Absent/Présent"=>"Absence"];
        $abs4 = ["Identifiant" => "22400227","Date"=>"04/01/2026","Heure"=>"08H00","Durée"=>"01H30",
            "Type"=>CourseType::BEN,"Matière"=>"Ressource 1","Groupes"=>"BUT INFO 2 Groupe A1","Profs"=>"","Contrôle"=>"Non",
            "Absent/Présent"=>"Absence"];
        $abs[] = $abs1;
        $abs[] = $abs2;
        $abs[] = $abs3;
        $abs[] = $abs4;
        AbsenceInsertor::addAbsences($abs);
    }

    #[Given('que je suis étudiant et que je suis sur la page de dépôt d’un justificatif')]
    public function prepareContext(){
        $this->exp = null;
    }

    #[When('renseigne correctement les informations')]
    public function renseigneCorrect(){
        $this->start = "2026-04-01";
        $this->end = "2026-04-30";
        try {
            $data = [];
            $data['absenceReason'] = "test";
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    #[Then('le justificatif est enregistré correctement dans la base de données')]
    public function checkOutcome(){
        $justsifications = new JustificationSelectBuilder()->dateStart($this->start." 08:00:00")->dateEnd($this->end." 08:00:00")->execute();
        TestCase::assertEquals(1,count($justsifications));
    }

    #[When('je renseigne correctement les informations mais qu’aucune absence n’est enregistrée entre ces dates')]
    public function pasDAbsence(){
        $this->start = "2026-05-01";
        $this->end = "2026-05-30";
        try {
            $data = [];
            $data['absenceReason'] = "test";
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    #[Then('le justificatif n’est pas enregistré et un message d’erreur est affiché')]
    public function messageErreur(){
        $justsifications = new JustificationSelectBuilder()->dateStart($this->start." 08:00:00")->dateEnd($this->end." 08:00:00")->execute();
        TestCase::assertEquals(0,count($justsifications));
    }

    #[When('je renseigne correctement les informations mais que la date de début est ultérieure à la date de fin')]
    public function dateDebutUterieurADateFin ()
    {
        $this->start = "2026-03-30";
        $this->end = "2026-03-01";
        try {
            $data = [];
            $data['absenceReason'] = "test";
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    #[When('je renseigne correctement les informations mais que le commentaire est vide')]
    public function pasDeCommentaire ()
    {
        $this->start = "2026-02-01";
        $this->end = "2026-02-28";
        try {
            $data = [];
            $data['absenceReason'] = "";
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    #[When('je renseigne des informations incohérentes')]
    public function donneeIncoerente ()
    {
        $this->start = "2026-01-01";
        $this->end = 20260130;
        try {
            $data = [];
            $data['absenceReason'] = "";
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exp = $e;
        }
    }

    /**
     * @Given /^je suis connecté à un compte étudiant de numéro étudiant "([^"]*)"$/
     */
    public function jeSuisConnectéÀUnCompteÉtudiantDeNuméroÉtudiant($arg1)
    {
        $this->etu = $arg1;
    }
    /**
     * @Given /^je suis sur la page de dépot de justificatif$/
     */
    public function jeSuisSurLaPageDeDépotDeJustificatif()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__,2) , '/.env.test');
        $dotenv->load();
        AbsenceInsertor::addAbsences([["Identifiant" => $this->etu,"Date"=>$this->absence["Date"],"Heure"=>$this->absence["Heure"],
            "Durée"=>$this->absence["Duree"],"Type"=>CourseType::BEN,"Matière"=>"Ressource 1","Groupes"=>"BUT INFO 2 Groupe A1",
            "Profs"=>"","Contrôle"=>$this->absence["Exam"], "Absent/Présent"=>"Absence"]]);
    }

    /**
     * @When /^je met en date de départ "2026\-04\-01"([^"]*)"2026\-04\-30"$/
     */
    public function jeMetEnDateDeDépartEtEnDateDeFin($arg1, $arg2)
    {
        $this->start = $arg1;
        $this->end = $arg2;
    }

    /**
     * @When  /^j'appuie sur le bouton envoyer le justificatif$/
     */
    public function jAppuieSurLeBoutonEnvoyerLeJustificatif()
    {
        $data = [];
        $data['absenceReason'] = $this->commentaire;
        $data['startDate'] = $this->start;
        $data['endDate'] = $this->end;
        JustificationService::addJustification(1, $data, []);
    }

    /**
     * @Then /^le justificatif est présent dans ma liste justificative$/
     */
    public function leJustificatifEstPrésentDansMaListeJustificative()
    {
        $justsifications = new JustificationSelectBuilder()->dateStart($this->start." 08:00:00")->dateEnd($this->end." 08:00:00")->execute();
        TestCase::assertEquals(1,count($justsifications));
    }

    /**
     * @Given /^j ai une absence le "([^"]*)" à "([^"]*)" d une durée de "([^"]*)" et "([^"]*)" examen$/
     */
    public function jAiUneAbsenceLeÀDUneDuréeDeEtExamen($arg1, $arg2, $arg3, $arg4)
    {
        $this->absence["Date"] = $arg1;
        $this->absence["Heure"] = $arg2;
        $this->absence["Duree"] = $arg3;
        if($arg4 === "sans") {
            $this->absence["Exam"] = "Non";
        }else{
            $this->absence["Exam"] = "Oui";
        }
    }
}