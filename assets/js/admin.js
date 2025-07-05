document.addEventListener("DOMContentLoaded", function () {
  const addButton = document.getElementById("easyup-add-field");
  const fieldsContainer = document.getElementById("easyup-fields-container");

  if (addButton) {
    addButton.addEventListener("click", function () {
      const fieldId = Date.now();
      const fieldBlock = document.createElement("div");
      fieldBlock.className = "easyup-field-block";
      fieldBlock.setAttribute("data-id", fieldId);
      fieldBlock.innerHTML = `
        <div class="easyup-field-top">
          <input type="text" placeholder="Nom du champ" class="easyup-field-name" />
          <input type="email" placeholder="Email admin" class="easyup-field-email" />
          <button class="button button-primary easyup-create-shortcode">OK</button>
        </div>
      `;
      
      fieldsContainer.appendChild(fieldBlock);

    });

  }

  document.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("easyup-create-shortcode")) {
      const fieldBlock = e.target.closest(".easyup-field-block");
      const nameInput = fieldBlock.querySelector(".easyup-field-name");
      const emailInput = fieldBlock.querySelector(".easyup-field-email");

      const name = nameInput.value.trim();
      const email = emailInput.value.trim();

      if (name && email) {
        const formData = new FormData();
        formData.append("action", "easyup_create_field");
        formData.append("name", name);
        formData.append("email", email);
        formData.append("nonce", easyup_ajax_obj.nonce);


        fetch(ajaxurl, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              location.reload();
            } else {
              alert("Erreur lors de la création du champ.");
            }
          });
      } else {
        alert("Veuillez remplir tous les champs.");
      }
    }
  });

  document.addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("easyup-copy-shortcode")) {
      const shortcodeInput = e.target.closest(".easyup-shortcode-line").querySelector(".easyup-shortcode");
      shortcodeInput.select();
      document.execCommand("copy");
      e.target.textContent = "Copié !";
      setTimeout(() => {
        e.target.textContent = "Copier";
      }, 1500);
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
    // Bouton SUPPRIMER
    document.querySelectorAll('.easyup-delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const fieldId = this.dataset.id;
            if (!fieldId) return;

            if (!confirm(`Supprimer le champ "${fieldId}" ? Cette action est irréversible.`)) return;

            const data = new FormData();
            data.append('action', 'easyup_delete_field');
            data.append('field_id', fieldId);
            data.append('nonce', easyup_ajax_obj.nonce);

            fetch(easyup_ajax_obj.ajax_url, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const bloc = document.querySelector(`#easyup-field-${fieldId}`);
                    if (bloc) bloc.remove();
                } else {
                    alert('Erreur : impossible de supprimer le champ.');
                }
            })
            .catch(() => alert('Erreur lors de la suppression'));
        });
    });
});
// Bouton ENREGISTRER

function saveFieldEdit(fieldId) {
    const maxFiles = document.getElementById('edit-max-files-' + fieldId).value;
    const maxSize = document.getElementById('edit-max-size-' + fieldId).value;
    const acceptPdf = document.getElementById('edit-accept-pdf-' + fieldId).value;
    const email = document.getElementById('edit-email-' + fieldId).value;

    const data = new FormData();
    data.append('action', 'easyup_update_field');
    data.append('field_id', fieldId);
    data.append('max_files', maxFiles);
    data.append('max_size', maxSize);
    data.append('accept_pdf', acceptPdf);
    data.append('email_to', email);
    data.append('nonce', easyup_ajax_obj.nonce);

    fetch(easyup_ajax_obj.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const bloc = document.getElementById('easyup-field-' + fieldId);
            if (bloc) {
                const infos = bloc.querySelector('p');
                infos.innerHTML =
                    'fichiers max : ' + maxFiles +
                    ' — taille : ' + maxSize + ' Ko' +
                    ' — PDF : ' + (acceptPdf === '1' ? 'oui' : 'non') +
                    ' — e-mail : ' + email;

                const form = document.getElementById('edit-form-' + fieldId);
                if (form) {
                    form.classList.remove('open');
                    form.style.maxHeight = '0px';
                    const editBtn = document.getElementById('edit-button-' + fieldId);
                    if (editBtn) editBtn.disabled = false;
                }

                const editButton = bloc.querySelector('.button.edit-button');
                if (editButton) {
                    editButton.disabled = false;
                }
            }
        } else {
            alert('Erreur : ' + result.data);
        }
    })
    .catch(() => alert('Erreur lors de l’enregistrement'));
}

// Initialiser le clic sur le bouton "Enregistrer"
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.easyup-save-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault(); // ⛔ Évite le rechargement
            const fieldId = this.dataset.id;
            if (!fieldId) return;
            saveFieldEdit(fieldId);
        });
    });
});

//ouverture et fermeture du bloc caché
function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    const button = document.getElementById('edit-button-' + id);
    if (!form || !button) return;

    const isOpen = form.classList.contains('open');

    // Ferme tous les autres blocs d’édition
    document.querySelectorAll('.easyup-edit-form').forEach(function (el) {
        el.classList.remove('open');
        el.style.maxHeight = '0px';
    });
        // Ouvre celui cliqué
    if (!isOpen) {
        form.classList.add('open');
        form.style.maxHeight = form.scrollHeight + 'px';
    }
        document.querySelectorAll('.edit-button').forEach(function (btn) {
        btn.disabled = false;
    });

    if (!isOpen) {
        form.classList.add('open');
        form.style.maxHeight = form.scrollHeight + 'px';
        button.disabled = true;
    }
}

