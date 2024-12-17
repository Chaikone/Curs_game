<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : ''; 

if (!$is_logged_in) {
    header('Location: login.php'); 
    exit;
}

require_once 'db.php';

if (!isset($_SESSION['game_started']) || $_SESSION['game_started'] === false) {
    $_SESSION['game_started'] = false;
    $_SESSION['words'] = [];
    $_SESSION['current_index'] = 0;
    $_SESSION['score'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['start_game'])) {
    $set_id = $_POST['set_id']; 

    $query = "SELECT word FROM words WHERE set_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $set_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $words = [];

    while ($row = $result->fetch_assoc()) {
        $words[] = $row['word'];
    }

    $_SESSION['words'] = $words;
    $_SESSION['current_index'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['game_started'] = true;

    header("Location: game.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['next_word'])) {
    $score = $_SESSION['score'];
    $current_index = $_SESSION['current_index'];
    $words = $_SESSION['words'];

    if ($current_index < count($words) - 1) {
        $_SESSION['current_index'] = $current_index + 1;
        $_SESSION['score'] = $score + 1;
    } else {
        $_POST['end_game'] = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['skip_word'])) {
    $current_index = $_SESSION['current_index'];
    $words = $_SESSION['words'];

    if ($current_index < count($words) - 1) {
        $_SESSION['current_index'] = $current_index + 1;
    } else {
        $_POST['end_game'] = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['end_game'])) {
    $score = $_SESSION['score'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO scores (user_id, correct_words) VALUES (?, ?)
              ON DUPLICATE KEY UPDATE correct_words = GREATEST(correct_words, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $user_id, $score, $score);
    $stmt->execute();

    $_SESSION['game_started'] = false; 
    header("Location: game.php");
    exit;
}

$words = $_SESSION['words'];
$current_index = $_SESSION['current_index'];
$current_word = isset($words[$current_index]) ? $words[$current_index] : '';
$score = $_SESSION['score'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Игра - Elias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .nav-container {
            background-color: #333;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .nav-left a {
            color: white;
            text-decoration: none;
            font-size: 1.2em;
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-right span {
            margin-right: 15px;
        }

        .nav-right a {
            color: white;
            text-decoration: none;
            background-color: #0066cc;
            padding: 5px 15px;
            border-radius: 5px;
        }

        .nav-right a:hover {
            background-color: #004a99;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        p {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 2em;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            border: 2px solid #0066cc;
            padding: 10px;
            border-radius: 10px;
            background-color: #f0f8ff;
        }

        h2.word-box {
            border: 2px dashed #007bff;
            background-color: #e6f7ff;
            padding: 15px;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }

        button:hover {
            background-color: #218838;
        }

        select {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<header>
    <div class="nav-container">
        <div class="nav-left">
            <a href="home.php">Главная</a>
        </div>
        <div class="nav-right">
            <span>Привет, <?= htmlspecialchars($username) ?></span>
            <a href="logout.php" class="btn">Выход</a>
        </div>
    </div>
</header>

<div class="container">
    <p>Счет: <?= $score ?></p>

    <?php if ($_SESSION['game_started']): ?>
        <h2 class="word-box">Слово: <?= htmlspecialchars($current_word) ?></h2>
        <form method="post">
            <button type="submit" name="next_word" class="btn">Следующее слово</button>
            <button type="submit" name="skip_word" class="btn">Пропустить слово</button>
            <button type="submit" name="end_game" class="btn">Завершить игру</button>
        </form>
    <?php else: ?>
        <form method="post">
            <label for="set_id">Выберите набор слов:</label>
            <select name="set_id" id="set_id">
                <?php
                $query = "SELECT id, name FROM word_sets";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
            <button type="submit" name="start_game" class="btn">Начать игру</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
