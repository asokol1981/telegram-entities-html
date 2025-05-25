# Telegram Entities HTML Renderer

[![tests](https://github.com/asokol1981/telegram-entities-html/workflows/tests/badge.svg)](https://github.com/asokol1981/telegram-entities-html/actions) [![codecov](https://codecov.io/gh/asokol1981/telegram-entities-html/branch/main/graph/badge.svg)](https://codecov.io/gh/asokol1981/telegram-entities-html)

📦 Turn Telegram message entities into HTML with ease.

---

## Features

- Converts `text` and `caption` with `entities` or `caption_entities` to HTML
- Supports all Telegram formatting types (bold, italic, code, etc.)
- Outputs safe and valid HTML, suitable for web rendering
- Written in modern PHP

---

## Installation

If the package is not available on Packagist, you can install it by adding the following to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/asokol1981/telegram-entities-html"
    }
  ],
  "require": {
    "asokol1981/telegram-entities-html": "dev-main"
  }
}
```

Then run:

```bash
composer update
```

⚠️ Make sure your project allows the dev-main version or use a specific tag (e.g. ^1.0) once available.

## Usage

```php
use ASokol1981\Telegram\Entities\Html\Renderer;

$renderer = new Renderer();

$html = $renderer->render(
    '😎 Text message with bold and italic',
    [
        ['type' => 'bold', 'offset' => 21, 'length' => 4],
        ['type' => 'italic', 'offset' => 30, 'length' => 6],
    ]
);

echo $html; // 😎 Text message with <b>bold</b> and <i>italic</i>
```

Works with both `text` + `entities` and `caption` + `caption_entities`.

Supported entity types:

- bold
- italic
- underline
- strikethrough
- spoiler
- blockquote
- expandable_blockquote
- code
- pre
- text_link
- text_mention
- custom_emoji

## 🛠️ Makefile Commands

To simplify local development, the following `make` commands are available:

### 📦 Installation

```bash
make install
```

Builds the Docker container, starts it in the background, and installs Composer dependencies.

---

### 🏗️ Build Image

```bash
make build
```

Builds the Docker image with the tag `asokol1981/telegram-entities-html`.

---

### ▶️ Start Container

```bash
make start
```

Starts the container in detached mode.

---

### ⏹️ Stop Container

```bash
make stop
```

Stops and removes the container (volumes are preserved).

---

### 🧹 Uninstall

```bash
make uninstall
```

Stops and removes the container, associated volumes, and the Docker image.

---

### 🐚 Shell Access

```bash
make exec -- bash
```

Runs a command inside the container.
Be sure to use `--` to separate `make` arguments from the actual command:

```bash
make exec -- php -v
```

---

### 🎼 Composer

```bash
make composer -- require --dev phpunit/phpunit
```

Executes a Composer command inside the container.
Example: install PHPUnit as a dev dependency.

---

### ✅ Run Tests

```bash
make test
```

Runs tests and shows code coverage summary in the terminal.

---

### 📊 HTML Coverage Report

```bash
make coverage
```

Generates a code coverage report in HTML format and saves it in the `coverage/` directory.

---

### 🧱 Artisan

```bash
make artisan -- list
```

Executes an Artisan command inside the container.

---

### 🧹 Code Style Fix

```bash
make php-cs-fixer
```

Runs PHP-CS-Fixer inside the container to automatically fix coding style issues according to the defined `.php-cs-fixer.dist.php` configuration.

---

**ℹ️ Note:**
When passing arguments to `exec`, `composer`, `artisan`, `test`, or `coverage` targets, **always prefix them with `--`** so `make` doesn't interpret them as its own flags.

## License

MIT © [asokol1981](https://github.com/asokol1981)
