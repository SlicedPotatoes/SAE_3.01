<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Connection;
use Uphf\GestionAbsence\Database\Select\JustificationSelector;
use Uphf\GestionAbsence\Database\Select\SelectBuilder\JustificationSelectBuilder;
use Uphf\GestionAbsence\Database\Update\ProcessJustificatif;
use Uphf\GestionAbsence\Database\Update\UpdateBuilder\AbsenceUpdateBuilder;
use Uphf\GestionAbsence\Exception\AbsenceNotProvidedException;
use Uphf\GestionAbsence\Exception\CommentEducationalManagerNotProvidedException;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Entity\Account\Student;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;

class JustificationService
{
    /**
     * Renvoie un justificatif à partir de son ID
     *
     * @param $idJustification
     * @return Justification
     * @throws EntityNotFoundException Dans le cas ou aucun justificatif n'existe pour cet ID
     */
    public static function getJustificationById($idJustification): Justification
    {
        $justification = JustificationSelector::getJustificationById($idJustification);
        if($justification === null) {
            throw new EntityNotFoundException("Justification not found");
        }
        return $justification;
    }

    /**
     * Renvoie true si le justificatif appartient à l'étudiant, sinon false.
     *
     * @param Student $student
     * @param Justification $justification
     * @return bool
     */
    public static function isJustificationOwnedByStudent(Student $student, Justification $justification): bool {
        return $justification->getStudent()->getIdAccount() === $student->getIdAccount();
    }

    /**
     * Traitement une justificatif et mise à jour des absences lié au justificatif
     *
     * @param Justification $justification
     * @param array $data
     * @return void
     * @throws AbsenceNotProvidedException Dans le cas ou une absence lié au justificatif n'a pas été fournis avec les informations de comment la traité
     * @throws CommentEducationalManagerNotProvidedException Dans le cas ou une absence est refusé et que le RP n'a pas fournis de raison
     */
    public static function processJustification (Justification $justification, array $data): void {
        // Données récupérer en post apres application des filtres
        $comment = $data['rejectionReason'];

        // Liste des absences lors d'examen pour le justificatif
        $absencesExemens = [];

        Connection::beginTransaction();

        // Mise à jour du justificatif
        try {
            ProcessJustificatif::execute($justification, $comment);
        }
        catch(\BadMethodCallException $e) {
            Connection::rollback();
            throw $e;
        }

        $absencesUpdater = new AbsenceUpdateBuilder();

        // Parcours des absences
        foreach($justification->getAbsences() as $abs) {
            // Construction de la clé et récupération des valeurs envoyée en POST pour cette absence
            $key = $abs->getIdAccount() . "_" . $abs->getTime()->format('Y-m-d H:i:s');

            // Cas ou l'absence n'a pas été fournis, ne devrait jamais arriver dans une utilisation normale de l'application
            if(!array_key_exists($key, $data['absences'])) {
                Connection::rollback();
                throw new AbsenceNotProvidedException("Absence not provided: " . $key);
            }

            // Récupération du traitement à effectuer sur cette absence
            $values = $data['absences'][$key];

            // Remplir une liste avec les absences justifiée lors d'examen
            if($values['state'] == StateAbs::Validated && $abs->getExamen()){
                $absencesExemens[] = $abs;
            }

            // Si l'absence est refusé et que le commentaire du RP est vide
            // On annule le traitement du justificatif (Le RP doit préciser un motif de refus dans le cas ou au moins une abs est refusé).
            if($values['state'] == StateAbs::Refused && $comment === '') {
                Connection::rollback();
                throw new CommentEducationalManagerNotProvidedException("Comment is required when at least one absence is refused");
            }

            // On charge l'absence dans l'updater et on effectue son traitement
            $absencesUpdater->loadAbsence($abs)->state($values['state'])->allowedJustification(!$values['lock']);
        }

        $absencesUpdater->execute();
        Connection::commit();

        // Pours chacune des absences justifiées lors d'examen, on envoie un mail au professeur et l'étudiant
        foreach ($absencesExemens as $absExam) {
            MailService::sendMailExam($absExam);
        }

        // Envoie du mail à l'étudiant pour le prévenir du traitement de son justificatif
        $student = $justification->getAbsences()[0]->getStudent();
        MailService::sendProcessedJustification($student, $justification);
    }

    /**
     * @param $idStudent
     * @param $data
     * @param $files
     * @return void
     */
    public static function addJustification($idStudent, $data, $files)
    {
        $student = StudentSelector::getStudentById($idStudent);
        try {
            // Créer le justificatif dans la BDD
            JustificationInsertor::insert(
                $student->getIdAccount(),
                $data['absenceReason'],
                $data['startDate'],
                $data['endDate'],
                $files
            );
            return;
        }
            // Exception levée par JustificationInsertor::insert, quand il n'y a pas d'absence justifiable sur la période sélectionnée
        catch (InvalidArgumentException $e) {
            // S'il y a eu une erreur critique pendent la création du justificatif, supprimer les fichiers du dossier upload
            FileUpload::deleteFiles($files);
            throw $e;
        }
        catch (Exception $e) {
            error_log("Créer Justification: " . $e->getMessage());
            // S'il y a eu une erreur critique pendent la création du justificatif, supprimer les fichiers du dossier upload
            FileUpload::deleteFiles($files);
            throw $e;
        }
    }

    /**
     * Cette méthode permet de récupérer les justificatifs d'un étudiant en fonction de différents filtres.
     *
     * Le tableau `$sortOptions` doit contenir les clés suivantes :
     *  - "columns" : un tableau de colonnes sur lesquels le trie sera éffectuer
     *  - "sortOrder" : Un objet de l'enum SortOrder
     *
     * @param $filters
     * @param array $sortOptions
     * @return array
     */
    public static function getJustificationsWithFilters($filters, array $sortOptions) : array
    {
        $builder = new JustificationSelectBuilder();

        // Application des filtres
        $whiteListMethod = ['dateStart', 'dateEnd', 'state', 'examen', 'idStudent'];

        foreach ($filters as $filter => $value) {
            if(isset($value) && in_array($filter, $whiteListMethod)) {
                call_user_func([$builder, $filter], $value);
            }
        }

        if(!empty($sortOptions['columns']) && $sortOptions['sortOrder'] !== null) {
            $builder->orderBy($sortOptions['columns'], $sortOptions['sortOrder']);
        }

        return $builder->execute();
    }

}