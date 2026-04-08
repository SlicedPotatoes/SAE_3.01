<?php

use Behat\Behat\Context\Context;
use Dotenv\Dotenv;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\JustificationSelectBuilder;
use PHPUnit\Framework\TestCase;
use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Database\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Validator\StudentProfileValidator;

class FeatureContext implements Context
{
    private $start;
    private $end;
    private $etu;
    private $commentaire;
    private $exeption;

    /**
     * @Given /^je suis connecté à un compte étudiant de numéro étudiant "([^"]*)" sur la page de dépot de justificatif$/
     */
    public function jeSuisConnectéÀUnCompteÉtudiantDeNuméroÉtudiantSurLaPageDeDépotDeJustificatif($arg1)
    {
        $this->etu = $arg1;
    }

    /**
     * @Given /^j ai une absence le "([^"]*)" à "([^"]*)" d une durée de "([^"]*)" et "([^"]*)" examen$/
     */
    public function jAiUneAbsenceLeÀDUneDuréeDeEtExamen($arg1, $arg2, $arg3, $arg4)
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2), '/.env.test');
        $dotenv->load();
        if ($arg4 === "sans") {
            $exam = "Non";
        } else {
            $exam = "Oui";
        }
        AbsenceInsertor::addAbsences([["Identifiant" => $this->etu, "Date" => $arg1, "Heure" => $arg2,
            "Durée" => $arg3, "Type" => CourseType::BEN, "Matière" => "Ressource 1", "Groupes" => "BUT INFO 2 Groupe A1",
            "Profs" => "", "Contrôle" => $exam, "Absent/Présent" => "Absence"]]);
    }

    /**
     * @When /^je met en date de départ "([^"]*)" et en date de fin "([^"]*)"$/
     */
    public function jeMetEnDateDeDépartEtEnDateDeFin($arg1, $arg2)
    {
        $this->start = $arg1;
        $this->end = $arg2;
    }

    /**
     * @Given /^j ai entré un commentaire qui dit "([^"]*)"$/
     */
    public function jAiEntréUnCommentaireQuiDit($arg1)
    {
        $this->commentaire = $arg1;
    }

    /**
     * @Given /^j appuie sur le bouton envoyer le justificatif$/
     */
    public function jAppuieSurLeBoutonEnvoyerLeJustificatif()
    {
        try {
            $data = [];
            $data['absenceReason'] = $this->commentaire;
            $data['startDate'] = $this->start;
            $data['endDate'] = $this->end;
            StudentProfileValidator::validationPostJustification($data);
            JustificationService::addJustification(1, $data, []);
        }catch (\Exception $e){
            $this->exeption = $e->getMessage();
        }
    }

    /**
     * @Then /^le nombre de justificatif doit être égale à "([^"]*)"$/
     */
    public function leNombreDeJustificatifDoitÊtreÉgaleÀ($arg1)
    {
        $justsifications = new JustificationSelectBuilder()->dateStart($this->start . " 08:00:00")->dateEnd($this->end . " 08:00:00")->execute();
        TestCase::assertEquals($arg1, count($justsifications));
    }

    /**
     * @Given /^l absence du "([^"]*)" à "([^"]*)" d une durée de "([^"]*)" et "([^"]*)" examen doit être en "([^"]*)"$/
     */
    public function lAbsenceDuÀDUneDuréeDeEtExamenDoitÊtreEn($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        // Crée un objet DateTime à partir du format français
        $dateObj = DateTime::createFromFormat('d/m/Y', $arg1);

        // Retourne la date au format souhaité
        $dateObj->format('Y-m-d');

        $abs = new AbsenceSelectBuilder()->dateStart($dateObj->format('Y-m-d') . " 08:00:00")->
        dateEnd($dateObj->format('Y-m-d') . " 08:00:00")->execute();
        TestCase::assertEquals(StateAbs::from($arg5), $abs[0]->getCurrentState());
    }

    /**
     * @Then /^le message d erreur correspond a (.+)$/
     */
    public function leMessageDErreurCorrespondA($arg1)
    {
        var_dump($this->exeption);
        TestCase::assertEquals($arg1, $this->exeption);
    }
}