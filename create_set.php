<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_role = $is_logged_in ? $_SESSION['role'] : ''; 
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);

    if (empty($name)) {
        $message = 'Имя набора не может быть пустым.';
    } else {
        $creator_id = $_SESSION['user_id']; 

        $query = "INSERT INTO word_sets (name, creator_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $name, $creator_id);

        if ($stmt->execute()) {
            $message = 'Набор слов успешно создан!';
        } else {
            $message = 'Ошибка при создании набора слов: ' . $stmt->error;
        }

        $stmt->close();
    }
}

$query = "SELECT * FROM word_sets WHERE creator_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if (isset($_POST['set_id']) && isset($_POST['word'])) {
    $set_id = $_POST['set_id'];
    $word = trim($_POST['word']); 

    if (!empty($word)) {
        $query = "INSERT INTO words (set_id, word) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is', $set_id, $word);

        if ($stmt->execute()) {
            $message = 'Слово успешно добавлено!';
        } else {
            $message = 'Ошибка при добавлении слова: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = 'Слово не может быть пустым.';
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание и управление наборами слов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div class="nav-container">
        <div class="nav-left">
            <a href="home.php">Главная</a>
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

<div class="container">
    <h1>Управление наборами слов</h1>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <h2>Создать новый набор слов</h2>
    <form method="POST" action="create_set.php">
        <input type="text" name="name" placeholder="Название набора" required>
        <button type="submit">Создать набор</button>
    </form>

    <h2>Ваши наборы слов</h2>
    <table>
        <thead>
            <tr>
                <th>Название набора</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <form method="POST" action="create_set.php">
                            <input type="hidden" name="set_id" value="<?= $row['id'] ?>">
                            <input type="text" name="word" placeholder="Введите слово" required>
                            <button type="submit">Добавить слово</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
