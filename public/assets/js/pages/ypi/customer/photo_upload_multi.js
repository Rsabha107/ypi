 const input = document.getElementById('photoUpload');
        const preview = document.getElementById('photoPreview');

        let selectedFiles = [];

        input.addEventListener('change', () => {
            const newFiles = Array.from(input.files);

            // ✅ Append new files, avoid duplicates (same name+size+lastModified)
            newFiles.forEach(f => {
                const exists = selectedFiles.some(x =>
                    x.name === f.name &&
                    x.size === f.size &&
                    x.lastModified === f.lastModified
                );
                if (!exists) selectedFiles.push(f);
            });

            syncInputFiles();
            renderPreviews();
        });

        function syncInputFiles() {
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        }

        function renderPreviews() {
            preview.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-item';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-img';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'photo-remove';
                    btn.innerHTML = '&times;';
                    btn.onclick = () => removeFile(index);

                    wrapper.appendChild(img);
                    wrapper.appendChild(btn);
                    preview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            syncInputFiles();
            renderPreviews();
        }