<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Under Construction – Flettons</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: url('/assets/img/bg.png') center center / cover no-repeat fixed;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #333;
        }
        body::before {
            content: "";
            position: fixed;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.2);
            backdrop-filter: blur(3px);
            z-index: 0;
        }
        .maintenance-box {
            position: relative;
            z-index: 1;
            max-width: 560px;
            background: rgba(255,255,255,0.97);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 0 0 40px;
            text-align: center;
            overflow: hidden;
        }
        .maintenance-header {
            background: #1a202c;
            padding: 24px 36px;
            margin-bottom: 28px;
        }
        .brand-logo {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .maintenance-box h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 20px;
            letter-spacing: 0.02em;
            padding: 0 36px;
        }
        .maintenance-box p {
            font-size: 1rem;
            line-height: 1.65;
            color: #444;
            margin: 0 0 1em;
            text-align: left;
            padding: 0 36px;
        }
        .maintenance-box p:last-of-type { margin-bottom: 0; }
        .thank-you { margin-top: 24px; font-weight: 600; color: #1a1a1a; }
    </style>
</head>

<body>
    <div class="maintenance-box">
        <div class="maintenance-header">
            <img src="https://flettons.group/wp-content/uploads/2025/05/Flettons-Logo-White-Transparent.png" alt="Flettons Group" class="brand-logo" />
        </div>
        <h1>Under Construction</h1>
        <p>Our quote calculator is currently undergoing maintenance while our development team works to resolve a technical issue.</p>
        <p>At this time, we are also unable to provide quotes over the phone.</p>
        <p>We apologise for any inconvenience this may cause and appreciate your patience. We ask that you please bear with us while we fix the problem. Please check back shortly.</p>
        <p class="thank-you">Thank you for your understanding.</p>
    </div>
</body>

</html>
