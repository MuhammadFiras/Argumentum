document.addEventListener("DOMContentLoaded", function () {
  //== SCRIPT UNTUK RATING BINTANG ==
  const csrfName = document.querySelector('meta[name="csrf-token-name"]')?.getAttribute("content");
  let csrfHash = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

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
      const feedbackDiv = document.getElementById(`rating-feedback-message-${answerId}`);

      parent.querySelectorAll(".star").forEach((s) => {
        s.classList.remove("rated");
        if (parseInt(s.dataset.value) <= ratingValue) {
          s.classList.add("rated");
        }
      });

      if (feedbackDiv) {
        feedbackDiv.textContent = "Menyimpan rating...";
        feedbackDiv.className = "rating-feedback-message text-info";
      }

      const formData = new URLSearchParams();
      formData.append("rating", ratingValue);
      formData.append(csrfName, csrfHash);

      fetch(`${baseUrl}/answer/rate/${answerId}`, {
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

            document.getElementById(`avg-rating-${answerId}`).textContent = parseFloat(data.average_rating).toFixed(1);
            document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;

            parent.dataset.currentRating = ratingValue;

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
  //== SCRIPT UNTUK SUBMIT KOMENTAR (AJAX) - FINAL VERSION
  //=======================================================
  document.querySelectorAll(".comment-form").forEach((form) => {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const answerId = this.dataset.answerId;
      const commentList = document.getElementById(`comment-list-${answerId}`);
      const textarea = this.querySelector('textarea[name="comment_text"]');
      const commentText = textarea.value.trim();
      const submitButton = this.querySelector('button[type="submit"]');

      if (commentText === "") return;

      const formData = new URLSearchParams();
      formData.append("comment_text", commentText);
      formData.append(csrfName, csrfHash);

      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

      fetch(this.action, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData.toString(),
      })
        .then((response) => {
          updateCsrfToken(response.headers.get("X-CSRF-TOKEN"));
          if (!response.ok) return response.json().then((err) => Promise.reject(err));
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            const newComment = data.comment;
            const commentId = data.comment_id ?? newComment.id_comment;
            const photoUrl = `${baseUrl}/assets/images/profiles/${newComment.photo_profile ?? "default.jpg"}`;
            const isOwner = data.is_owner ?? true; // diasumsikan true jika tidak dikirim

            const newCommentHtml = `
            <div class="comment-item d-flex align-items-start mb-2"
              id="comment-item-${commentId}"
              data-answer-id="${answerId}">
              <img src="${photoUrl}" alt="${newComment.nama_lengkap}" class="rounded-circle me-2" width="24" height="24">
              <div class="comment-content w-100">
                <strong>${newComment.nama_lengkap}</strong>
                <div id="comment-text-display-${commentId}">
                  <p class="mb-0">${newComment.comment_text}</p>
                  <small class="text-muted">${newComment.created_at ?? "Baru saja"}</small>
                </div>
                <div class="comment-edit-form-area" id="comment-edit-form-area-${commentId}" style="display: none;"></div>
                ${
                  isOwner
                    ? `<div class="comment-actions mt-1">
                        <a href="#" class="btn-edit-comment small text-decoration-none" data-comment-id="${commentId}">Edit</a>
                        ·
                        <a href="#" class="btn-delete-comment small text-decoration-none text-danger" data-comment-id="${commentId}">Hapus</a>
                      </div>`
                    : ""
                }
              </div>
            </div>
          `;

            commentList.insertAdjacentHTML("beforeend", newCommentHtml);

            textarea.value = "";

            const commentCountEl = document.getElementById(`comment-count-${answerId}`);
            if (commentCountEl) {
              commentCountEl.textContent = parseInt(commentCountEl.textContent) + 1;
            }
          } else {
            alert("Gagal menambahkan komentar: " + data.message);
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert(error.message || "Terjadi kesalahan saat mengirim komentar.");
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

  document.body.addEventListener("click", function (event) {
    // --- LOGIKA HAPUS KOMENTAR ---
    if (event.target.classList.contains("btn-delete-comment")) {
      event.preventDefault();

      const deleteButton = event.target;
      const isAdminDelete = deleteButton.dataset.isAdminDelete === "true";

      let confirmMessage = "Apakah Anda yakin ingin menghapus komentar ini?";
      if (isAdminDelete) {
        confirmMessage += " (Sebagai Admin)";
      }

      if (!confirm(confirmMessage)) {
        return;
      }

      const commentId = deleteButton.dataset.commentId;
      const commentItemEl = document.getElementById(`comment-item-${commentId}`);
      const answerId = commentItemEl.dataset.answerId;

      const formData = new URLSearchParams();
      formData.append(csrfName, csrfHash);

      fetch(`${baseUrl}/comment/delete/${commentId}`, {
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
            commentItemEl.style.transition = "opacity 0.5s ease";
            commentItemEl.style.opacity = "0";
            setTimeout(() => {
              commentItemEl.remove();
              const commentCountEl = document.getElementById(`comment-count-${answerId}`);
              if (commentCountEl) {
                commentCountEl.textContent = Math.max(0, parseInt(commentCountEl.textContent) - 1);
              }
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

      textDisplayEl.style.display = "none";
      editFormArea.style.display = "block";
      editFormArea.innerHTML = `
      <form class="form-edit-comment" data-comment-id="${commentId}">
          <textarea class="form-control form-control-sm mb-2" name="comment_text" rows="2" required>${originalText}</textarea>
          <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
          <button type="button" class="btn btn-sm btn-outline-secondary btn-cancel-edit">Batal</button>
      </form>
    `;
    }

    // --- LOGIKA BATAL EDIT ---
    if (event.target.classList.contains("btn-cancel-edit")) {
      event.preventDefault();
      const form = event.target.closest(".form-edit-comment");
      const commentId = form.dataset.commentId;

      document.getElementById(`comment-text-display-${commentId}`).style.display = "block";
      document.getElementById(`comment-edit-form-area-${commentId}`).style.display = "none";
      document.getElementById(`comment-edit-form-area-${commentId}`).innerHTML = "";
    }

    // --- LOGIKA HAPUS RATING ---
    if (event.target.classList.contains("btn-delete-rating")) {
      event.preventDefault();

      if (!confirm("Anda yakin ingin menghapus rating Anda untuk jawaban ini?")) return;

      const deleteButton = event.target;
      const answerId = deleteButton.dataset.answerId;
      const starRatingDiv = document.querySelector(`.star-rating[data-answer-id="${answerId}"]`);

      const formData = new URLSearchParams();
      formData.append(csrfName, csrfHash);

      fetch(`${baseUrl}/answer/delete-rating/${answerId}`, {
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
            document.getElementById(`avg-rating-${answerId}`).textContent = data.average_rating;
            document.getElementById(`count-rating-${answerId}`).textContent = data.rating_count;
            starRatingDiv.querySelectorAll(".star").forEach((s) => s.classList.remove("rated"));
            starRatingDiv.dataset.currentRating = "0";
            deleteButton.style.display = "none";
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

  // --- LOGIKA SUBMIT FORM EDIT KOMENTAR ---
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

      fetch(`${baseUrl}/comment/update/${commentId}`, {
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
            textDisplayEl.querySelector("p").textContent = data.updated_text;
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
