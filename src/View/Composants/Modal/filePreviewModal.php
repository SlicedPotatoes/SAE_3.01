<?php

/**
 * Fichier php de la modal utilisé pour le visualisation de fichier
 */
?>

<div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Aperçu du fichier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <img id="filePreview" src="" alt="Aperçu" class="img-fluid d-none">
        <iframe id="filePdf" src="" class="w-100 d-none" style="height:70vh; border:none;"></iframe>
        <p id="fileOther" class="text-muted d-none">Aperçu non disponible</p>
      </div>
    </div>
  </div>
</div>

<script>

  const modal = document.getElementById("fileModal");
  const img = document.getElementById("filePreview");
  const pdf = document.getElementById("filePdf");
  const other = document.getElementById("fileOther");

  modal.addEventListener("show.bs.modal", (event) => {
    const button = event.relatedTarget;
    const fileUrl = button.getAttribute("data-bs-file") || "";

    img.classList.add("d-none");
    pdf.classList.add("d-none");
    other.classList.add("d-none");

    if (fileUrl.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
      img.src = fileUrl;
      img.classList.remove("d-none");
    } else if (fileUrl.match(/\.pdf$/i)) {
      pdf.src = fileUrl;
      pdf.classList.remove("d-none");
    } else {
      other.classList.remove("d-none");
    }
  });

  modal.addEventListener("hidden.bs.modal", () => {
    img.src = "";
    pdf.src = "";
  });
</script>
