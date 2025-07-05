document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".easyup-form").forEach(function (form) {
    const input = form.querySelector('input[type="file"]');
    const preview = form.querySelector(".easyup-preview");
    const button = form.querySelector("button.easyup-submit");
    const status = form.querySelector(".easyup-status");

    const emailInput = form.querySelector('input[name="email"]');
    const fieldId = form.dataset.fieldId;
    const maxFiles = parseInt(form.dataset.maxFiles || "1");

    input.addEventListener("change", function () {
      preview.innerHTML = "";
      const files = Array.from(input.files).slice(0, maxFiles);

      files.forEach((file) => {
        const div = document.createElement("div");
        div.className = "easyup-thumb";

        if (file.type.startsWith("image/")) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            div.appendChild(img);
          };
          reader.readAsDataURL(file);
        } else {
          const span = document.createElement("span");
          span.textContent = file.name;
          div.appendChild(span);
        }

        preview.appendChild(div);
      });
    });

    button.addEventListener("click", function (e) {
      e.preventDefault();

      const formData = new FormData();
      const email = emailInput.value.trim();
      if (!email) {
        status.textContent = "Veuillez renseigner votre adresse email.";
        status.style.color = "red";
        return;
      }

      formData.append("action", "easyup_upload");
      formData.append("field_id", fieldId);
      formData.append("email", email);

      Array.from(input.files)
        .slice(0, maxFiles)
        .forEach((file, index) => {
          formData.append(`files[${index}]`, file);
        });

      status.textContent = "Envoi en cours...";
      status.style.color = "black";

      fetch(easyup_ajax.ajax_url, {
        method: "POST",
        body: formData,
      })
        .then((res) => res.json())
        .then((res) => {
          status.textContent = res.data?.message || "Une erreur est survenue.";
          status.style.color = res.success ? "green" : "red";
          if (res.success) {
            form.reset();
            preview.innerHTML = "";
          }
        })
        .catch((err) => {
          status.textContent = "Erreur réseau.";
          status.style.color = "red";
        });
    });
  });
});

