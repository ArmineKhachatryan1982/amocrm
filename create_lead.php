<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать сделку</title>
</head>
<body>
    <h2>Создание новой сделки</h2>
    <form action="handle_create_lead.php" method="post">
        <label>
            Название сделки:<br>
            <input type="text" name="name" required>
        </label>
        <br><br>

        <label>
            Сумма (₽):<br>
            <input type="number" name="price" required>
        </label>
        <br><br>

        <label>
            Примечание:<br>
            <textarea name="note" rows="4" cols="40"></textarea>
        </label>
        <br><br>

        <button type="submit">Создать сделку</button>
    </form>
</body>
</html>