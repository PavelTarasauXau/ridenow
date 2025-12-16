export function initBooking() {
  const form = document.getElementById('bookingForm');
  const carIdInput = document.getElementById('bf_car_id');
  const hintEl = document.getElementById('bf_hint');
  const msgEl = document.getElementById('bf_msg');
  const selects = document.querySelectorAll('.bf-select');

  if (!form || !carIdInput) return;

  const setMsg = (text, ok = false) => {
    msgEl.textContent = text || '';
    msgEl.style.color = ok ? 'green' : 'darkred';
  };

  const setHint = (text) => {
    hintEl.textContent = text || '';
  };

  // Выбор авто из карточки
  selects.forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.car;
      const price = btn.dataset.price;

      carIdInput.value = id;
      setHint(`Вы выбрали авто #${id}, цена ${price} р/сутки`);
      setMsg('');

      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // Отправка формы
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    setMsg('');
    // hint не очищаем — пусть показывает выбранную машину

    if (!carIdInput.value) {
      setMsg('Сначала выберите автомобиль ниже.');
      return;
    }

    const formData = new FormData(form);

    try {
      const resp = await fetch('/api/book.php', {
        method: 'POST',
        body: formData,
      });

      // если сервер вернул НЕ json — тоже поймаем
      const data = await resp.json().catch(() => null);

      if (!data) {
        setMsg('Сервер вернул некорректный ответ');
        return;
      }

      // 401 — не авторизован
      if (resp.status === 401) {
        setMsg(data.error || 'Требуется авторизация');
        // можно редиректить:
        // window.location.href = '/auth/login.php';
        return;
      }

      // 419 — CSRF
      if (resp.status === 419) {
        setMsg(data.error || 'Сессия истекла, обновите страницу');
        return;
      }

      if (data.ok) {
        setMsg(`Бронирование создано, номер: ${data.booking_id}`, true);
      } else {
        setMsg(data.error || 'Ошибка бронирования');
      }
    } catch (err) {
      setMsg('Сервер недоступен, попробуйте позже');
    }
  });
}
