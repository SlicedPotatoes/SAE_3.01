<?php

use Behat\Behat\Context\Context;
use Behat\Hook\BeforeSuite;
use Dotenv\Dotenv;
use Respect\Validation\Exceptions\NestedValidationException;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\JustificationSelectBuilder;
use PHPUnit\Framework\TestCase;
use Uphf\GestionAbsence\Database\Select\StudentSelector;
use Uphf\GestionAbsence\Model\Entity\Absence\Absence;
use Uphf\GestionAbsence\Model\Entity\Absence\CourseType;
use Uphf\GestionAbsence\Model\Entity\Absence\Resource;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Database\Insert\AbsenceInsertor;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;
use Uphf\GestionAbsence\Service\JustificationService;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\AbsenceSelectBuilder;
use Uphf\GestionAbsence\Validator\StudentProfileValidator;

class FeatureContext implements Context {
    private string $start;
    private string $end;
    private Student $etu;
    private string $commentaire;
    private string $exception;
    private Justification $justification;

    #[BeforeSuite]
    // Charger l'environnement de test, pour se connecter à la base de données de test.
    public static function loadEnv(): void {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2), '/.env.test');
        $dotenv->load();
    }

    /**
     * @Given /^je suis connecté à un compte étudiant de numéro étudiant (.*) sur la page de dépot de justificatif$/
     */
    public function jeSuisConnectéÀUnCompteÉtudiantDeNuméroÉtudiantSurLaPageDeDépotDeJustificatif($idStudent) {
        $this->etu = StudentSelector::getStudentById($idStudent);
    }

    /**
     * @Given /^j ai une absence le (.*), (.*), avec comme etat (.*)$/
     */
    public function jAiUneAbsenceLe($date, $allowJustification, $state) {
        AbsenceInsertor::insertAbsences([
            new Absence(
                $this->etu,
                DateTime::createFromFormat('Y-m-d H:i:s', $date),
                '1:30',
                false,
                $allowJustification === "justifiable",
                null,
                StateAbs::from($state),
                CourseType::from("TD"),
                new Resource(1, "R4.01"),
                null
            )
        ]);
    }

    /**
     * @When /^je met en date de départ (.*) et en date de fin (.*)$/
     */
    public function jeMetEnDateDeDépartEtEnDateDeFin($start, $end) {
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @When /^j ai entré un commentaire qui dit (.*)$/
     */
    public function jAiEntréUnCommentaireQuiDit($comment) {
        $this->commentaire = $comment;
    }

    /**
     * @When /^j appuie sur le bouton envoyer le justificatif$/
     */
    public function jAppuieSurLeBoutonEnvoyerLeJustificatif() {
        try {
            $data = [
                'absenceReason' => $this->commentaire,
                'startDate' => $this->start,
                'endDate' => $this->end,
            ];

            StudentProfileValidator::validationPostJustification($data);
            JustificationService::addJustification($this->etu->getIdAccount(), $data, []);
        }
        catch (NestedValidationException $e) {
            // NestedValidationException est retournée quand les données saisies ne sont pas au format attendu,
            // elle contient un tableau de messages d'erreur.
            // Une case par règle de validation qui n'est pas respectée.
            // Par exemple, le format de la date de début et de fin ne sont pas respectée,
            // il y aura une case pour la date de début et une autre pour la date de fin.
            // On récupère la première case du tableau de messages d'erreur,
            // qui correspond à la première règle de validation qui n'est pas respectée.
            $errors = $e->getMessages();
            $this->exception = reset($errors);
        }
        catch (\Exception $e) {
            $this->exception = $e->getMessage();
        }
    }

    /**
     * @Then /^le justificatif (.*) dans la base de données$/
     */
    public function leJustificatifEstPrésentDansLaBaseDeDonnées($present) {
        $count = $present === "est présent" ? 1 : 0;

        $justsifications = new JustificationSelectBuilder()->dateStart($this->start)->dateEnd($this->end)->execute();
        TestCase::assertEquals($count, count($justsifications));

        if($count === 1) {
            $this->justification = $justsifications[0];
        }
    }

    /**
     * @Then /^le motif du justificatif doit être (.*)$/
     */
    public function leMotifDuJustificatifDoitÊtre($comment) {
        TestCase::assertEquals($this->justification->getCause(), $comment);
    }

    /**
     * @Then /^l absence du (.*) doit être en (.*) et (.*)$/
     */
    public function lAbsenceDuDoitÊtreEn($date, $result, $allowJustification) {
        $allowJustification = $allowJustification === "justifiable";

        $abs = new AbsenceSelectBuilder()->dateStart($date)->dateEnd($date)->execute();

        TestCase::assertEquals(StateAbs::from($result), $abs[0]->getCurrentState());
        TestCase::assertEquals($allowJustification, $abs[0]->getAllowedJustification());
    }

    /**
     * @Then /^l absence du (.*) doit (.*) au justificatif$/
     */
    public function lAbsenceDuDoitAuJustificatif($date1, $lier) {
        $absences = $this->justification->getAbsences();
        $estLier = $lier === "etre lier";

        foreach ($absences as $abs) {
            if($abs->getTime()->format('Y-m-d H:i:s') === $date1) {
                if(!$estLier) {
                    TestCase::fail("Absence du $date1 est lié au justificatif alors que ce n'est pas censé être le cas");
                }

                // Aucun problème, l'absence est bien lié au justificatif.
                return;
            }
        }

        if($estLier) {
            TestCase::fail("Absence du $date1 n'est pas lié au justificatif alors que c'est censé être le cas");
        }
    }

    /**
     * @Then /^le message d erreur correspond a "(.+)"$/
     */
    public function leMessageDErreurCorrespondA($arg1)
    {
        TestCase::assertEquals($arg1, $this->exception);
    }
}