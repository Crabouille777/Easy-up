// admin/js/easyup-admin.js

document.addEventListener("DOMContentLoaded", function () {
    const addButton = document.getElementById("easyup-add-field");

    if (addButton) {
        addButton.addEventListener("click", function () {
            const container = document.getElementById("easyup-fields");
            const fieldId = Date.now();
            const fieldHTML = `
                <div class="easyup-field-card" data-id="${fieldId}">
                    <div class="easyup-config">
                        <label>Nom du champ :</label>
                        <input type="text" name="easyup_name" placeholder="Ex: Photo identité" />
                        <label>Email de réception :</label>
                        <input type="email" name="easyup_email" placeholder="email@exemple.com" />
                        <label>Nombre max de fichiers :</label>
                        <div class="easyup-spinner">
                            <button class="easyup-down">-</button>
                            <input type="number" name="easyup_max_files" min="0" value="1" />
                            <button class="easyup-up">+</button>
                        </div>
                        <label>Taille max par fichier (Ko) :</label>
                        <input type="number" name="easyup_max_size" placeholder="Ex: 2048" />
                        <button class="easyup-create-shortcode">OK</button>
                    </div>
                </div>`;
            container.insertAdjacentHTML("beforeend", fieldHTML);
        });
    }

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("easyup-up")) {
            const input = e.target.previousElementSibling;
            input.stepUp();
        }
        if (e.target.classList.contains("easyup-down")) {
            const input = e.target.nextElementSibling;
            input.stepDown();
        }

        if (e.target.classList.contains("easyup-create-shortcode")) {
            const wrapper = e.target.closest(".easyup-field-card");
            const name = wrapper.querySelector('input[name="easyup_name"]').value.trim();
            const email = wrapper.querySelector('input[name="easyup_email"]').value.trim();
            const maxFiles = wrapper.querySelector('input[name="easyup_max_files"]').value;
            const maxSize = wrapper.querySelector('input[name="easyup_max_size"]').value;

            if (!name || !email) {
                alert("Veuillez renseigner un nom et un email.");
                return;
            }

            const formData = new FormData();
            formData.append("action", "easyup_create_field");
            formData.append("name", name);
            formData.append("email", email);
            formData.append("max_files", maxFiles);
            formData.append("max_size", maxSize);

            fetch(ajaxurl, {
                method: "POST",
                body: formData,
            })
                .then((r) => r.json())
                .then((response) => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert("Erreur : " + response.data);
                    }
                });
        }

        if (e.target.classList.contains("easyup-delete")) {
            const id = e.target.dataset.id;
            if (!confirm("Supprimer ce champ ?")) return;

            const formData = new FormData();
            formData.append("action", "easyup_delete_field");
            formData.append("id", id);

            fetch(ajaxurl, {
                method: "POST",
                body: formData,
            })
                .then((r) => r.json())
                .then((response) => {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert("Erreur : " + response.data);
                    }
                });
        }

        if (e.target.classList.contains("easyup-toggle-settings")) {
            const box = e.target.closest(".easyup-shortcode-box");
            box.classList.toggle("open");
        }

        if (e.target.classList.contains("easyup-copy")) {
            const input = e.target.previousElementSibling;
            navigator.clipboard.writeText(input.value);
            e.target.textContent = "Copié !";
            setTimeout(() => (e.target.textContent = "Copier"), 2000);
        }
    });
});

