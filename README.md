# YakNet \ Medikit

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892bf.svg?style=flat-square)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

**YakNet Medikit** is a revolutionary "Self-Healing" runtime library for PHP. It intercepts runtime exceptions, analyzes the failing code using Google Gemini AI, and provides instant diagnostics and fix suggestions through a premium UI or CLI report.

## 🚑 Why Medikit?

Traditional logging tells you *what* happened. Medikit tells you *why* it happened and *how* to fix it. It acts as a "resident doctor" for your application, providing immunity against unhandled crashes.

## 🚀 Features

- **AI-Powered Diagnostics**: Uses Gemini 3.1 Flash Lite to understand code logic and intent.
- **Context Awareness**: Automatically extracts relevant code snippets surrounding the error for deep analysis.
- **Premium Reporting**: Beautiful, dark-themed HTML diagnostic pages for web errors and colorized CLI output.
- **Zero-Config Integration**: One-line registration to protect your entire application.

## 📦 Installation

```bash
composer require yaknet/medikit
```

## 🛠 Usage

Simply register Medikit at the entry point of your application.

```php
use YakNet\Medikit\Medikit;

// This will only activate if .env has MEDIKIT_DEBUG=true
Medikit::register(); 
```

### ⚙️ Environment Variables (.env)

Medikit respects your development workflow. Create a `.env` file in your root:

```env
GEMINI_API_KEY=your_key_here
MEDIKIT_DEBUG=true
```

- **MEDIKIT_DEBUG**: Must be `true` for Medikit to intercept errors. In production (false), it stays silent.
- **Interactive Mode**: In CLI, Medikit will ask if you want to attempt a repair when a fix is found.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

---
Developed with ❤️ by [YakNet](https://yak.net.tr)
