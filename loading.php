<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading - Dress to Impress</title>
    <style>
        body {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            overflow: hidden;
        }

        .logo-container {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .logo {
            width: 300px;
            height: auto;
            filter: drop-shadow(0 0 20px rgba(223, 129, 179, 0.5));
        }

        .loading-bar-container {
            width: 300px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin-top: 30px;
            overflow: hidden;
            opacity: 0;
            animation: fadeIn 0.5s ease-out 0.5s forwards;
        }

        .loading-bar {
            width: 0%;
            height: 100%;
            background: #DF81B3;
            border-radius: 2px;
            animation: loading 2s ease-out 1s forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes loading {
            0% {
                width: 0%;
            }
            50% {
                width: 70%;
            }
            100% {
                width: 100%;
            }
        }

        .loading-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(223, 129, 179, 0.2) 0%, rgba(223, 129, 179, 0) 70%);
            border-radius: 50%;
            animation: glow 2s ease-in-out infinite;
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }

        @keyframes glow {
            0% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }
            100% {
                transform: scale(1);
                opacity: 0.5;
            }
        }
    </style>
</head>
<body>
    <div class="loading-glow"></div>
    <div class="logo-container">
        <img src="dresstoimpress.png" alt="Dress to Impress Logo" class="logo">
    </div>
    <div class="loading-bar-container">
        <div class="loading-bar"></div>
    </div>

    <script>
        // Redirect to prelogin.php after animation completes
        setTimeout(() => {
            window.location.href = 'prelogin.php';
        }, 3500); // Wait for animations to complete (0.5s initial delay + 1s loading delay + 2s loading)
    </script>
</body>
</html> 