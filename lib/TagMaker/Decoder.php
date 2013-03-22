<?php

namespace TagMaker;

/**
 * Used to decode HTML, transforming into TagMaker elements
 */
class Decoder {  

  /**
   * Extracts the tag from a single HTML tag
   * @param String HTML
   * @return String
   */
  public static function extract_tag($html) {
    preg_match('/<([\w-]+)/', $html, $matches);
    $tag = !empty($matches[1]) ? $matches[1] : null;

    return $tag;
  }

  /**
   * Extracts the content from a single HTML tag
   * @param String HTML
   * @return String
   */
  public static function extract_content($html) {
    // Checking for pure text
    if (is_null(static::extract_tag($html)))
      return $html;

    preg_match('/<.*>(.*)<.*>/', $html, $matches);
    $content = !empty($matches[1]) ? $matches[1] : null;

    return $content;
  }

  /**
   * Extracts the attributes from a single HTML tag
   * @param String HTML
   * @return Array
   */
  public static function extract_attributes($html) {
    // Checking for pure text
    if (is_null(static::extract_tag($html)))
      return array();

    preg_match('/<([^<]*)>/', $html, $matches); // <getting all that goes here>...</...>
    $inside = trim($matches[1]);

    $parts = explode(' ', $inside);
    $parts = array_slice($parts, 1); // Excluding the first match, wich is the tag itself
    

    $attributes = array();

    foreach($parts as $part) {
      if (empty($part) || $part === '/') 
        continue;

      preg_match('/(.*)=["\'](.*)["\']/', $part, $matches);
      if (isset($matches[1]) && isset($matches[2]))
        $attributes[$matches[1]] = $matches[2];
      else
        $attributes[] = $part;
    }

    return $attributes;
  }

  /**
   * Decodes a single HTML tag, transforming it into TagMaker\Element
   * @param String HTML tag to decode
   * @return TagMaker\Element
   */
  public static function decode_element($html) {
    $tag        = static::extract_tag($html);
    $content    = static::extract_content($html);
    $attributes = static::extract_attributes($html);

    return new Element($tag, $content, $attributes);
  }
  
}