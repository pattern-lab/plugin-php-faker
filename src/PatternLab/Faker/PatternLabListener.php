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
use \PatternLab\PatternEngine\Twig\TwigUtil;

class PatternLabListener extends \PatternLab\Listener {
  
  /**
  * Add the listeners for this plug-in
  */
  public function __construct() {
    
    // add listener
    $this->addListener("twigPatternLoader.customize","fakeContent");
    
    // set-up locale
    $locale = Config::getOption("faker.locale");
    $locale = ($locale) ? $locale : "en_US";
    
    // set-up Faker
    $this->faker = \Faker\Factory::create($locale);
    $this->faker->addProvider(new \Faker\Provider\Color($faker));
    $this->faker->addProvider(new \Faker\Provider\Payment($faker));
    $this->faker->addProvider(new \Faker\Provider\DateTime($faker));
    $this->faker->addProvider(new \Faker\Provider\Image($faker));
    
  }
  
  /**
  * Go through data and replace any values that match items from the link.array
  * @param  {String}       a string entry from the data to check for link.pattern
  *
  * @return {String}       replaced version of link.pattern
  */
  private function compareReplaceFaker($value) {
    if (is_string($value) && preg_match("/^Faker\.([A-z]+)(\((\'|\")?([A-z]+)?(\'|\")?\))?$/",$value,$matches)) {
      $formatter = $matches[1];
      $options   = isset($matches[5]) ? $matches[5] : "";
      if ($options != "") {
        return $this->formatOptionsAndFake($formatter, $options);
      } else {
        return $this->faker->$formatter;
      }
    }
    return $value;
  }
  
  /**
  * Read in the data and process faker data
  */
  public function fakeContent() {
    
    $foo = $this->recursiveWalk(Data::get());
    print_r($foo);
    
  }
  
  /**
  * Read in the data and process faker data
  */
  public function formatOptionsAndFake($formatter, $options) {
    
    if (($formatter == "date") || ($formatter == "time")) {
      return $this->faker->$formatter($options);
    } else {
      $options = explode(",", $options);
      if (count($options) === 6) {
        return $this->faker->$formatter($options[0],$options[1],$options[2],$options[3],$options[4],$options[5]);
      } else if (count($options) === 5) {
        return $this->faker->$formatter($options[0],$options[1],$options[2],$options[3],$options[4]);
      } else if (count($options) === 4) {
        return $this->faker->$formatter($options[0],$options[1],$options[2],$options[3]);
      } else if (count($options) === 3) {
        return $this->faker->$formatter($options[0],$options[1],$options[2]);
      } else if (count($options) === 2) {
        return $this->faker->$formatter($options[0],$options[1]);
      } else {
        return $this->faker->$formatter($options[0]);
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
