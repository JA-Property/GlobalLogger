<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create .env File</title>
    <style>
        body { font-family: sans-serif; padding: 2em; }
        pre { background: #f4f4f4; padding: 1em; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Create Your .env File</h1>
    <p>Your project does not have a <code>.env</code> file. To configure your application, please create one in the project root directory using the template below:</p>
    <pre>
APP_ENV=development
DB_HOST=localhost
DB_NAME=your_database
DB_USERNAME=root
DB_PASSWORD=your_password
LOG_DIRECTORY=../logs
MAX_LOG_FILE_SIZE=10485760
    </pre>
    <p>You can copy the above template and save it as <code>.env</code> in your project's root directory.</p>
</body>
</html>
