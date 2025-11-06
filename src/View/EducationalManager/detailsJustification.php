<?php
use Uphf\GestionAbsence\Model\Justification\Justification;
use Uphf\GestionAbsence\Model\Justification\StateJustif;
use Uphf\GestionAbsence\Model\Absence\StateAbs;
use Uphf\GestionAbsence\Model\Account\AccountType;

$idJustification = $_GET['id'] ?? null;
$justification = Justification::getJustificationById($idJustification);
$absences = $justification->getAbsences();
$files = $justification->getFiles();
$currentState = $justification->getCurrentState();
$accountType = $_SESSION['account']->getAccountType();
$isEducationalManager = $accountType === AccountType::EducationalManager;

$h2 = "Jusificatif ";

if ($isEducationalManager)
{
    $h2 .= " de " . $justification->getStudent()->getFirstName() . " " . $justification->getStudent()->getLastName() . ", ";
}
$h2 .= "du " . $justification->getSendDate()->format('d/m/Y')

?>

<div class="card flex-fill d-flex flex-column gap-3 mt-4 p-3" style="min-height: 0">
    <!-- Information principale d'un justificatifs -->
    <div>
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-1"><?=$h2?></h2>
            <div class="d-flex gap-3 align-items-center">
                <?php if($isEducationalManager): ?>
                <a href="index.php?currPage=studentProfile&studentId=<?= $justification->getStudent()->getIdAccount() ?>" class="btn btn-uphf">Profil Étudiant</a>
                <?php endif; ?>
                <span class="badge rounded-pill text-bg-<?= $currentState->colorBadge() ?> fs-6 px-3 py-2">
                    <?= $currentState->label() ?>
                </span>
            </div>
        </div>

        <div class="col-md-6"><strong>Date début :</strong> <?= $justification->getStartDate()->format('d/m/Y') ?></div>
        <div class="col-md-6"><strong>Date fin :</strong> <?= $justification->getEndDate()->format('d/m/Y') ?></div>
        <?php if($justification->getProcessedDate() !== null) : ?>
        <div class="col-md-6"><strong>Date de traitement :</strong> <?= $justification->getProcessedDate()->format('d/m/Y') ?> </div>
        <?php endif; ?>
    </div>

    <!-- Liste des absences -->
    <div class="d-flex flex-column" style="flex: 1 1 30%; min-height: 0">
        <div class="d-flex align-items-center mb-2">
            <h4>Absences</h4>

            <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
                <div class="btn-group gap-1 ms-auto" id="absence-cta-rp-all">
                    <button type="button" class="btn btn-outline-success rounded">Tout valider</button>
                    <button type="button" class="btn btn-danger d-none"><i class="bi bi-lock"></i></button>
                </div>
            <?php endif; ?>
        </div>

        <div class="border-top flex-fill overflow-y-auto" style="min-height: 0">
            <?php foreach ($absences as $absence): ?>
                <div class="d-flex align-items-center border-bottom py-2">
                    <!-- Date de l'absence -->
                    <div class="me-3">
                        <div>Date: <?= $absence->getTime()->format('d/m/Y H:i') ?></div>
                        <div>Durée: <?= $absence->getDuration() ?></div>
                    </div>

                    <!-- Tag examen -->
                    <?php if($absence->getExamen()): ?>
                        <span class='badge rounded-pill text-bg-warning ms-3 me-2'>Examen</span>
                    <?php endif; ?>

                    <!-- Etat de l'absence -->
                    <!-- Si le justificatif n'est pas traité, et qu'on est le RP, on peut choisir de validé ou non l'abs -->
                    <?php if ($currentState === StateJustif::NotProcessed && $isEducationalManager): ?>
                        <div class="ms-auto me-3">
                            <input type="hidden"
                                   form="validateJustificationForm"
                                   name="absences[<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>][state]"
                                   value="Validated"
                                   class="absence-input-state">
                            <input type="hidden"
                                   form="validateJustificationForm"
                                   name="absences[<?= $absence->getIdAccount() ?>_<?= $absence->getTime()->format('Y-m-d H:i:s') ?>][lock]"
                                   value="true"
                                   class="absence-input-lock">
                            <div class="btn-group gap-1 absence-cta-rp">
                                <button type="button" class="btn btn-outline-success absence-btn-state rounded">Validé</button>
                                <button type="button" class="btn btn-danger absence-btn-lock d-none"><i class="bi bi-lock"></i></button>
                            </div>
                        </div>
                    <!-- Dans les autres cas, on affiche seulement l'état de l'absence -->
                    <?php else: ?>
                        <span class="badge rounded-pill text-bg-<?= $absence->getCurrentState()->colorBadge() ?>">
                            <?= $absence->getCurrentState()->label() ?>
                        </span>
                    <?php endif; ?>

                    <!-- Locked -->
                    <?php if(!$absence->getAllowedJustification() && ($absence->getCurrentState() == StateAbs::Refused || $absence->getCurrentState() == StateAbs::NotJustified)): ?>
                        <i style="font-size: 30px" class="bi bi-file-lock2" data-bs-toggle="tooltip" data-bs-title="Le responsable pédagogique n'autorise pas la justification de cette absence"></i>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Motif de l'absence & Justificatifs côte à côte (toujours affiché) -->
    <div class="row" style="flex: 1 1 20%; min-height: 0">
        <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
            <h4>Motif de l'absence :</h4>
            <div class="border rounded p-2 overflow-y-auto flex-fill" style="min-height:0">
                <?= htmlspecialchars($justification->getCause()) ?>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column h-100" style="min-height: 0">
            <h4>Justificatifs :</h4>
            <div class="border rounded overflow-y-auto flex-fill" style="min-height:0">
                <?php if (empty($files)): ?>
                    <li class="p-2">Aucun fichier justificatif.</li>
                <?php endif; ?>
                <?php foreach ($files as $file): ?>
                    <div class="border-bottom p-2 border-0 border-bottom d-flex justify-content-between align-items-center pe-2">
                        <span title="<?= htmlspecialchars($file->getFileName()) ?>" class="text-truncate"><?= htmlspecialchars($file->getFileName()) ?></span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-danger bi bi-eye me-1" title="Voir" type="button"></button>
                            <a class="btn btn-outline-primary bi bi-download" title="Télécharger" href="" download></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Bloc Motif du refus -->
    <div class="flex-fill d-flex flex-column" style="flex: 1 1 20%; min-height: 0">
        <?php if( !($currentState === StateJustif::NotProcessed && !$isEducationalManager) ): ?>
            <label for="JustificationRejectionReason" class="h4 mb-2">Commentaire :</label>
        <?php endif; ?>
        <?php if($currentState === StateJustif::NotProcessed): ?>
            <?php if ($accountType === AccountType::EducationalManager): ?>
                <form class="flex-fill d-flex flex-column" style="min-height: 0" id="validateJustificationForm" action="./Presentation/validateJustification.php" method="post">
                    <input type="hidden" name="idJustification" value="<?= $justification->getIdJustification() ?>">
                    <textarea name="rejectionReason" id="JustificationRejectionReason" class="form-control flex-fill" style="max-height: 100%"></textarea>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($justification->getRefusalReason() === null || $justification->getRefusalReason() === ''): ?>
                <p>Aucun motif de refus n'a été communiqué pour cette justification.</p>
            <?php else: ?>
                <div class="border rounded p-2 overflow-y-auto flex-fill">
                    <?= htmlspecialchars($justification->getRefusalReason()) ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Button d'action -->
    <div class="d-flex justify-content-between">
        <a href="index.php" class="btn btn-secondary">Retour</a>
        <?php if ($accountType === AccountType::EducationalManager && $currentState === StateJustif::NotProcessed ): ?>
            <button type="submit" form="validateJustificationForm" id="JustificationRPValidation" class="btn btn-uphf">Envoyer</button>
        <?php endif; ?>
    </div>
</div>

<script>
    const STATE_VALIDATED_JUSTIFICATION = 'Validated';
    const STATE_REFUSED_JUSTIFICATION = 'Refused';

    // Récupération de la textaréa permettant au RP de mettre un commentaire
    const commentInput = document.getElementById('JustificationRejectionReason');

    // Récupération de la div qui contient les CTA des absences
    const allAbsencesForJustification = document.querySelectorAll('.absence-cta-rp');

    // Fonction qui permet le changement d'état d'une absence par le RP
    function changeState(btn_state, btn_lock, input_state, newState) {
        if(newState === STATE_REFUSED_JUSTIFICATION) {
            btn_state.classList.replace('btn-outline-success', 'btn-outline-danger');
            btn_state.classList.remove('rounded');
            btn_state.textContent = (input_state ? 'R' : 'Tout r' ) + 'efusé';

            if (input_state) input_state.value = STATE_REFUSED_JUSTIFICATION;
            btn_lock.classList.replace('d-none', 'd-block');
        }
        else {
            btn_state.classList.replace('btn-outline-danger', 'btn-outline-success');
            btn_state.classList.add('rounded');
            btn_state.textContent = (input_state ? 'V' : 'Tout v' ) + 'alidé';

            if (input_state) input_state.value = STATE_VALIDATED_JUSTIFICATION;
            btn_lock.classList.replace('d-block', 'd-none');
        }
    }
    // Fonction qui permet le changement d'autorisé la re justification d'une absence par le RP
    function changeLock(btn_lock, input_lock, new_lock) {
        console.log(btn_lock.value, new_lock);

        if(new_lock) {
            btn_lock.classList.replace('btn-success', 'btn-danger');
            btn_lock.innerHTML = '<i class="bi bi-lock"></i>';

            if(input_lock) input_lock.value = true;
        }
        else {
            btn_lock.classList.replace('btn-danger', 'btn-success');
            btn_lock.innerHTML = '<i class="bi bi-unlock"></i>';

            if(input_lock) input_lock.value = false;
        }
    }

    // Récupération de la div des CTA "ChangeAll"
    const cta_rp_absence_div = document.getElementById('absence-cta-rp-all');
    const btnChangeAllState = cta_rp_absence_div.firstElementChild;
    const btnChangeAllLock = cta_rp_absence_div.lastElementChild;

    // Gestion du click sur le bouton ChangeAllState
    btnChangeAllState.addEventListener('click', () => {
        const newState = btnChangeAllState.classList.contains('btn-outline-danger') ? STATE_VALIDATED_JUSTIFICATION : STATE_REFUSED_JUSTIFICATION;

        changeState(btnChangeAllState, btnChangeAllLock, null, newState);

        // Changer l'état de chaque absence
        allAbsencesForJustification.forEach(div => {
            const input_state = div.parentNode.querySelector('.absence-input-state');
            const btn_state = div.querySelector('.absence-btn-state');
            const btn_lock = div.querySelector('.absence-btn-lock');

            changeState(btn_state, btn_lock, input_state, newState);
        });
    });

    // Gestion du click sur le bouton ChangeAllLock
    btnChangeAllLock.addEventListener('click', () => {
       const newLock = btnChangeAllLock.classList.contains('btn-success');

       changeLock(btnChangeAllLock, null, newLock);

       // Changer le lock de chaque absence
       allAbsencesForJustification.forEach(div => {
           const btn_lock = div.querySelector('.absence-btn-lock');
           const input_lock = div.parentNode.querySelector('.absence-input-lock');

           changeLock(btn_lock, input_lock, newLock);
       });
    });

    allAbsencesForJustification.forEach(div => {
        const input_state = div.parentNode.querySelector('.absence-input-state');
        const input_lock = div.parentNode.querySelector('.absence-input-lock');
        const btn_state = div.querySelector('.absence-btn-state');
        const btn_lock = div.querySelector('.absence-btn-lock');

        // Gestion des clicks individuel pour l'état d'une absence
        btn_state.addEventListener('click', () => {
            if(input_state.value === STATE_REFUSED_JUSTIFICATION) {
                changeState(btn_state, btn_lock, input_state, STATE_VALIDATED_JUSTIFICATION);
            }
            else {
                changeState(btn_state, btn_lock, input_state, STATE_REFUSED_JUSTIFICATION);
            }
            commentInput.setCustomValidity("");
        });

        // Gestion des clicks individuel pour le lock d'une absence
        btn_lock.addEventListener('click', () => {
            changeLock(btn_lock, input_lock, input_lock.value === 'false');
        });
    });

    const formProcessJustification = document.getElementById('validateJustificationForm');

    if(formProcessJustification) {
        formProcessJustification.addEventListener('submit', event => {
            console.log('UwU')
            event.preventDefault();

            let haveRefusedAbs = false;
            allAbsencesForJustification.forEach(div => {
                const input_state = div.parentNode.querySelector('.absence-input-state');

                if(input_state.value === STATE_REFUSED_JUSTIFICATION) {
                    haveRefusedAbs = true;
                }
            });

            console.log(commentInput.value);

            if(haveRefusedAbs && commentInput.value.trim() === '') {
                commentInput.setCustomValidity("Ce champ est nécessaire en raison du refus d'au moins une absence");
                commentInput.reportValidity();
                return;
            }

            formProcessJustification.submit();
        });
    }


</script>
