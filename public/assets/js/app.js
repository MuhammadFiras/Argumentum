// public/assets/js/app.js

document.addEventListener('DOMContentLoaded', function() {

    // SCRIPT BUAT VALIDASI FORM LOGIN (dari login.php)
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            let isValid = true;
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            // Hapus pesan error lama
            if(emailError) {
                emailError.style.display = 'none';
                emailError.textContent = '';
            }
            if(passwordError){
                passwordError.style.display = 'none';
                passwordError.textContent = '';
            }
            email.classList.remove('is-invalid');
            password.classList.remove('is-invalid');

            // Validasi Email
            if (email.value.trim() === '') {
                if(emailError){
                    emailError.textContent = 'Email tidak boleh kosong.';
                    emailError.style.display = 'block';
                }
                email.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Password
            if (password.value.trim() === '') {
                if(passwordError){
                    passwordError.textContent = 'Kata sandi tidak boleh kosong.';
                    passwordError.style.display = 'block';
                }
                password.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault(); // Mencegah form submit jika tidak valid
            }
        });
    }


    // SCRIPT UNTUK VALIDASI FORM REGISTER (dari register.php) 
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {

        registerForm.addEventListener('submit', function(event) {
            let isValid = true;
            const namaLengkap = document.getElementById('nama_lengkap');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');

            // Reset semua field
            [namaLengkap, email, password, confirmPassword].forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            // Validasi Nama Lengkap
            if (namaLengkap.value.trim() === '') {
                namaLengkap.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value.trim() === '' || !emailPattern.test(email.value)) {
                email.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Password
            if (password.value.length < 8) {
                password.classList.add('is-invalid');
                isValid = false;
            }

            // Validasi Konfirmasi Password
            if (password.value !== confirmPassword.value || confirmPassword.value === '') {
                confirmPassword.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
                // Opsional: tampilkan alert umum
                // alert('Harap periksa kembali isian form Anda. Pastikan semua terisi dengan benar.');
            }
        });
    }

    //== SCRIPT UNTUK RATING BINTANG (dari view_question.php) ==
    // Mengambil token CSRF dari meta tag yang dibuat oleh `csrf_meta()` di layout
    const csrfTokenName = document.querySelector('meta[name="X-CSRF-TOKEN-NAME"]')?.getAttribute('content');
    let csrfTokenValue = document.querySelector('meta[name="X-CSRF-TOKEN-HASH"]')?.getAttribute('content');
    
    document.querySelectorAll('.star-rating .star').forEach(star => {
        star.addEventListener('click', function() {
            const ratingValue = this.dataset.value;
            const parent = this.closest('.star-rating');
            const answerId = parent.dataset.answerId;
            const feedbackDiv = document.getElementById(`rating-feedback-message-${answerId}`);
            const siteUrl = window.location.origin; 

            // langsung ubah bintang & beri pesan "menyimpan"
            parent.querySelectorAll('.star').forEach(s => {
                s.classList.remove('rated');
                if (parseInt(s.dataset.value) <= ratingValue) {
                    s.classList.add('rated');
                }
            });
            feedbackDiv.textContent = 'Menyimpan rating...';
            feedbackDiv.className = 'rating-feedback-message text-info';

            const formData = new URLSearchParams();
            formData.append('rating', ratingValue);
            formData.append(csrfTokenName, csrfTokenValue);

            fetch(`${siteUrl}/answer/rate/${answerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData.toString()
            })
            .then(response => {
                // Perbarui token CSRF jika server mengirim yang baru
                const newCsrfToken = response.headers.get('X-CSRF-TOKEN');
                if (newCsrfToken) {
                    csrfTokenValue = newCsrfToken;
                    document.querySelector('meta[name="X-CSRF-TOKEN-HASH"]').setAttribute('content', newCsrfToken);
                }
                if (!response.ok) {
                    return response.json().then(errData => Promise.reject(errData));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    feedbackDiv.textContent = data.message;
                    feedbackDiv.className = 'rating-feedback-message text-success';
                    document.getElementById(`avg-rating-${answerId}`).textContent = data.average_rating;
                    document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;
                } else {
                    feedbackDiv.textContent = data.message || 'Gagal memberi rating.';
                    feedbackDiv.className = 'rating-feedback-message text-danger';
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                let errorMessage = 'Terjadi kesalahan.';
                if (error && error.message) { errorMessage = error.message; }
                feedbackDiv.textContent = errorMessage;
                feedbackDiv.className = 'rating-feedback-message text-danger';
            });
        });
    });

    
    // SCRIPT UNTUK PREVIEW FOTO PROFIL (dari edit_profile.php) 
    const photoProfileInput = document.getElementById('photo_profile');
    if (photoProfileInput) {
        photoProfileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const profilePicture = document.querySelector('.profile-picture');
                    if(profilePicture) {
                        profilePicture.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

}); 