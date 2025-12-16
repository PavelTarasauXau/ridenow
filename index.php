<?php
// Старт сессии и данные пользователя (если вход уже делали где-то на сайте)
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null; // ожидание: ['id'=>..., 'full_name'=>..., 'email'=>...]
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideNow - Аренда автомобилей</title>

    <link rel="stylesheet" href="/css/style.css">
    <!--Fonts connection-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<?php require __DIR__ . '/includes/header.php'; ?>

<main class="main">
    <section class="hero">
        <div class="main-pic">
            <img src="/pics/porsche-taycan-4-2024-avtomobili-porsche-1712636.jpg" alt="Porsche Taycan">
        </div>
        <div class="hero-content">
            <h1>Аренда автомобиля в пару кликов</h1>
            <a class="rent-btn" href="/pages/fleet.php">Арендовать авто</a>
        </div>
    </section>

    <section class="booking">
  <div class="booking-container">
    <form class="booking-form" action="/pages/fleet.php" method="get" autocomplete="off">
      <legend><p class="booking-slogan">Выбери, забронируй, поезжай<br>Без очереди и бумаг</p></legend>

      <div class="form-group">
        <label>Откуда</label>
        <input type="text" name="place" placeholder="Введите место">
      </div>

      <div class="form-group">
        <label>Дата и время начала</label>
        <input type="text" id="start" name="start" placeholder="Выберите дату начала" class="date-input">
      </div>

      <div class="form-group">
        <label>Дата и время окончания</label>
        <input type="text" id="end" name="end" placeholder="Выберите дату окончания" class="date-input">
      </div>

      <button type="submit" class="findauto-btn">Найти авто</button>
    </form>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
  const startPicker = flatpickr("#start", {
    enableTime: true,
    dateFormat: "d.m.Y H:i",
    minDate: "today",
    time_24hr: true,
    onChange: ([date]) => endPicker.set('minDate', date || "today")
  });
  const endPicker = flatpickr("#end", {
    enableTime: true,
    dateFormat: "d.m.Y H:i",
    minDate: "today",
    time_24hr: true
  });
</script>

    <section class="fleet">
        <h2>Наш автопарк</h2>
        <p class="fleet-subtitle">Популярные автомобили</p>
        <div class="cars-grid">
            <div class="car-card">
                <img src="/pics/kia_rio_4.jpg" alt="Kia Rio">
                <h3>Kia Rio</h3>
                <div class="car-details">
                    <span>механика</span>
                    <span>бензин</span>
                    <span>5 мест</span>
                </div>
                <p class="car-price">59 р/сутки</p>
                <a class="rent-btn" href="/pages/fleet.php?q=Kia%20Rio">Забронировать</a>
            </div>

            <div class="car-card">
                <img src="/pics/novyy_geely_coolray_2_c0b.webp" alt="Geely Coolray">
                <h3>Geely Coolray</h3>
                <div class="car-details">
                    <span>автомат</span>
                    <span>бензин</span>
                    <span>5 мест</span>
                </div>
                <p class="car-price">89 р/сутки</p>
                <a class="rent-btn" href="/pages/fleet.php?q=Geely%20Coolray">Забронировать</a>
            </div>

            <div class="car-card">
                <img src="/pics/renaultscenic.jpeg" alt="Renault Scenic">
                <h3>Renault Scenic</h3>
                <div class="car-details">
                    <span>механика</span>
                    <span>бензин</span>
                    <span>5 мест</span>
                </div>
                <p class="car-price">99 р/сутки</p>
                <a class="rent-btn" href="/pages/fleet.php?q=Renault%20Scenic">Забронировать</a>
            </div>
        </div>
    </section>

    <section id="how" class="how-it-works">
        <h2>Как это работает</h2>
        <div class="steps">
            <div class="step">
                <div class="step-icon">1</div>
                <h3>Зарегистрируйтесь</h3>
                <p>Пройдите регистрацию на сайте либо скачайте приложение.</p>
            </div>
            <div class="step">
                <div class="step-icon">2</div>
                <h3>Найдите и забронируйте</h3>
                <p>Найдите автомобиль на карте и забронируйте его на нужное время</p>
            </div>
            <div class="step">
                <div class="step-icon">3</div>
                <h3>Откройте автомобиль</h3>
                <p>Подойдите к машине и откройте ее через приложение</p>
            </div>
            <div class="step">
                <div class="step-icon">4</div>
                <h3>Поезжайте!</h3>
                <p>Вас ждет чистый и заправленный автомобиль. Верните его в разрешенной зоне.</p>
            </div>
        </div>
    </section>

    <section class="advantages" id="pricing">
        <h2>Тарифы</h2>
        <p class="fleet-subtitle" style="text-align: center; margin-bottom: 48px;">Прозрачные цены без скрытых платежей</p>
        <div class="advantages-grid">
            <div class="advantage-card">
                <h3>Эконом</h3>
                <p class="car-price" style="font-size: 32px; margin: 16px 0;">от 59 р/сутки</p>
                <p>Базовые модели автомобилей для городских поездок. Идеально для ежедневных задач.</p>
                <ul style="text-align: left; margin-top: 20px; padding-left: 20px;">
                    <li>Малолитражные автомобили</li>
                    <li>Механическая коробка</li>
                    <li>Бензин</li>
                    <li>Страховка включена</li>
                </ul>
            </div>
            <div class="advantage-card">
                <h3>Комфорт</h3>
                <p class="car-price" style="font-size: 32px; margin: 16px 0;">от 89 р/сутки</p>
                <p>Современные автомобили с автоматической коробкой передач. Комфорт и удобство.</p>
                <ul style="text-align: left; margin-top: 20px; padding-left: 20px;">
                    <li>Седаны и кроссоверы</li>
                    <li>Автоматическая коробка</li>
                    <li>Бензин / Гибрид</li>
                    <li>Полная страховка</li>
                </ul>
            </div>
            <div class="advantage-card">
                <h3>Премиум</h3>
                <p class="car-price" style="font-size: 32px; margin: 16px 0;">от 150 р/сутки</p>
                <p>Премиальные автомобили для особых случаев. Максимальный комфорт и престиж.</p>
                <ul style="text-align: left; margin-top: 20px; padding-left: 20px;">
                    <li>Премиум класс</li>
                    <li>Автоматическая коробка</li>
                    <li>Электро / Гибрид</li>
                    <li>Премиум страховка</li>
                </ul>
            </div>
            <div class="advantage-card">
                <h3>Что включено</h3>
                <p style="margin-top: 20px;">Во всех тарифах:</p>
                <ul style="text-align: left; margin-top: 20px; padding-left: 20px;">
                    <li>✅ Страховка ОСАГО и КАСКО</li>
                    <li>✅ Техническое обслуживание</li>
                    <li>✅ Мойка автомобиля</li>
                    <li>✅ Круглосуточная поддержка</li>
                    <li>✅ Бесплатная отмена за 24 часа</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="advantages">
        <h2>Почему RideNow?</h2>
        <div class="advantages-grid">
            <div class="advantage-card">
                <h3>Экономия времени</h3>
                <p>Никаких очередей в прокате. Бронируйте за минуты, садитесь и поезжайте.</p>
            </div>
            <div class="advantage-card">
                <h3>Всё включено</h3>
                <p>Страховка, техобслуживание и мойка уже в стоимости. Никаких скрытых платежей.</p>
            </div>
            <div class="advantage-card">
                <h3>Свобода передвижения</h3>
                <p>Паркуйтесь в любой разрешённой зоне города. Поездка заканчивается, когда вы выходите из машины.</p>
            </div>
            <div class="advantage-card">
                <h3>Экологично</h3>
                <p>В нашем парке есть электромобили. Помогите городу дышать легче.</p>
            </div>
        </div>
    </section>

    <section class="section" id="contacts" style="background: var(--card);">
        <div class="container">
            <h2>Контакты</h2>
            <p class="fleet-subtitle" style="text-align: center; margin-bottom: 48px;">Свяжитесь с нами любым удобным способом</p>
            
            <div class="advantages-grid">
                <div class="advantage-card">
                    <h3>📞 Телефон</h3>
                    <p style="font-size: 20px; font-weight: 600; margin: 16px 0;">
                        <a href="tel:+375291234567" style="color: var(--brand); text-decoration: none;">+375 (29) 123-45-67</a>
                    </p>
                    <p>Круглосуточная поддержка</p>
                    <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Звонок бесплатный</p>
                </div>
                
                <div class="advantage-card">
                    <h3>✉️ Email</h3>
                    <p style="font-size: 18px; font-weight: 600; margin: 16px 0;">
                        <a href="mailto:info@ridenow.local" style="color: var(--brand); text-decoration: none;">info@ridenow.local</a>
                    </p>
                    <p>Ответим в течение 24 часов</p>
                    <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Для общих вопросов</p>
                </div>
                
                <div class="advantage-card">
                    <h3>💬 Онлайн чат</h3>
                    <p style="font-size: 18px; font-weight: 600; margin: 16px 0;">Доступен 24/7</p>
                    <p>Быстрые ответы на ваши вопросы</p>
                    <button class="rent-btn" style="margin-top: 16px; width: 100%;">Открыть чат</button>
                </div>
                
                <div class="advantage-card">
                    <h3>📍 Адрес офиса</h3>
                    <p style="font-size: 16px; margin: 16px 0;">
                        г. Минск, ул. Примерная, д. 1<br>
                        БЦ "Бизнес-центр", офис 101
                    </p>
                    <p>Пн-Пт: 9:00 - 18:00</p>
                    <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Сб-Вс: по предварительной записи</p>
                </div>
            </div>
            
            <div style="margin-top: 48px; padding: 32px; background: var(--bg-2); border-radius: var(--radius); text-align: center;">
                <h3 style="color: var(--white); margin-bottom: 16px;">Есть вопросы?</h3>
                <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px;">Наша команда готова помочь вам в любое время</p>
                <a href="mailto:info@ridenow.local" class="rent-btn" style="display: inline-block;">Написать нам</a>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <p>&copy; 2025 RideNow. Все права защищены.</p>
</footer>
</body>
</html>
