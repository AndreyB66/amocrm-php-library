## 📦 Установка

<details>
<summary><b>composer.json</b> (нажмите, чтобы развернуть)</summary>

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Integrat\\Amocrm\\": "vendor/integrat/amocrm-library/src/Integrat/Amocrm/" // Необходимо явно указать
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/integrat/amocrm-library"
        }
    ],
    "require": {
        "integrat/amocrm-library": "^1.0"
    }
}
```

</details>

Или выполните пошагово:

**1. Добавьте репозиторий в `composer.json`:**
```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Integrat\\Amocrm\\": "vendor/integrat/amocrm-library/src/Integrat/Amocrm/" // Необходимо явно указать
        }
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/integrat/amocrm-library"
        }
    ],
    "require": {
        "integrat/amocrm-library": "^1.0"
    }
}
```
