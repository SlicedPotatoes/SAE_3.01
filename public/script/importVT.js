/**
 * Script utilisé pour l'import VT pour avoir des annimation et un drag & drop fonctionnel avec JavaScript
 */


document.addEventListener('DOMContentLoaded', () =>
{
  const zone = document.querySelector('.upload_dropZone');
  const input = document.getElementById('import_vt');
  const filenameLabel = document.querySelector('.upload_filename');
  const clearBtn = document.querySelector('.upload_clear');
  const submitBtn = document.getElementById('import_submit');
  const downloadBtn = document.querySelector('.upload_download');

  const uploadMessage = document.querySelector('.upload_message');
  const uploadIcon = document.querySelector('.bi-upload');
  const successIcon = document.querySelector('.bi-check-circle');

  let downloadUrl = null;

  // Permet de retirer les comportements par défaut d'un éléments pour le Drag & Drop
  const preventDefaults = (e) =>
  {
    e.preventDefault();
    e.stopPropagation();
  };

  // Retire le comportement par défaut du drag & drop
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName =>
  {
    zone.addEventListener(eventName, preventDefaults, false);
  });

  // remplace la couleur et l'effet de la bordure quand un fichier est au dessus et quand il est dans l'input
  ['dragenter', 'dragover'].forEach(eventName =>
  {
    zone.addEventListener(eventName, () =>
    {
      zone.classList.add('border-uphf');
      zone.classList.remove('border-secondary');
      zone.classList.add('bg-uphf');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName =>
  {
    zone.addEventListener(eventName, () =>
    {
      zone.classList.remove('border-primary');
      zone.classList.add('border-uphf');
      zone.classList.remove('bg-uphf');
    }, false);
  });

  // Met à jour le lien de téléchargement en fonction du fichier
  const updateDownload = (file) =>
  {
    if (downloadUrl)
    {
      URL.revokeObjectURL(downloadUrl);
      downloadUrl = null;
    }

    if (!file)
    {
      downloadBtn.href = '#';
      downloadBtn.classList.add('d-none');
      return;
    }

    downloadUrl = URL.createObjectURL(file);
    downloadBtn.href = downloadUrl;
    downloadBtn.download = file.name;
    downloadBtn.classList.remove('d-none');
  };

  const updateUI = (files) =>
  {
    const hasFile = files && files.length > 0;

    if (!hasFile)
    {
      document.querySelector('label[for="import_vt"]').classList.remove('d-none');
      filenameLabel.textContent = 'Aucun fichier sélectionné';
      clearBtn.classList.add('d-none');
      submitBtn.disabled = true;

      zone.style.borderStyle = 'dashed';
      zone.classList.remove('bg-uphf');

      // Message et icônes en mode "aucun fichier"
      if (uploadMessage)
      {
        uploadMessage.innerHTML =
          'Déposez le fichier au format CSV à l\'intérieur de la zone délimitée par les pointillés.<br><i>ou</i>';
      }
      if (uploadIcon && successIcon)
      {
        uploadIcon.classList.remove('d-none');
        successIcon.classList.add('d-none');
      }

      updateDownload(null);
    }
    else
    {
      document.querySelector('label[for="import_vt"]').classList.add('d-none');
      const file = files[0];
      filenameLabel.textContent = file.name;
      clearBtn.classList.remove('d-none');
      submitBtn.disabled = false;

      zone.style.borderStyle = 'solid';
      zone.classList.add('bg-uphf');

      // Message et icônes en mode "fichier présent"
      if (uploadMessage)
      {
        uploadMessage.innerHTML =
          'Un fichier a été sélectionné.<br>' +
          '<span class="fw-semibold">Vous pouvez l\'importer ou le télécharger.</span>';
      }
      if (uploadIcon && successIcon)
      {
        uploadIcon.classList.add('d-none');
        successIcon.classList.remove('d-none');
      }

      updateDownload(file);
    }
  };

  // Drop : mettre le fichier dans l'input
  // Si un input était déjà mis il est dégager
  zone.addEventListener('drop', (event) =>
  {
    const files = event.dataTransfer.files;
    if (!files || files.length === 0) return;

    const dt = new DataTransfer();
    dt.items.add(files[0]);
    input.files = dt.files;

    updateUI(input.files);
  });

  // Sélection classique
  input.addEventListener('change', (event) =>
  {
    updateUI(event.target.files);
  });

  // Bouton pour vider l'input
  clearBtn.addEventListener('click', () =>
  {
    input.value = '';
    updateUI(null);
  });

  // Init
  updateUI(input.files);
});