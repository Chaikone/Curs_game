<?php

session_start();
//$_SESSION['user_id'] = $user_id;  
//$_SESSION['username'] = $username;  
//$_SESSION['role'] = $user_role;  

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];  
        $_SESSION['role_id'] = $user['role_id'];

        if ($_SESSION['role_id'] == 1) {
            header("Location: admin.php");
        } elseif($_SESSION['role_id'] == 2) {
            header("Location: create_set.php");
        }else{
            header("Location: home.php");
        }
        exit();
    } else {
        echo "Неверное имя пользователя или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Вход</h2>
        <form action="login.php" method="POST">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit" class="btn">Войти</button>
        </form>
        <p>Еще нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</body>
</html>
