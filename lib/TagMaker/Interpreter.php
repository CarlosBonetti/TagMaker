<?php

namespace TagMaker;

/**
 * Used to interpret formation rules
 */
class Interpreter {

  /**
   * Returns a hash of attributes, receiving them typed like 'key=value1,single_attr,key2=value2'
   */
  public static function extract_attributes($attributes) {
    if (!$attributes || empty($attributes))
      return array();

    $equals = explode(',', $attributes);
    foreach($equals as $e) {
      $parts = explode('=', $e);
      if (count($parts) > 2)
        throw new InvalidRuleException("There is an invalid rule near of \"{$e}\"");

      if (count($parts) == 2)
        $res[$parts[0]] = $parts[1];
      elseif (count($parts == 1)) {
        $res[] = $parts[0];
      }
    }

    return $res;
  }

  /**
   * Interprets an element rule
   * Element rules are used to create single elements.   
   * 
   * Blank tags (like '.content' or '#main') are implicitly transformed to a 'div' tag 
   * @return array Array with 'tag' => 'tagname' and attributes => array_of_attributes containing the provided attributes
   */
  public static function element_rule($rule) {
    $rule = trim($rule);

    if (empty($rule) || !$rule)
      throw new InvalidRuleException("Blank rules are not valid");

    // Getting the attributes inside brackets '[]'
    preg_match('/\[(?<attributes>.*)\]/', $rule, $matches);
    $attributes = isset($matches["attributes"]) ? $matches["attributes"] : false;
    $element["attributes"] = self::extract_attributes($attributes);

    // Getting the classes '.'
    preg_match_all('/\.(?<classes>[\w-]*)/', $rule, $matches);
    $classes = isset($matches["classes"]) ? $matches["classes"] : false;
    if ($classes)
      $element["attributes"]["class"] = implode(' ', $classes);

    // Getting the id '#'
    preg_match('/#(?<id>[\w-]*)/', $rule, $matches);
    $id = isset($matches["id"]) ? $matches["id"] : false;
    if ($id)
      $element["attributes"]["id"] = $id;    

    // Getting the tag
    preg_match('/(?<tag>[\w-]*)/', $rule, $matches);
    $tag = isset($matches["tag"]) ? $matches["tag"] : false;
    $element["tag"] = $tag ? $tag : 'div';

    return $element;
  }  

}