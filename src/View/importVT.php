<?php
/**
 * Page permettant au secrétaire de faire l'import VT
 */

global $dataView;

require_once __DIR__."/Composants/header.php";
?>

<div class="card p-3 flex-fill d-flex flex-column" style="min-height: 0">

  <form method="post" enctype="multipart/form-data"
        class="d-flex flex-column flex-grow-1" style="min-height: 0;">
    <div class="card border-2 border-secondary upload_dropZone flex-grow-1 mb-3 d-flex"
         style="border-style: dashed; min-height: 0;">

      <div class="text-center p-4 m-auto d-flex flex-column justify-content-center">

      <!-- Icône upload -> quand il n'y a pas de fichier dans l'input -->
        <i class="bi bi-box-arrow-in-down d-none bi-upload" style="font-size: 4rem"></i>

        <!-- Icône succès -> quand il y a un fichier dans l'input -->
        <i class="bi bi-check-circle text-success d-none" style="font-size: 4rem"></i>

        <p class="small my-2 upload_message">
          Déposez le fichier au format CSV à l'intérieur de la zone délimitée par les pointillés.<br>
          <i>ou</i>
        </p>
        <input id="import_vt"
               name="vt_file"
               class="position-absolute invisible"
               type="file"
               accept=".csv,text/csv"/>
        <label class="btn btn-uphf m-5 mb-3 mt-1 p-2" for="import_vt">
          Choisir un fichier
        </label>

        <div class="upload_filename small text-muted mb-2">
          Aucun fichier sélectionné
        </div>

        <div class="btn-group btn-group-sm">

          <a href="#" class="btn btn-outline-primary bi bi-download me-1 upload_download d-none" download ></a>
          <button type="button" class="btn btn-outline-danger bi bi-trash upload_clear d-none"></button>
        </div>

      </div>
    </div>

    <input id="import_submit"
           class="btn btn-primary btn-uphf mt-3 align-self-end"
           type="submit"
           value="Importer dans la base de donnée">
  </form>
</div>

<script src="/script/importVT.js"></script>


