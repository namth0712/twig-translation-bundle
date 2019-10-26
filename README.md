# TwigTranslationBundle

Symfony 4 bundle for performance translation in twig, use this bundle only for **the project has multiple languages but provides one language only**.
Example: Your project has two languages: en, fr and deploy to different domains.

- en.example.com
- fr.example.com

Same source code but has different language for display. If you're using Twig Translations to translate like

```jinja2
{{ 'Strangle Things'|trans }}

{{ 'Hello %name%'|trans({'%name%': 'Walter'}) }}
```

then when your project create cached files for twig, it will become:

```php
echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Strangle Things"), "html", null, true);

echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans("Hello %name%", ["%name%" => "Walter"]), "html", null, true);
```

in twig cached files, so every `trans` will take time to translate every time cached files called, it does not depend on your environment(production or development). This causes huge performance when compare with your one language version(plain text only).

This bundle will print plain text to twig cached files. Translate process while creating cached files.

```php
echo "Strangle Things";

echo strtr('Hello %name%', ["%name%" => "Walter"]);
```

Hence, this process improves a lot of performance.

## Introduction

This Bundle extends [Twig Translations](https://symfony.com/doc/current/translation.html) and provides the following features:

- Print plain translated text into twig cache files(instead of extension functions)
- Translate text with twig tags, filters, and functions.

## Installation

### Composer

Run command:
`composer require "darkcat/twig-translation-bundle"`

Or add to `composer.json` in your project to `require` section:

```json
{
  "darkcat/twig-translation-bundle": "1.0.*"
}
```

and run command:
`php composer.phar update`

### Add this bundle to your application's kernel

> In a default Symfony application that uses Symfony Flex, bundles are enabled/disabled automatically for you when installing/removing them, so you don't need to look at or edit this bundles.php file.

```php
// config/bundles.php
return [
    // ...
    DarkCat\TwigTranslationBundle\TwigTranslationBundle::class => ['all' => true],
];
```

## Using Translation in Templates

### Use Twig Tags

```jinja2
{% statictrans with {'%name%': 'Jesse'} from 'messsages' %}Hello %name%{% endstatictrans %}

{% statictrans with {'%name%': 'Jesse'} from 'messsages' into 'fr' %}Hello %name%{% endstatictrans %}
```

### Use Twig Filters

The `t` and `statictrans` filters can be used to translate variable texts and complex expressions:

```jinja2
{{ message|statictrans(params, domain, locale, count) }}

{{ 'Strangle Things'|t }}
{{ 'Strangle Things'|statictrans }}
{{ 'Hello %name%'|statictrans({'%name%': 'Walter White'}, 'messsages') }}
```

### Use Twig Functions

```jinja2
{{ statictrans(message, params, domain, locale, count) }}

{{ t('Strangle Things') }}
{{ statictrans('Strangle Things') }}
{{ statictrans('Hello %name%', {'%name%': 'Walter White'}) }}
```
