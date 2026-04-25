<!DOCTYPE html>
<html>
<head>
    <title>Boarding House Management System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .welcome-container {
            background: white;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .welcome-header h1 {
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 15px;
        }
        .welcome-header p {
            color: #666;
            font-size: 18px;
            margin-bottom: 40px;
        }
        .button-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 15px 40px;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 18px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-login {
            background: linear-gradient(to right, #3498db, #2980b9);
        }
        .btn-register {
            background: linear-gradient(to right, #27ae60, #2ecc71);
        }
    </style>
</head>
<body>

    <div class="welcome-container">
        <div class="welcome-header">
            <h1>🏠 Boarding House Management</h1>
            <p>Manage your rooms and tenants easily.</p>
        </div>

        <div class="button-group">
            <a href="login.php" class="btn btn-login">🔐 Login</a>
            <a href="register.php" class="btn btn-register">✨ Register</a>
        </div>
    </div>

</body>
</html>