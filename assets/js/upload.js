document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".easyup-upload-form").forEach(function (form) {
    const input = form.querySelector('input[type="file"]');
    const previewContainer = form.closest('.easyup-upload-container')?.querySelector('.easyup-preview');
if (!previewContainer) {
  console.warn("Aucune .easyup-preview trouvée pour ce formulaire.");
  return;
}
    const maxSize = parseInt(form.dataset.maxsize || "0");

    input.addEventListener("change", function () {
      const maxFiles = parseInt(input.dataset.max || "0");
      const alreadyPreviewed = previewContainer.querySelectorAll(".easyup-preview-item").length;
      const newFiles = input.files.length;

      if (maxFiles > 0 && (alreadyPreviewed + newFiles) > maxFiles) {
        alert(`Vous ne pouvez sélectionner que ${maxFiles} fichiers.\nDéjà ajoutés : ${alreadyPreviewed}`);
        input.value = "";
        return;
      }

      [...input.files].forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const preview = document.createElement("div");
          preview.className = "easyup-preview-item";
          if (file.type.startsWith("image/")) {
            preview.innerHTML = `<img src="${e.target.result}" alt="${file.name}" />`;
          } else {
            preview.innerHTML = `<p>${file.name}</p>`;
          }
          previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData();

      const fieldId = form.dataset.field || '';
      const emailTo = form.dataset.email || '';

      [...input.files].forEach((file) => {
        if (maxSize > 0 && file.size > maxSize) {
          alert(`Le fichier "${file.name}" dépasse la taille maximale autorisée.`);
          return;
        }
        formData.append("easyup_files[]", file);
      });

      formData.append("action", "easyup_upload_files");
      formData.append("field_id", fieldId);
      formData.append("user_email", emailTo);

      fetch(easyup_ajax.ajaxurl, {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            form.reset();
            previewContainer.innerHTML = "";
            alert("Vos fichiers ont bien été envoyés");
          } else {
            alert("Erreur lors de l'envoi des fichiers.");
          }
        });
    });
  });
});
