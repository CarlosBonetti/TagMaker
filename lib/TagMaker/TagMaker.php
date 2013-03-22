<?php

namespace TagMaker;

class TagMaker {

  /**
   * Creates a single element
   * @param String element_rule
   * @param String Text inside the tags (optional)
   * @param Array $attributes. Will override attributes passed at the element rule (Optional)
   * @return Element
   */
  public static function create($element_rule, $content = null, $attributes = array()) {
    $config = Interpreter::element_rule($element_rule);

    if ($content === null && !empty($config["content"]))
      $content = $config["content"];

    $element = new Element($config["tag"], $content, $config["attributes"]);
    $element->merge_attributes($attributes);
    return $element;
  }

  /**
   * Decodes a HTML string, transforming it into TagMaker\Element (Just work for single elements. Working to support multiple elements on 1.0 version)
   * @param String HTML tag to decode
   * @return TagMaker\Element
   */
  public static function decode($html) {
    return Decoder::decode_element($html);
  }

}