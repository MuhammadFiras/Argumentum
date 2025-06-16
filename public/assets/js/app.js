document.addEventListener("DOMContentLoaded", function () {
  //== SCRIPT UNTUK RATING BINTANG ==
  // Ambil nama dan hash CSRF dari meta tag yang benar
  const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute("content");
  let csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

  // Fungsi untuk memperbarui token CSRF setelah setiap request AJAX
  const updateCsrfToken = (newHash) => {
    if (newHash) {
      csrfHash = newHash;
      document.querySelector('meta[name="csrf-token"]').setAttribute("content", newHash);
    }
  };

  document.querySelectorAll(".star-rating .star").forEach((star) => {
    star.addEventListener("click", function () {
      const parent = this.closest(".star-rating");
      const ratingValue = this.dataset.value;
      const answerId = parent.dataset.answerId;
      const originalRating = parent.dataset.currentRating;

      // ===== FIX: Aktifkan kembali baris ini =====
      const feedbackDiv = document.getElementById(`rating-feedback-message-${answerId}`);

      // Langsung perbarui tampilan bintang (Optimistic Update)
      parent.querySelectorAll(".star").forEach((s) => {
        s.classList.remove("rated");
        if (parseInt(s.dataset.value) <= ratingValue) {
          s.classList.add("rated");
        }
      });

      // Tampilkan pesan sementara
      if (feedbackDiv) {
        feedbackDiv.textContent = "Menyimpan rating...";
        feedbackDiv.className = "rating-feedback-message text-info";
      }

      const formData = new URLSearchParams();
      formData.append("rating", ratingValue);
      formData.append(csrfName, csrfHash);

      fetch(`${siteUrl}/answer/rate/${answerId}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData.toString(),
      })
        .then((response) => {
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));
          if (!response.ok) {
            return response.json().then((errData) => Promise.reject(errData));
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            if (feedbackDiv) {
              feedbackDiv.textContent = data.message;
              feedbackDiv.className = "rating-feedback-message text-success";
            }

            // === SEKARANG KODE INI AKAN BERJALAN ===
            // Penyempurnaan: Format angka agar selalu ada satu desimal (misal: 4.0)
            document.getElementById(`avg-rating-${answerId}`).textContent = parseFloat(data.average_rating).toFixed(1);
            document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;

            // Update rating saat ini untuk klik berikutnya
            parent.dataset.currentRating = ratingValue;

            // ===== FIX ADA DI SINI =====
            // Setelah berhasil memberi rating, cari tombol 'Hapus Rating' dan tampilkan.
            const deleteButton = parent.closest(".star-rating-container").querySelector(".btn-delete-rating");
            if (deleteButton) {
              deleteButton.style.display = "inline";
            }
          } else {
            throw new Error(data.message || "Gagal memberi rating.");
          }
        })
        .catch((error) => {
          console.error("Error details:", error);
          if (feedbackDiv) {
            feedbackDiv.textContent = error.message || "Terjadi kesalahan.";
            feedbackDiv.className = "rating-feedback-message text-danger";
          } else {
            alert(error.message || "Terjadi kesalahan.");
          }

          // Kembalikan bintang ke rating semula jika gagal
          parent.querySelectorAll(".star").forEach((s) => {
            s.classList.remove("rated");
            if (parseInt(s.dataset.value) <= originalRating) {
              s.classList.add("rated");
            }
          });
        });
    });
  });

  // SCRIPT UNTUK PREVIEW FOTO PROFIL (dari edit_profile.php)
  const photoProfileInput = document.getElementById("photo_profile");
  if (photoProfileInput) {
    photoProfileInput.addEventListener("change", function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const profilePicture = document.querySelector(".profile-picture");
          if (profilePicture) {
            profilePicture.src = e.target.result;
          }
        };
        reader.readAsDataURL(file);
      }
    });
  }

  //=======================================================
  //== SCRIPT UNTUK SUBMIT KOMENTAR (AJAX) - DIPERBAIKI
  //=======================================================
  document.querySelectorAll(".comment-form").forEach((form) => {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const answerId = this.dataset.answerId;
      const commentList = document.getElementById(`comment-list-${answerId}`);
      const commentTextarea = this.querySelector('textarea[name="comment_text"]');
      const commentText = commentTextarea.value.trim();
      const submitButton = this.querySelector('button[type="submit"]');

      if (commentText === "") {
        return;
      }

      const formData = new URLSearchParams();
      formData.append("comment_text", commentText);
      // Gunakan variabel CSRF yang sudah kita definisikan di atas dan selalu up-to-date
      formData.append(csrfName, csrfHash);

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

      // Hapus deklarasi const siteUrl dari sini, karena sudah ada secara global

      // Gunakan variabel `siteUrl` global dari main_layout.php
      fetch(`${siteUrl}/comment/create/${answerId}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData.toString(),
      })
        .then((response) => {
          // Selalu perbarui token CSRF setelah request berhasil
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));

          if (!response.ok) {
            return response.json().then((errData) => Promise.reject(errData));
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            // (Disarankan menggunakan metode aman yang dijelaskan di atas)
            const newComment = data.comment;
            const commentEl = document.createElement("div");
            commentEl.className = "comment-item d-flex align-items-start mb-2";
            commentEl.innerHTML = `
              <img src="${siteUrl}/assets/images/profiles/${newComment.photo_profile}" alt="${newComment.nama_lengkap}" class="rounded-circle me-2" width="24" height="24">
              <div class="comment-content">
                  <strong>${newComment.nama_lengkap}</strong>
                  <p class="mb-0">${newComment.comment_text}</p>
                  <small class="text-muted">${newComment.created_at}</small>
              </div>
            `;
            commentList.appendChild(commentEl);
            commentTextarea.value = "";
            const commentCountEl = document.getElementById(`comment-count-${answerId}`);
            commentCountEl.textContent = parseInt(commentCountEl.textContent) + 1;
          } else {
            alert("Gagal menambahkan komentar: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          // Cek message dari server jika ada, agar lebih informatif
          const errorMessage = error.message || "Terjadi kesalahan. Silakan buka Developer Tools (F12) di tab Network untuk detail.";
          alert(errorMessage);
        })
        .finally(() => {
          submitButton.disabled = false;
          submitButton.textContent = "Kirim";
        });
    });
  });

  //=======================================================
  //== SCRIPT UNTUK EDIT & HAPUS KOMENTAR (AJAX)
  //=======================================================

  // Gunakan event delegation untuk efisiensi
  document.body.addEventListener("click", function (event) {
    // --- LOGIKA HAPUS KOMENTAR ---
    if (event.target.classList.contains("btn-delete-comment")) {
      event.preventDefault();

      if (!confirm("Apakah Anda yakin ingin menghapus komentar ini?")) {
        return;
      }

      const commentId = event.target.dataset.commentId;
      const commentItemEl = document.getElementById(`comment-item-${commentId}`);

      const formData = new URLSearchParams();
      formData.append(csrfName, csrfHash); // csrfName & csrfHash dari scope global

      fetch(`${siteUrl}/comment/delete/${commentId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
      })
        .then((response) => {
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));
          if (!response.ok) return response.json().then((err) => Promise.reject(err));
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            // Animasi fade out lalu hapus
            commentItemEl.style.transition = "opacity 0.5s ease";
            commentItemEl.style.opacity = "0";
            setTimeout(() => {
              commentItemEl.remove();
              // (Opsional) Update jumlah komentar
            }, 500);
          } else {
            alert("Gagal menghapus: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert(error.message || "Terjadi kesalahan saat menghapus komentar.");
        });
    }

    // --- LOGIKA TOMBOL EDIT DIKLIK ---
    if (event.target.classList.contains("btn-edit-comment")) {
      event.preventDefault();
      const commentId = event.target.dataset.commentId;
      const textDisplayEl = document.getElementById(`comment-text-display-${commentId}`);
      const editFormArea = document.getElementById(`comment-edit-form-area-${commentId}`);
      const originalText = textDisplayEl.querySelector("p").textContent;

      // Sembunyikan teks asli
      textDisplayEl.style.display = "none";

      // Tampilkan form edit
      editFormArea.style.display = "block";
      editFormArea.innerHTML = `
                <form class="form-edit-comment" data-comment-id="${commentId}">
                    <textarea class="form-control form-control-sm mb-2" name="comment_text" rows="2" required>${originalText}</textarea>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-edit">Batal</button>
                </form>
            `;
    }

    // --- LOGIKA TOMBOL BATAL EDIT DIKLIK ---
    if (event.target.classList.contains("btn-cancel-edit")) {
      event.preventDefault();
      const form = event.target.closest(".form-edit-comment");
      const commentId = form.dataset.commentId;

      // Kembalikan seperti semula
      document.getElementById(`comment-text-display-${commentId}`).style.display = "block";
      document.getElementById(`comment-edit-form-area-${commentId}`).style.display = "none";
      document.getElementById(`comment-edit-form-area-${commentId}`).innerHTML = "";
    }

    // --- LOGIKA HAPUS RATING ---
    if (event.target.classList.contains("btn-delete-rating")) {
      event.preventDefault();

      if (!confirm("Anda yakin ingin menghapus rating Anda untuk jawaban ini?")) {
        return;
      }

      const deleteButton = event.target;
      const answerId = deleteButton.dataset.answerId;
      const starRatingDiv = document.querySelector(`.star-rating[data-answer-id="${answerId}"]`);

      const formData = new URLSearchParams();
      formData.append(csrfName, csrfHash);

      fetch(`${siteUrl}/answer/delete-rating/${answerId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
      })
        .then((response) => {
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));
          if (!response.ok) return response.json().then((err) => Promise.reject(err));
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            // 1. Update teks rata-rata dan jumlah suara
            document.getElementById(`avg-rating-${answerId}`).textContent = data.average_rating;
            document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;

            // 2. Kosongkan semua bintang
            starRatingDiv.querySelectorAll(".star").forEach((s) => s.classList.remove("rated"));

            // 3. Update data-current-rating menjadi 0
            starRatingDiv.dataset.currentRating = "0";

            // 4. Sembunyikan tombol "Hapus Rating"
            deleteButton.style.display = "none";

            // 5. Tampilkan pesan sukses
            const feedbackDiv = document.getElementById(`rating-feedback-message-${answerId}`);
            if (feedbackDiv) {
              feedbackDiv.textContent = data.message;
              feedbackDiv.className = "rating-feedback-message text-success";
            }
          } else {
            alert("Gagal: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert(error.message || "Terjadi kesalahan saat menghapus rating.");
        });
    }
  });

  // --- LOGIKA SUBMIT FORM EDIT ---
  // Karena form dibuat dinamis, kita juga pakai event delegation
  document.body.addEventListener("submit", function (event) {
    if (event.target.classList.contains("form-edit-comment")) {
      event.preventDefault();

      const form = event.target;
      const commentId = form.dataset.commentId;
      const newText = form.querySelector("textarea").value.trim();
      const submitButton = form.querySelector('button[type="submit"]');

      if (newText === "") return;

      submitButton.disabled = true;
      submitButton.textContent = "Menyimpan...";

      const formData = new URLSearchParams();
      formData.append("comment_text", newText);
      formData.append(csrfName, csrfHash);

      fetch(`${siteUrl}/comment/update/${commentId}`, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: formData,
      })
        .then((response) => {
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));
          if (!response.ok) return response.json().then((err) => Promise.reject(err));
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            const textDisplayEl = document.getElementById(`comment-text-display-${commentId}`);
            // Update teks di view
            textDisplayEl.querySelector("p").textContent = data.updated_text;

            // Kembalikan ke mode tampilan (bukan edit)
            textDisplayEl.style.display = "block";
            document.getElementById(`comment-edit-form-area-${commentId}`).style.display = "none";
            document.getElementById(`comment-edit-form-area-${commentId}`).innerHTML = "";
          } else {
            alert("Gagal update: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert(error.message || "Terjadi kesalahan saat mengupdate komentar.");
        })
        .finally(() => {
          submitButton.disabled = false;
          submitButton.textContent = "Simpan";
        });
    }
  });
});
