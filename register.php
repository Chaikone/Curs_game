<?php
session_start();
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id']; 

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Пользователь с таким именем уже существует!";
    } else {
        $query = "INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $username, $hashed_password, $role_id);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role_id'] = $role_id;

            
            if ($_SESSION['role_id'] == 1) {
                header("Location: admin.php");
            } elseif($_SESSION['role_id'] == 2) {
                header("Location: create_set.php");
             }else{
                header("Location: home.php");
        }
        exit();
        } else {
            echo "Ошибка регистрации!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Регистрация</h2>
        <form action="register.php" method="POST">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>

            <label for="role_id">Роль:</label>
            <select name="role_id" id="role_id">
                <option value="2">Создатель наборов слов</option>
                <option value="3">Пользователь</option>
            </select>

            <button type="submit" class="btn">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>
