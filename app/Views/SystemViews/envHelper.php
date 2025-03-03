<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create .env File</title>
    <style>
        body { font-family: sans-serif; padding: 2em; }
        form { max-width: 500px; margin: auto; }
        label { display: block; margin-top: 1em; }
        input[type="text"], input[type="number"] { width: 100%; padding: 0.5em; }
        input[type="submit"] { margin-top: 1em; padding: 0.7em 1.5em; }
    </style>
</head>
<body>
    <h1>Create Your .env File</h1>
    <form method="post" action="">
        <label for="APP_ENV">APP_ENV:</label>
        <input type="text" id="APP_ENV" name="APP_ENV" placeholder="development" required>

        <label for="DB_HOST">DB_HOST:</label>
        <input type="text" id="DB_HOST" name="DB_HOST" placeholder="localhost" required>

        <label for="DB_NAME">DB_NAME:</label>
        <input type="text" id="DB_NAME" name="DB_NAME" placeholder="your_database" required>

        <label for="DB_USERNAME">DB_USERNAME:</label>
        <input type="text" id="DB_USERNAME" name="DB_USERNAME" placeholder="root" required>

        <label for="DB_PASSWORD">DB_PASSWORD:</label>
        <input type="text" id="DB_PASSWORD" name="DB_PASSWORD" placeholder="your_password" required>

        <label for="LOG_DIRECTORY">LOG_DIRECTORY:</label>
        <input type="text" id="LOG_DIRECTORY" name="LOG_DIRECTORY" placeholder="logs" required>

        <label for="MAX_LOG_FILE_SIZE">MAX_LOG_FILE_SIZE (bytes):</label>
        <input type="number" id="MAX_LOG_FILE_SIZE" name="MAX_LOG_FILE_SIZE" placeholder="10485760" required>

        <input type="submit" value="Generate .env File">
    </form>
</body>
</html>
