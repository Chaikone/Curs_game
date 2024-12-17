<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: home.php");
    exit();
}

require_once 'db.php';

if (isset($_GET['id'])) {
    $set_id = $_GET['id'];

    $query = "SELECT * FROM word_sets WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $set_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $set = $result->fetch_assoc();

    $words_query = "SELECT * FROM words WHERE set_id = ?";
    $stmt = $conn->prepare($words_query);
    $stmt->bind_param("i", $set_id);
    $stmt->execute();
    $words_result = $stmt->get_result();
    $words = $words_result->fetch_all(MYSQLI_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['name']) && !empty(trim($_POST['name']))) {
            $new_name = trim($_POST['name']);

            $update_query = "UPDATE word_sets SET name = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("si", $new_name, $set_id);
            $stmt->execute();
        }

        if (isset($_POST['new_word']) && !empty(trim($_POST['new_word']))) {
            $new_word = trim($_POST['new_word']);
            $insert_word_query = "INSERT INTO words (word, set_id) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_word_query);
            $stmt->bind_param("si", $new_word, $set_id);
            $stmt->execute();
        }

        if (isset($_POST['delete_word_id'])) {
            $delete_word_id = $_POST['delete_word_id'];
            $delete_word_query = "DELETE FROM words WHERE id = ?";
            $stmt = $conn->prepare($delete_word_query);
            $stmt->bind_param("i", $delete_word_id);
            $stmt->execute();
        }

        header("Location: edit_word_set.php?id=$set_id");
        exit();
    }
} else {
    echo "Набор слов не найден.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование набора слов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Редактирование набора слов</h2>
        <form method="POST" action="">
            <label for="name">Название набора:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($set['name']) ?>" required>
            <button type="submit" class="btn">Сохранить изменения</button>
        </form>

        <h3>Слова в наборе</h3>
        <ul>
            <?php foreach ($words as $word): ?>
                <li>
                    <?= htmlspecialchars($word['word']) ?>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="delete_word_id" value="<?= $word['id'] ?>">
                        <button type="submit" class="btn btn-delete">Удалить</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Добавить новое слово</h3>
        <form method="POST" action="">
            <label for="new_word">Новое слово:</label>
            <input type="text" id="new_word" name="new_word" required>
            <button type="submit" class="btn">Добавить</button>
        </form>
    </div>
</body>
</html>
