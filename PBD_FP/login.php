<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* body {
            background: linear-gradient(to bottom right, #4a90e2, #6a9ef0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        } */
        .login-container {
            /* background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); */
            width: 100%;
            max-width: 400px;
        }

    </style>
</head>
<body class="bg-primary vh-100 d-flex justify-content-center align-items-center">
    <div class="bg-light login-container  p-5 rounded">
        <h3 class="login-title text-center">MiniMarket</h3>
        <form method="POST" action="proses_login.php">
            <div class="mb-3">
                <label for="id_karyawan" class="form-label">Id Karyawan</label>
                <input type="text" class="form-control" name="id_karyawan" placeholder="Masukkan Id">
            </div>
            <button type="submit" class="btn btn-primary w-100">Log In</button>
            <!-- <div class="text-center mt-3">
                <a href="dashboard.php" class="text-decoration-none">Forgot Password?</a>
            </div> -->
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
