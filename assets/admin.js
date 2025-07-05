document.addEventListener('DOMContentLoaded', () => {
    const addFieldBtn = document.getElementById('easyup-add-field');
    const fieldContainer = document.getElementById('easyup-fields-container');
    const fieldTemplate = document.getElementById('easyup-field-template');
    const shortcodeTemplate = document.getElementById('easyup-shortcode-template');

    // Ajouter un champ
    addFieldBtn.addEventListener('click', () => {
        const clone = document.importNode(fieldTemplate.content, true);
        const block = clone.querySelector('.easyup-field-block');

        block.querySelector('.easyup-delete-field').addEventListener('click', () => {
            block.remove();
        });

        block.querySelector('.easyup-minus-file').addEventListener('click', () => {
            const input = block.querySelector('.easyup-max-files');
            input.value = Math.max(0, parseInt(input.value) - 1);
        });

        block.querySelector('.easyup-plus-file').addEventListener('click', () => {
            const input = block.querySelector('.easyup-max-files');
            input.value = parseInt(input.value) + 1;
        });

        block.querySelector('.easyup-generate-shortcode').addEventListener('click', () => {
            const name = block.querySelector('.easyup-field-name').value.trim();
            const email = block.querySelector('.easyup-field-email').value.trim();
            const files = block.querySelector('.easyup-max-files').value.trim() || "1";
            const size = block.querySelector('.easyup-max-size').value.trim() || "";

            if (!name) return alert("Veuillez entrer un nom de champ.");

            const shortcode = `[easyup name="${name}" email="${email}" files="${files}" size="${size}"]`;

            const shortBlock = document.importNode(shortcodeTemplate.content, true);
            shortBlock.querySelector('.easyup-shortcode-name').textContent = name;
            shortBlock.querySelector('.easyup-shortcode-code').textContent = shortcode;

            const params = shortBlock.querySelector('.easyup-shortcode-params');
            const toggleBtn = shortBlock.querySelector('.easyup-toggle-params');

            toggleBtn.addEventListener('click', () => {
                params.style.display = (params.style.display === 'none') ? 'block' : 'none';
                toggleBtn.classList.toggle('dashicons-arrow-down');
                toggleBtn.classList.toggle('dashicons-arrow-up');
            });

            shortBlock.querySelector('.easyup-copy-shortcode').addEventListener('click', () => {
                navigator.clipboard.writeText(shortcode);
                alert("Shortcode copié !");
            });

            shortBlock.querySelector('.easyup-param-name').value = name;
            shortBlock.querySelector('.easyup-param-email').value = email;
            shortBlock.querySelector('.easyup-param-files').value = files;
            shortBlock.querySelector('.easyup-param-size').value = size;

            fieldContainer.appendChild(shortBlock);
            block.remove();
        });

        fieldContainer.appendChild(block);
    });
});

