const input = document.getElementById('photoUpload');
const preview = document.getElementById('photoPreview');

let selectedFile = null;

input.addEventListener('change', () => {
    if (!input.files.length) return;

    selectedFile = input.files[0]; // ✅ only one file
    syncInputFiles();
    renderPreview();
});

function syncInputFiles() {
    const dt = new DataTransfer();
    if (selectedFile) dt.items.add(selectedFile);
    input.files = dt.files;
}

function renderPreview() {
    preview.innerHTML = '';
    if (!selectedFile) return;

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
        btn.onclick = clearFile;

        wrapper.appendChild(img);
        wrapper.appendChild(btn);
        preview.appendChild(wrapper);
    };
    reader.readAsDataURL(selectedFile);
}

function clearFile() {
    selectedFile = null;
    input.value = ''; // important
    preview.innerHTML = '';
}