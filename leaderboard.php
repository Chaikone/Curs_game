<?php
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
    <title>Лидерборд</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php">Главная</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>Таблица лидеров</h1>
        <table>
            <tr>
                <th>Пользователь</th>
                <th>Очки</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= $row['correct_words'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
