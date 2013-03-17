<?php

namespace TagMaker;

/**
 * Used to interpret formation rules
 */
class Interpreter {

  /**
   * Returns a hash of attributes, receiving them typed like 'key=value1,single_attr,key2=value2'
   * @param String Attributes
   * @return Array
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
   * Normalizes a rule with some filters
   * @param String Rule
   * @return String Rule normalized
   */
  protected static function normalize_rule($rule) {
    return trim($rule);
  }

  /**
   * Extracts a tag from an element rule
   * @param String Element rule
   * @return String Tag name
   */
  public static function extract_tag($rule) {
    $rule = static::normalize_rule($rule);
    preg_match('/(?<tag>[\w-]*)/', $rule, $matches);
    $tag = !empty($matches["tag"]) ? $matches["tag"] : 'div';
    return $tag;
  }

  /**
   * Extracts the id from an element rule
   * @param String Element rule
   * @return String Id
   */
  public static function extract_id($rule) {
    $rule = static::normalize_rule($rule);
    preg_match('/#(?<id>[\w-]*)/', $rule, $matches);
    $id = isset($matches["id"]) ? $matches["id"] : null;
    return $id;    
  }

  /**
   * Extracts the classes from an element rule
   * @param String Element rule
   * @return Array Array with the classes names
   */
  public static function extract_classes($rule) {
    $rule = static::normalize_rule($rule);
    preg_match_all('/\.(?<classes>[\w-]*)/', $rule, $matches);
    return $matches["classes"];
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
    $classes = static::extract_classes($rule);
    if (!empty($classes))
      $element["attributes"]["class"] = implode(' ', $classes);

    // Getting the id '#'
    $id = static::extract_id($rule);
    if ($id)
      $element["attributes"]["id"] = $id;    

    // Getting the tag
    $element["tag"] = static::extract_tag($rule);

    return $element;
  }

}