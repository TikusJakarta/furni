document.getElementById('image').addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file) { return; }
            document.getElementById('imgPreviewEl').src = URL.createObjectURL(file);
            document.getElementById('imgPreview').classList.add('has-image');
            document.getElementById('imgCaption').textContent = 'Pratinjau gambar baru (belum disimpan).';
        });