<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medikit | Smart Diagnosis</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --primary: #38bdf8;
            --error: #ef4444;
            --success: #22c55e;
            --text: #f1f5f9;
            --text-dim: #94a3b8;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 900px;
            padding: 40px 0;
        }
        header {
            text-align: center;
            margin-bottom: 40px;
        }
        .badge {
            background: rgba(56, 189, 248, 0.1);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(56, 189, 248, 0.2);
        }
        h1 {
            font-weight: 800;
            font-size: 2.5rem;
            margin: 20px 0 10px;
            letter-spacing: -1px;
        }
        .error-msg {
            color: var(--error);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        .card {
            background: var(--card);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }
        h2 {
            font-size: 1.2rem;
            color: var(--primary);
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        pre {
            background: #000;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .explanation {
            line-height: 1.7;
            color: var(--text-dim);
            font-size: 1rem;
        }
        .fix-container {
            border-left: 4px solid var(--success);
            padding-left: 20px;
            margin-top: 20px;
        }
        .location {
            font-size: 0.8rem;
            color: var(--text-dim);
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 0.8rem;
            color: var(--text-dim);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <span class="badge">Medikit Immunity Active</span>
            <h1>Runtime Healed</h1>
            <div class="error-msg"><?= htmlspecialchars($originalError->getMessage()) ?></div>
        </header>

        <div class="card">
            <h2>🩺 Diagnosis</h2>
            <div class="explanation">
                <?= nl2br(htmlspecialchars($diagnosis->explanation)) ?>
            </div>
        </div>

        <?php if ($diagnosis->suggestedFix): ?>
        <div class="card">
            <h2>💊 Prescribed Fix</h2>
            <div class="location">Suggested for <?= htmlspecialchars($context->file) ?>:<?= $context->line ?></div>
            <div class="fix-container">
                <pre><?= htmlspecialchars($diagnosis->suggestedFix) ?></pre>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <h2>🔍 Source Context</h2>
            <pre><?= htmlspecialchars($context->snippet) ?></pre>
        </div>

        <div class="footer">
            Developed by YakNet &bull; Powered by Gemini AI
        </div>
    </div>
</body>
</html>
