document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.easyup-upload-form').forEach(function (form) {
    const input = form.querySelector('input[type="file"]');
    const container = form.closest('.easyup-upload-container');
    const preview = container.querySelector('.easyup-preview');
    if (!preview) {
      console.warn("Aucune .easyup-preview trouvée dans ce container.");
      return;
    }
    const emailInput = form.querySelector('input[type="email"]');
    const sendButton = form.querySelector('.easyup-submit-btn, .easyup-send-btn');
    const message = container.querySelector('.easyup-message');
    const loader = form.querySelector('.easyup-loader');

    if (!input || !preview || !sendButton || !message) return;

    // Tableau qui stocke les fichiers sélectionnés
    let selectedFiles = [];

    // Fonction pour mettre à jour la preview des fichiers avec bouton "Supprimer"
function updatePreview() {
  preview.innerHTML = '';
  selectedFiles.forEach((file, index) => {
    const reader = new FileReader();
    reader.onload = function (e) {
      let html = `<div class="easyup-preview-item" data-index="${index}" style="display: flex; flex-direction: column; align-items: center;">`;
      
      if (file.type.startsWith('image/')) {
        html += `<img src="${e.target.result}" alt="${file.name}" />`;
      } else if (file.type === 'application/pdf') {
        html += `<p class="pdf-icon">PDF<br>${file.name}</p>`;
      } else {
        html += `<p>${file.name}</p>`;
      }

      // Nouveau conteneur pour le bouton "Supprimer"
      html += `<div class="easyup-delete-wrapper" style="margin-top: 5px; text-align: center;">
                  <input type="button" class="easyup-delete-file easyup-delete-button" data-index="${index}" value="Supprimer">
               </div>`;

      html += `</div>`;
      preview.innerHTML += html;
    };
    reader.readAsDataURL(file);
  });
}


    // Lorsqu'un fichier est sélectionné, on recharge le tableau et la preview
input.addEventListener('change', function () {
  const maxFiles = parseInt(input.getAttribute('data-max'), 10); // Récupère la limite depuis l’attribut data-max
  const newFiles = Array.from(input.files);

  if (selectedFiles.length + newFiles.length > maxFiles) {
    alert(`Vous ne pouvez uploader que ${maxFiles} fichier(s).`);
    return;
  }

  selectedFiles = selectedFiles.concat(newFiles);
  updatePreview();
  input.value = ''; // Permet de re-sélectionner
});



    // Gestion de la suppression d'un fichier dans la preview
    preview.addEventListener('click', function (e) {
      if (e.target.classList.contains('easyup-delete-file')) {
        const index = parseInt(e.target.getAttribute('data-index'), 10);
        // Retirer le fichier sélectionné du tableau
        selectedFiles.splice(index, 1);
        updatePreview();

        // Re-créer une FileList à partir du tableau mis à jour
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
      }
    });

    // Envoi du formulaire en utilisant le tableau selectedFiles
    sendButton.addEventListener('click', function (e) {
      e.preventDefault();
      message.style.color = 'green';

      if (!selectedFiles.length) {
        message.textContent = "Aucun fichier sélectionné.";
        message.style.color = 'red';
        return;
      }

      const data = new FormData();
      selectedFiles.forEach(file => {
        data.append('easyup_files[]', file);
      });

      data.append('action', 'easyup_upload_files');
      data.append('field_id', form.dataset.field);
      data.append('nonce', easyup_frontend.nonce);

      if (loader) loader.style.display = 'inline-block';
      message.textContent = '';

      fetch(easyup_frontend.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
      })
        .then(response => response.json())
        .then(result => {
          if (loader) loader.style.display = 'none';

          if (result.success) {
            message.textContent = "Vos fichiers ont bien été envoyés.";
            message.style.color = 'green';
            preview.innerHTML = '';
            input.value = '';
            selectedFiles = [];
            if (emailInput) emailInput.value = '';
          } else {
            const error = result.data;
            if (typeof error === 'string') {
              message.textContent = "Erreur : " + error;
              message.style.color = 'red';
            } else if (error.message) {
              message.textContent = "Erreur : " + error.message;
              message.style.color = 'red';
              if (error.details && Array.isArray(error.details)) {
                message.textContent += "\n" + error.details.join("\n");
              }
            } else {
              message.textContent = "Erreur inconnue.";
              message.style.color = 'red';
            }
          }
        })
        .catch(() => {
          if (loader) loader.style.display = 'none';
          message.textContent = "Erreur lors de l'envoi.";
          message.style.color = 'red';
        });
    });
  });
});
