# Faker Plugin for Pattern Lab

The Faker Plugin adds support for the [Faker Library](https://github.com/fzaninotto/Faker) to Pattern Lab.

## Installation

Pattern Lab PHP uses [Composer](https://getcomposer.org/) to manage project dependencies with Pattern Lab Editions. To add the Faker Plugin to the dependencies list for your Edition you can type the following in the command line at the base of your project:

    composer require pattern-lab/plugin-faker

See Packagist for [information on the latest release](https://packagist.org/packages/pattern-lab/plugin-faker).

## Usage

To use this in your project set the value for a data option to:

```
"key": "Faker.[formatter]([options])",
```

For example, to have a random first name use:

```
"firstName": "Faker.firstName('female')",
```

Using Mustache as an example you'd use:

```
First Name: {{ firstName }}
```

would output:

```
First Name: Mary
```
