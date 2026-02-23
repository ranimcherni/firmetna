<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
</head>
<body>
    <h1>Simple Test Login</h1>
    <form method="post" action="/login">
        <div>
            <label>Email:</label>
            <input type="email" name="email" value="admin@firmetna.com" required>
        </div>
        <div>
            <label>Password:</label>
            <input type="password" name="password" value="admin123" required>
        </div>
        <div>
            <button type="submit">Login</button>
        </div>
    </form>
</body>
</html>
