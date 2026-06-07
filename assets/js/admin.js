document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.querySelector('#image_file');
    const preview = document.querySelector('#image_preview');
    const imageUrlInput = document.querySelector('#image_url');

    if (imageInput && preview) {
        imageInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบ?')) {
                e.preventDefault();
            }
        });
    });

    const toggleForms = document.querySelectorAll('[data-toggle-form]');
    toggleForms.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const formId = this.getAttribute('data-toggle-form');
            const form = document.getElementById(formId);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
});
