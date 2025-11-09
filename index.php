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
</head>
<body>
<header class="header">
    <div class="header-container">
        <div class="logo"><a href="/">RideNow</a></div>
        <nav class="nav">
            <a href="/" class="navlink">Главная</a>
            <a href="#how" class="navlink">Как это работает</a>
            <a href="/scripts/fleet.php" class="navlink">Автопарк</a>
            <a href="#pricing" class="navlink">Тарифы</a>
            <a href="#contacts" class="navlink">Контакты</a>

            <?php if ($user): ?>
                <span class="navlink" style="opacity:.9;">Привет, <?= htmlspecialchars($user['full_name'] ?? 'пользователь') ?></span>
                <a class="navlink" href="/auth/logout.php">Выйти</a>
            <?php else: ?>
                <a class="navlink" href="/auth/register.php">Регистрация</a>
                <a class="login-btn" href="/auth/login.php">Войти</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main">
    <section class="hero">
        <div class="main-pic">
            <img src="/pics/porsche-taycan-4-2024-avtomobili-porsche-1712636.jpg" alt="Porsche Taycan">
        </div>
        <div class="hero-content">
            <h1>Аренда автомобиля в пару кликов</h1>
            <a class="rent-btn" href="/scripts/fleet.php">Арендовать авто</a>
        </div>
    </section>

    <!-- Форма пока без БД: отправляем параметры на страницу автопарка (GET) -->
    <section class="booking">
        <div class="booking-container">
            <form class="booking-form" action="/scripts/fleet.php" method="get" autocomplete="off">
                <legend><p class="booking-slogan">Выбери, забронируй, поезжай<br>Без очереди и бумаг</p></legend>

                <div class="form-group">
                    <label>Откуда</label>
                    <input type="text" name="place" placeholder="Введите место">
                </div>

                <div class="form-group">
                    <label>Дата и время начала</label>
                    <input type="datetime-local" name="start">
                </div>

                <div class="form-group">
                    <label>Дата и время окончания</label>
                    <input type="datetime-local" name="end">
                </div>

                <button type="submit" class="findauto-btn">Найти авто</button>
            </form>
        </div>
    </section>

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
                <a class="rent-btn" href="/scripts/fleet.php?q=Kia%20Rio">Забронировать</a>
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
                <a class="rent-btn" href="/scripts/fleet.php?q=Geely%20Coolray">Забронировать</a>
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
                <a class="rent-btn" href="/scripts/fleet.php?q=Renault%20Scenic">Забронировать</a>
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

    <section class="client-reviews" id="contacts"></section>
</main>

<footer class="footer">
    <p>&copy; 2025 RideNow. Все права защищены.</p>
</footer>
</body>
</html>
