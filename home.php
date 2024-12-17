<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';

require_once 'db.php';

$query = "SELECT u.username, s.correct_words 
          FROM scores s
          JOIN users u ON s.user_id = u.id
          ORDER BY s.correct_words DESC
          LIMIT 10";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        header {
            background-color: #333;
            padding: 10px 0;
            color: white;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .nav-left a {
            color: white;
            font-size: 1.2em;
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-right span {
            margin-right: 20px;
            font-size: 1.1em;
        }

        .nav-right a {
            color: white;
            background-color: #0066cc;
            padding: 5px 15px;
            border-radius: 5px;
            margin-left: 10px;
        }

        .nav-right a:hover {
            background-color: #004a99;
        }

        .main-container {
            margin-top: 50px;
            text-align: center;
        }

        .btn-play {
            display: inline-block;
            padding: 15px 30px;
            background-color: #0066cc;
            color: white;
            font-size: 1.2em;
            border-radius: 8px;
            margin-top: 20px;
            margin-bottom: 30px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-play:hover {
            background-color: #004a99;
        }

        .leaderboard {
            margin-top: 30px;
            text-align: center;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php">Главная</a>
            </div>
            <div class="nav-right">
                <?php if ($is_logged_in): ?>
                    <span>Привет, <?= htmlspecialchars($username) ?></span>
                    <a href="logout.php" class="btn">Выход</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Войти</a>
                    <a href="register.php" class="btn">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

  <div class="container main-container">
        <h1>Добро пожаловать в Элиас!</h1>
        
        <?php if ($is_logged_in): ?>
            <a href="game.php" class="btn-play">Играть</a>
        <?php else: ?>
            <p>Чтобы начать игру, вам нужно <a href="login.php">войти</a> или <a href="register.php">зарегистрироваться</a>.</p>
        <?php endif; ?>


        <h2>Таблица лидеров</h2>
        <table>
            <thead>
                <tr>
                    <th>Место</th>
                    <th>Игрок</th>
                    <th>Угадано слов</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $rank = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $rank++ . "</td>
                                <td>" . htmlspecialchars($row['username']) . "</td>
                                <td>" . htmlspecialchars($row['correct_words']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Нет данных о лидерах</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>