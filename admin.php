<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: home.php");
    exit();
}

require_once 'db.php';

$query_users = "SELECT * FROM users";
$users_result = $conn->query($query_users);

$query_word_sets = "SELECT * FROM word_sets";
$word_sets_result = $conn->query($query_word_sets);

if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $user_id");
    header("Location: admin.php"); 
    exit();
}

if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $new_username = $_POST['new_username'];
    $conn->query("UPDATE users SET username = '$new_username' WHERE id = $user_id");
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_set'])) {
    $set_id = $_GET['delete_set'];


    $delete_words_query = "DELETE FROM words WHERE set_id = $set_id";
    $conn->query($delete_words_query);

    $delete_set_query = "DELETE FROM word_sets WHERE id = $set_id";
    $conn->query($delete_set_query);

    header("Location: admin.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница администратора</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="nav-left">
                <a href="home.php">Главная</a>
            </div>
            <div class="nav-right">
                <span>Привет, <?= $_SESSION['username'] ?></span>
                <a href="logout.php" class="btn">Выход</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h2>Управление пользователями</h2>
        
        <!-- Таблица пользователей -->
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя пользователя</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td>
                            <form method="POST" action="admin.php">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="text" name="new_username" value="<?= $user['username'] ?>" required>
                                <button type="submit" name="update_user" class="btn">Изменить логин</button>
                            </form>
                        </td>
                        <td>
                            <?= $user['role_id'] == 1 ? 'Администратор' : ($user['role_id'] == 2 ? 'Создатель наборов слов' : 'Пользователь') ?>
                        </td>
                        <td>
                            <a href="admin.php?delete_user=<?= $user['id'] ?>" class="btn" onclick="return confirm('Вы уверены?')">Удалить</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Управление наборами слов</h2>


        <table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название набора</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($set = $word_sets_result->fetch_assoc()): ?>
            <tr>
                <td><?= $set['id'] ?></td>
                <td><?= $set['name'] ?></td>
                <td>
                    <a href="edit_word_set.php?id=<?= $set['id'] ?>" class="btn">Редактировать</a>
                    <a href="admin.php?delete_set=<?= $set['id'] ?>" class="btn" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </div>
</body>
</html>
