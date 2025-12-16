export function initContracts() {
    const list = document.getElementById("contractsList");
    const csrfEl = document.getElementById("csrfToken");
  
    if (!list || !csrfEl) return;
    const csrf = csrfEl.value;
  
    list.addEventListener("click", async (e) => {
      const btn = e.target.closest("[data-cancel]");
      if (!btn) return;
  
      const card = btn.closest(".contract-card");
      const msgEl = card.querySelector("[data-msg]");
      const bookingId = card.dataset.bookingId;
  
      msgEl.textContent = "";
      msgEl.classList.remove("contract-msg--ok", "contract-msg--bad");
  
      if (!bookingId) return;
  
      if (!confirm("Точно отменить бронь?")) return;
  
      btn.disabled = true;
  
      try {
        const fd = new FormData();
        fd.append("csrf", csrf);
        fd.append("booking_id", bookingId);
  
        const resp = await fetch("/api/cancel_booking.php", {
          method: "POST",
          body: fd,
        });
  
        const data = await resp.json().catch(() => null);
        if (!data) {
          msgEl.textContent = "Сервер вернул некорректный ответ";
          msgEl.classList.add("contract-msg--bad");
          btn.disabled = false;
          return;
        }
  
        if (!resp.ok || !data.ok) {
          msgEl.textContent = data.error || "Ошибка отмены";
          msgEl.classList.add("contract-msg--bad");
          btn.disabled = false;
          return;
        }
  
        // Успех: обновляем UI
        msgEl.textContent = "Бронь отменена";
        msgEl.classList.add("contract-msg--ok");
  
        const statusEl = card.querySelector(".contract-status");
        statusEl.textContent = "Отменено";
        statusEl.classList.remove("contract-status--pending", "contract-status--approved");
        statusEl.classList.add("contract-status--cancelled");
  
        // оставить кнопку disabled
      } catch (err) {
        msgEl.textContent = "Сервер недоступен, попробуйте позже";
        msgEl.classList.add("contract-msg--bad");
        btn.disabled = false;
      }
    });
  }
  