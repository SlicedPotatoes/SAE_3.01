<?php

namespace Uphf\GestionAbsence\Service;

use Uphf\GestionAbsence\Database\Select\JustificationSelector;
use Uphf\GestionAbsence\Exception\EntityNotFoundException;
use Uphf\GestionAbsence\Model\Entity\Justification\Justification;

class JustificationService
{
    /**
     * @param $idJustification
     * @return Justification
     * @throws EntityNotFoundException
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
     * @param $studentId
     * @param $justification
     * @return bool
     */
    public static function ifStudentThenIsItsOwnJustification($studentId, $justification)
    {
        return $justification->getStudent() === $studentId;
    }

    /**
     * @param $justification
     * @param $data
     * @param $absences
     * @return true
     * @throws EntityNotFoundException
     */
    public static function ProcessJustification ($justification, $data, $absences)
    {
        // Données récupérer en post apres application des filtres
        $comment = $data['rejectionReason'];
        $absencesDataPost = $data['absences'];

        // Liste des absences lors d'examen pour le justificatif
        $absencesExemens = [];

        Connection::beginTransaction();

        // Mise à jour du justificatif
        try {
            ProcessJustificatif::execute($justification, $comment);
        }
        catch(BadMethodCallException $e) {
            Connection::rollback();
            throw $e;
        }

        $absencesUpdater = new AbsenceUpdateBuilder();

        // Parcours des absences
        foreach($absences as $abs) {
            // Construction de la clé et récupération des valeurs envoyée en POST pour cette absence
            $key = $abs->getIdAccount() . "_" . $abs->getTime()->format('Y-m-d H:i:s');

            // Cas ou l'absence n'a pas été fournis en POST, ne devrait jamais arriver dans une utilisation normale de l'application
            if(!array_key_exists($key, $absencesDataPost)) {
                Connection::rollback();
                throw new EntityNotFoundException("Absence not found");
            }

            // Récupération du traitement à effectuer sur cette absence
            $values = $absencesDataPost[$key];

            // Remplir une liste avec les absences justifiée lors d'examen
            if($values['state'] == StateAbs::Validated && $abs->getExamen()){
                $absencesExemens[] = $abs;
            }

            // Si l'absence est refusé et que le commentaire du RP est vide
            // On annule le traitement du justificatif (Le RP doit préciser un motif de refus dans le cas ou au moins une abs est refusé).
            if($values['state'] == StateAbs::Refused && $comment === '') {
                Connection::rollback();
                throw new EntityNotFoundException("Comment not found");
            }

            // On charge l'absence dans l'updater et on effectue son traitement
            $absencesUpdater->loadAbsence($abs)->state($values['state'])->allowedJustification(!$values['lock']);
        }

        $absencesUpdater->execute();
        Connection::commit();
        return true;
    }

    /**
     * @param $filters
     * @param $currTab
     * @return array
     */
    public static function getJustificationByFliters($filters, $currTab)
    {
        // Builder pour récupérer les justificatifs
        $justificationToDoBuilder = new JustificationSelectBuilder()->state(StateJustif::NotProcessed);
        $justificationDoneBuilder = new JustificationSelectBuilder()->state(StateJustif::Processed);

        // Application des filtres
        $whiteListMethod = ['dateStart', 'dateEnd', 'examen'];
        $builderCurrTab = $currTab == 'proofToDo' ? $justificationToDoBuilder : $justificationDoneBuilder;
        foreach($filters as $filter => $value) {
            if(isset($value) && in_array($filter, $whiteListMethod)) {
                call_user_func([$builderCurrTab, $filter], $value);
            }
        }

        $justificationToDoBuilder->orderBy(["sendDate"], SortOrder::ASC);
        $justificationDoneBuilder->orderBy(["sendDate"], SortOrder::DESC);

        $justificationsToDo = $justificationToDoBuilder->execute();
        $justificationsDone = $justificationDoneBuilder->execute();

        return [$justificationsToDo, $justificationsDone];
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

}