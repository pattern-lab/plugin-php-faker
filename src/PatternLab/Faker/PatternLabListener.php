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
    $this->addListener("patternData.dataLoaded","fakeContent");
    
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
  * Fake some content
  */
  public function fakeContent() {
    
    $foo = $this->recursiveWalk(Data::get());
    print_r($foo);
    
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
