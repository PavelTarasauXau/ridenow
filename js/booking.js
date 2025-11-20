// /js/booking.js
export function initBooking() {
  const form   = document.getElementById('bookingForm');
  const carIdInput = document.getElementById('bf_car_id');
  const hintEl = document.getElementById('bf_hint');
  const msgEl  = document.getElementById('bf_msg');
  const selects = document.querySelectorAll('.bf-select');

  if (!form || !carIdInput) return;

  // выбор машины из карточки
  selects.forEach(btn => {
    btn.addEventListener('click', () => {
      const id    = btn.dataset.car;
      const price = btn.dataset.price;

      carIdInput.value = id;
      hintEl.textContent = `Вы выбрали авто #${id}, цена ${price} р/сутки`;
      msgEl.textContent = '';

      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // отправка формы бронирования через fetch
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msgEl.textContent = '';
    hintEl.textContent = '';

    if (!carIdInput.value) {
      msgEl.textContent = 'Сначала выберите автомобиль ниже.';
      msgEl.style.color = 'darkred';
      return;
    }

    const formData = new FormData(form);

    try {
      const resp = await fetch('/api/book.php', {
        method: 'POST',
        body: formData
      });

      const data = await resp.json();
      if (data.ok) {
        msgEl.textContent = `Бронирование создано, номер: ${data.booking_id}`;
        msgEl.style.color = 'green';
      } else {
        msgEl.textContent = data.error || 'Ошибка бронирования';
        msgEl.style.color = 'darkred';
      }
    } catch (err) {
      msgEl.textContent = 'Сервер недоступен, попробуйте позже';
      msgEl.style.color = 'darkred';
    }
  });
}
