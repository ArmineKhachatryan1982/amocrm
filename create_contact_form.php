<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать контакт</title>
</head>
<body>
    <h2>Создание нового контакта</h2>
    <form action="handle_create_contact.php" method="post">
        <label>
            Имя:<br>
            <input type="text" name="name" required>
        </label>
        <br><br>

        <label>
            Телефон:<br>
            <input type="text" name="phone">
        </label>
        <br><br>

        <button type="submit">Создать контакт</button>
    </form>
</body>
</html>
