<?php

namespace TagMaker;

class TagMaker {

  /**
   * Creates a single element
   * @param String element_rule
   * @param String Text inside the tags (optional)
   * @param Array $attributes. Will override attributes passed at the element rule
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

}