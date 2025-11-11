<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'] ?? null;

// Получаем параметры фильтрации из GET
$place = $_GET['place'] ?? '';
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$search = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Автопарк - RideNow</title>
    <link rel="stylesheet" href="/css/style.css">
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
            <a href="/#how" class="navlink">Как это работает</a>
            <a href="/fleet.php" class="navlink">Автопарк</a>
            <a href="/#pricing" class="navlink">Тарифы</a>
            <a href="/#contacts" class="navlink">Контакты</a>

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
    <section class="section" style="padding-top: 32px;">
        <div class="container">
            <h1>Наш автопарк</h1>
            <p style="font-size: 18px; color: var(--muted); margin-bottom: 32px;">Выберите автомобиль для аренды</p>

            <!-- Форма поиска и фильтрации -->
            <div class="booking-container" style="margin-bottom: 48px;">
                <form class="booking-form" action="/fleet.php" method="get" autocomplete="off">
                    <div class="form-group">
                        <label>Откуда</label>
                        <input type="text" name="place" placeholder="Введите место" value="<?= htmlspecialchars($place) ?>">
                    </div>

                    <div class="form-group">
                        <label>Дата и время начала</label>
                        <input type="datetime-local" name="start" value="<?= htmlspecialchars($start) ?>">
                    </div>

                    <div class="form-group">
                        <label>Дата и время окончания</label>
                        <input type="datetime-local" name="end" value="<?= htmlspecialchars($end) ?>">
                    </div>

                    <button type="submit" class="findauto-btn">Найти авто</button>
                </form>
            </div>

            <!-- Список автомобилей -->
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
                    <a class="rent-btn" href="/fleet.php?q=Kia%20Rio">Забронировать</a>
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
                    <a class="rent-btn" href="/fleet.php?q=Geely%20Coolray">Забронировать</a>
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
                    <a class="rent-btn" href="/fleet.php?q=Renault%20Scenic">Забронировать</a>
                </div>
            </div>

            <?php if ($search): ?>
                <div style="margin-top: 32px; padding: 20px; background: var(--card); border-radius: var(--radius);">
                    <h3>Вы искали: <?= htmlspecialchars($search) ?></h3>
                    <p>Фильтрация по запросу будет реализована при подключении к базе данных.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer class="footer">
    <p>&copy; 2025 RideNow. Все права защищены.</p>
</footer>
</body>
</html>

