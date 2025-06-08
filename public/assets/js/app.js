
document.addEventListener('DOMContentLoaded', function() {
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