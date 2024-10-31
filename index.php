<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HRMS</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arvo', serif;
            background: url('image/back.jpeg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(8px);
        }

        .container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            width: 150px;
            /* Adjust size as needed */
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            text-align: left;
        }

        input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            color: #fff;
            border: none;
            border-radius: 8px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button {
            background-color: #006600;
            /* Green color from the contact page */
        }

        button:hover {
            background-color: #218838;
            /* Darker green for hover */
        }

        .home-button {
            margin-top: 10px;
            background-color: #007bff;
            /* Match contact color */
        }

        .home-button:hover {
            background-color: #0056b3;
            /* Match hover color */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="image/log.png" alt="Company Logo"> <!-- Update the path to your logo image -->
        </div>
        <h1>HRMS Login</h1>
        <form action="login.php" method="post">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required placeholder="Username">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required placeholder="Password">
            </div>
            <button type="submit">Login</button>
        </form>
        <button class="home-button" onclick="location.href='home.html'">Home</button>
    </div>
</body>

</html>