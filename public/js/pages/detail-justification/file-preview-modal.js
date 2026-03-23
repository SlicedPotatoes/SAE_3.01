/**
 * Script gérant la modal de preview d'un fichier, permet de charger le fichier dans celle-ci
 */
const modal = document.getElementById("fileModal");
const img = document.getElementById("filePreview");
const pdf = document.getElementById("filePdf");
const other = document.getElementById("fileOther");

modal.addEventListener("show.bs.modal", (event) => {
    const button = event.relatedTarget;
    const fileName = button.getAttribute("data-bs-file") || "";
    const url = button.getAttribute("data-bs-url") || "";

    img.classList.add("d-none");
    pdf.classList.add("d-none");
    other.classList.add("d-none");

    if (fileName.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
        img.src = url;
        img.classList.remove("d-none");
    } else if (fileName.match(/\.pdf$/i)) {
        pdf.src = url;
        pdf.classList.remove("d-none");
    } else {
        other.classList.remove("d-none");
    }
});

modal.addEventListener("hidden.bs.modal", () => {
    img.src = "";
    pdf.src = "";
});