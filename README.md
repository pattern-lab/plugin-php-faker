![license](https://img.shields.io/github/license/pattern-lab/plugin-php-faker.svg?maxAge=2592000)
[![Packagist](https://img.shields.io/packagist/v/pattern-lab/plugin-faker.svg?maxAge=2592000)](https://packagist.org/packages/pattern-lab/plugin-faker) [![Gitter](https://img.shields.io/gitter/room/pattern-lab/php.svg?maxAge=2592000)](https://gitter.im/pattern-lab/php)

# Faker Plugin for Pattern Lab

The Faker Plugin adds [Faker](https://github.com/fzaninotto/Faker) support to Pattern Lab. The Faker Plugin will create unique content each time Pattern Lab is generated. You can also use the Faker Plugin to provide locale appropriate content.

## Installation

To add the Faker Plugin to your project using [Composer](https://getcomposer.org/) type:

    composer require pattern-lab/plugin-faker

See Packagist for [information on the latest release](https://packagist.org/packages/pattern-lab/plugin-faker).

## Usage

You can create fake data in your `json` or `yml` data files by using this format:

    "key": "Faker.[formatter]([options])"

If a formatter has no options or you want to use the formatter's default options you can use the following format:

    "key": "Faker.[formatter]"

See below for a list of formatters and their options.

## Example

To create a random first name without regard to gender you can add the following to your `json` or `yml` data files:

    "firstName": "Faker.firstName"

To specify a gender you would use:

    "firstName": "Faker.firstName('female')"

## Formatters

The Faker Plugin supports the following content formatters:

* Faker\Provider\en_US\Person
* Faker\Provider\en_US\Address
* Faker\Provider\en_US\PhoneNumber
* Faker\Provider\en_US\Company
* Faker\Provider\Lorem
* Faker\Provider\Internet
* Faker\Provider\Color
* Faker\Provider\Payment
* Faker\Provider\DateTime
* Faker\Provider\Image
* Faker\Provider\Miscellaneous

See the official repository for a [list of options available to each formatter](https://github.com/fzaninotto/Faker#formatters).

## Locales

The content produced by Faker can be localized based on a Faker locale. If the Faker locale isn't supported by a formatter the content will fall back to the default `en_US`. There is a [list of Faker locales](https://github.com/fzaninotto/Faker/tree/master/src/Faker/Provider).

To update your Faker locale you can either directly edit `./config/config.yml` or use the command line option:

    php core/console --config --set faker.locale=[locale]

For example:

    php core/console --config --set faker.locale=fr_FR

## Disabling the Plugin

To disable the Faker plugin you can either directly edit `./config/config.yml` or use the command line option:

    php core/console --config --set faker.on=false
