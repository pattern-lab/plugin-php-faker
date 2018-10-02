<?php

/*!
 * Faker Listener Class
 *
 * Copyright (c) 2016 Dave Olsen, http://dmolsen.com
 * Licensed under the MIT license
 *
 * Adds Faker support to Pattern Lab
 *
 */

namespace PatternLab\Faker;

use \PatternLab\Config;
use \PatternLab\Console;
use \PatternLab\Data;
use \PatternLab\PatternEngine\Twig\TwigUtil;

class PatternLabListener extends \PatternLab\Listener {
  
  protected $faker;
  protected $locale;
  
  /**
  * Add the listeners for this plug-in
  */
  public function __construct() {
    
    // add listener
    $this->addListener("patternData.dataLoaded","fakeContent");
    
    // set-up locale
    $locale = Config::getOption("plugins.faker.locale");
    $locale = ($locale) ? $locale : "en_US";
    $this->locale = $locale;
    
    // set-up time zone if not already set to prevent errors in PHP 5.4+
    if (!ini_get('date.timezone')) {
      date_default_timezone_set('UTC');
    }
    
    // set-up Faker
    $this->faker = \Faker\Factory::create($locale);
    $this->faker->addProvider(new \Faker\Provider\Color($this->faker));
    $this->faker->addProvider(new \Faker\Provider\Payment($this->faker));
    $this->faker->addProvider(new \Faker\Provider\DateTime($this->faker));
    $this->faker->addProvider(new \Faker\Provider\Image($this->faker));
    $this->faker->addProvider(new \Faker\Provider\Miscellaneous($this->faker));
    
  }
  
  /**
  * Clean the passed option
  * @param  {String}       the option to be cleaned
  *
  * @return {String}       the cleaned option
  */
  private function clean($option) {
    $option = trim($option);
    $option = (($option[0] == '"') || ($option[0] == "'")) ? substr($option, 1) : $option;
    $option = (($option[strlen($option)-1] == '"') || ($option[strlen($option)-1] == "'")) ? substr($option, 0, -1) : $option;
    return $option;
  }
  
  /**
  * Go through data and replace any values that match items from the link.array
  * @param  {String}       a string entry from the data to check for link.pattern
  *
  * @return {String}       replaced version of link.pattern
  */
  private function compareReplaceFaker($value) {
    if (is_string($value) && (strpos($value,"Faker.") === 0)) {
      preg_match("/^Faker\.([A-z]+)(\(('|\")?(.*)?('|\")?\))?$/",$value,$matches);
      $formatter = $matches[1];
      $options   = isset($matches[4]) ? $matches[4] : "";
      if ($options != "") {
        return $this->formatOptionsAndFake($formatter, $options);
      } else {
        try {
          return $this->faker->$formatter;
        } catch (\InvalidArgumentException $e) {
          Console::writeWarning("Faker plugin error: ".$e->getMessage()."...");
          return $value;
        }
      }
    }
    return $value;
  }
  
  /**
  * Fake some content. Replace the entire store.
  */
  public function fakeContent() {
    
    if ((bool)Config::getOption("plugins.faker.enabled")) {
      $fakedContent = $this->recursiveWalk(Data::get());
      Data::replaceStore($fakedContent);
    }
    
  }
  
  /**
  * Format the options and fake out the data
  * @param  {String}       the name of the formatter
  * @param  {String}       a string of options. separated by commas if appropriate
  *
  * @return {String}       the formatted text
  */
  public function formatOptionsAndFake($formatter, $options) {
    
    if (($formatter == "date") || ($formatter == "time")) {
      
      // don't try to parse date or time options. cross our fingers
      return $this->faker->$formatter($options);
      
    } else {
      
      // get explodey
      $options = explode(",", $options);
      $count   = count($options);
      
      // clean up the options
      $option0 = $this->clean($options[0]);
      $option1 = isset($options[1]) ? $this->clean($options[1]) : "";
      $option2 = isset($options[2]) ? $this->clean($options[2]) : "";
      $option3 = isset($options[3]) ? $this->clean($options[3]) : "";
      $option4 = isset($options[4]) ? $this->clean($options[4]) : "";
      $option5 = isset($options[5]) ? $this->clean($options[5]) : "";
      $option6 = isset($options[6]) ? $this->clean($options[6]) : "";
      
      // probably should have used a switch. i'm lazy
      try {
        if ($count === 6) {
          return $this->faker->$formatter($option0,$option1,$option2,$option3,$option4,$option5);
        } else if ($count === 5) {
          return $this->faker->$formatter($option0,$option1,$option2,$option3,$option4);
        } else if ($count === 4) {
          return $this->faker->$formatter($option0,$option1,$option2,$option3);
        } else if ($count === 3) {
          return $this->faker->$formatter($option0,$option1,$option2);
        } else if ($count === 2) {
          return $this->faker->$formatter($option0,$option1);
        } else {
          return $this->faker->$formatter($option0);
        }
      } catch (\InvalidArgumentException $e) {
        Console::writeWarning("Faker plugin error: ".$e->getMessage()."...");
      }
      
    }
    
  }
  
  /**
  * Work through a given array and decide if the walk should continue or if we should replace the var
  * @param  {Array}       the array to be checked
  *
  * @return {Array}       the "fixed" array
  */
  private function recursiveWalk($array) {
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = self::recursiveWalk($v);
        } else {
            $array[$k] = self::compareReplaceFaker($v);
        }
    }
    return $array;
  }
}
