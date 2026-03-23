<?php

/**
 * Modal pour la preview d'un fichier
 */
?>

<div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-xl-down modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu du fichier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="filePreview" src="" alt="Aperçu" class="img-fluid d-none">
                <iframe id="filePdf" src="" class="w-100 d-none"></iframe>
                <p id="fileOther" class="text-muted d-none">Aperçu non disponible</p>
            </div>
        </div>
    </div>
</div>
