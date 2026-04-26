## 📦 Установка

<details>
<summary><b>composer.json</b> (нажмите, чтобы развернуть)</summary>

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Integrat\\Amocrm\\": "vendor/integrat/amocrm-php-library/src/Integrat/Amocrm/"
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/AndreyB66/amocrm-php-library.git"
        }
    ],
    "require": {
        "integrat/amocrm-php-library": "*"
    }
}
```

</details>

Шаблон:

**1. Добавьте репозиторий в `composer.json`:**
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Integrat\\Amocrm\\": "vendor/integrat/amocrm-php-library/src/Integrat/Amocrm/"
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/AndreyB66/amocrm-php-library.git"
        }
    ],
    "require": {
        "integrat/amocrm-php-library": "*"
    }
}
```
