<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Rapid ERP</title>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        #particles-js {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Dark background */
        }
        .content {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: #fff;
            z-index: 1;
        }
    </style>
</head>
<body>

    <div id="particles-js"></div>

    <div class="content">
        <h1>Welcome to Rapid ERP</h1>
        <p>Your one-stop solution for efficient business management. Streamline your operations, track your progress, and make informed decisions with ease.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            particlesJS('particles-js', {
                "particles": {
                    "number": {
                        "value": 80,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#ffffff"
                    },
                    "shape": {
                        "type": "circle"
                    },
                    "opacity": {
                        "value": 0.5
                    },
                    "size": {
                        "value": 3
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#ffffff",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 6
                    }
                },
                "interactivity": {
                    "detect_on": "window",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "repulse"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    }
                }
            });
        });
    </script>

</body>
</html>
