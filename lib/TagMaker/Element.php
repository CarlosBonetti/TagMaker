<?php

namespace TagMaker;

/**
 * The element class
 */
class Element {

  /**
   * The tag element. Examples: a, p, ul, li
   */
  private $tag;

  /**
   * The tag content. Example: <a href="">The content goes here</a>
   */
  private $content = null;

  /**
   * Tag attributes like key="value"
   */
  private $attributes = array();

  /**
   * Used to flag self-closing tag elements, like <br /> or <input />
   */
  private $empty_tag = false;

  /**
   * Default self-closing tags. $empty_tag flag will be setted true at creation time of these tags
   */
  private static $DEFAULT_EMPTY_TAGS = array(
    'area',
    'base',
    'basefont',
    'br',
    'hr',
    'input',
    'img',
    'link',
    'meta',
  );

  /**
   * Constructs a new element.
   * @param String $tag
   * @param Array $attributes
   */
  public function __construct($tag, $content = '', $attributes = array()) {
    $this->set_tag($tag);
    $this->set_content($content);
    $this->set_attributes($attributes);
  }

  public function __call($method_name, $arguments) {
    // Set attribute magic method
    if (substr($method_name, 0, 4) === 'set_') {
      $attribute = substr($method_name, 4);
      return $this->set_attribute($attribute, $arguments[0]);
    }

    // Get attribute magic method
    if (substr($method_name, 0, 4) === 'get_') {
      $attribute = substr($method_name, 4);
      return $this->get_attribute($attribute);
    }

    // Append attribute magic method
    if (substr($method_name, 0, 7) === 'append_') {
      $attribute = substr($method_name, 7);
      return $this->append_attribute($attribute, $arguments[0]);
    }

    // Prepend attribute magic method
    if (substr($method_name, 0, 8) === 'prepend_') {
      $attribute = substr($method_name, 8);
      return $this->prepend_attribute($attribute, $arguments[0]);
    }

    throw new \Exception("The method '{$method_name}' does not exist");
  }

  /**
   * Get magic method. Creates a shortcut to get_ methods. Using $this->attributes has the same effect of $this->get_attributes()
   */
  public function __get($name) {
    if (!isset($this->{$name}))
      throw new UndefinedAttributeException("The attribute '{$name}' does not exist. Use get_{$name}() or get_attribute('{$name}') if you are trying to access a tag attribute");

    return call_user_method("get_{$name}", $this);
  }

  /**
   * Set magic method. Creates a shortcut to set_ methods. Using $this->tag = 'a' has the same effect of $this->set_tag('a')
   */
  public function __set($name, $value) {
    if (!isset($this->{$name}))
      throw new UndefinedAttributeException("The attribute '{$name}' does not exist. Use set_{$name}('{$value}') or set_attribute('{$name}', '{$value}') if you are trying to modify a tag attribute");

    return call_user_method_array("set_{$name}", $this, array($value)); 
  }

  /**
   * Returns if the element is a empty tag
   * @return boolean
   */
  public function is_empty_tag() {
    return $this->empty_tag;
  }

  /**
   * Sets the empty_tag flag. Turns it to true with you want self-closing tags.
   * @param boolean $flag
   */
  public function set_empty_tag($flag) {
    $this->empty_tag = (boolean) $flag;
  }

  /**
   * Sets the tag of the element
   * @param $tag string not blank
   * @throws BlankTagException
   */
  public function set_tag($tag) {
    if (empty($tag))
      throw new BlankTagException("Tag must not be null");

    $this->set_empty_tag(false);
    if (in_array(strtolower($tag), self::$DEFAULT_EMPTY_TAGS))
      $this->set_empty_tag(true);

    $this->tag = $tag;
  }

  /**
   * Returns the tag of the element
   * @return string $tag
   */
  public function get_tag() {
    return $this->tag;
  }

  /**
   * Sets the tag content (won't be render if element is a empty tag element, like <br />)
   * @param String $content
   */
  public function set_content($content) {
    $this->content = $content;
  }

  /**
   * Returns the content of the tag element
   * @return String
   */
  public function get_content() {
    return $this->content;
  }

  /**
   * Returns a copy of the element
   * @return Element
   */
  public function copy() {
    $new = new Element($this->tag, $this->get_content(), $this->get_attributes());

    if ($this->is_empty_tag())
      $new->set_empty_tag();
    
    return $new;    
  }

  /**
   * Allow usage of $element->copy() like: $copy = clone $obj;
   */
  public function __clone() {
    return $this->copy();
  }

  // ====================================================================
  // Attributes methods

  /**
   * Sets the element attributes, overriding the old ones
   * @param $attributes Array of attributes
   */
  public function set_attributes(array $attributes) {
    $this->attributes = $attributes;
  }

  /**
   * Merges the actual attributes with the parameter, overriding the old ones in case of duplicates
   * @param Array $attributes Attributes to be merged
   */
  public function merge_attributes(array $attributes) {
    foreach($attributes as $key => $value)
      $this->set_attribute($key, $value);
  }

  /**
   * Gets the element attributes
   * @return array
   */
  public function get_attributes() {
    return $this->attributes;
  }

  /**
   * Returns if the attribute is isolated. Example: <input checked />. False if attribute does not exists
   * @param String $key
   * @return boolean
   */
  public function is_isolated_attribute($key) {
    return gettype(array_search($key, $this->attributes)) == 'integer';
  }

  /**
   * Returns if the attributed is valued. Example <input checked="checked" />. False if attribute does not exists
   * @param String $key
   * @return boolean
   */
  public function is_valued_attribute($key) {
    return array_key_exists($key, $this->attributes);
  }

  /**
   * Checks if an attribute exists in the element
   * @param String $key
   * @return boolean
   */
  public function attribute_exists($key) {
    return $this->is_valued_attribute($key) || $this->is_isolated_attribute($key);
  }

  /**
   * Gets the value of an attribute.
   * @param String $key
   * @param Boolean $force Pass true to get false instead of an exception if the attribute does not exists
   * @return String
   * @throws UndefinedAttributeException
   */
  public function get_attribute($key, $force = false) {
    if (!$this->attribute_exists($key) && !$force)
      throw new UndefinedAttributeException("The attribute '{$key}' does not exist in the element");

    if (!$this->attribute_exists($key))
      return false;

    if (gettype(array_search($key, $this->attributes)) == 'integer')
      return true;

    return $this->attributes[$key];
  }

  /**
   * Adds an attribute to the element, overriding it if already exists
   * @param String $key
   * @param String $value
   */
  public function set_attribute($key, $value = null) {
    if ($value === null)
      $this->attributes[] = $key;
    else
      $this->attributes[$key] = $value;
  }

  /**
   * Adds an attribute to the element, throwing and exception if it already exists. For the override method, use set_attribute()
   * @param String $key
   * @param String $value
   * @throws ExistentAttributeException
   */
  public function add_attribute($key, $value = null) {
    if ($this->attribute_exists($key))
      throw new ExistentAttributeException("The '{$key}' attribute already exists in the element");

    $this->set_attribute($key, $value);
  }  

  /**
   * Removes all the element attributes
   */
  public function clear_attributes() {
    $this->attributes = array();
  }

  /**
   * Removes the attribute
   * @param String $key
   */ 
  public function remove_attribute($key) {
    if ($this->is_isolated_attribute($key))
      unset($this->attributes[array_search($key, $this->attributes)]);
    else
      unset($this->attributes[$key]);
  }

  /**
   * Adds a value to the end of an attribute. Example: append_attribute('class', 'btn') to <a class="link"></a> will transform it to <a class="link btn"></a>
   * @param String $key
   * @param String $value
   */
  public function append_attribute($key, $value) {
    if ($this->is_isolated_attribute($key)) {
      $this->remove_attribute($key);
      $this->set_attribute($key, $value);
    } else if ($this->is_valued_attribute($key, $value))
      $this->set_attribute($key, $this->get_attribute($key) . ' ' . $value);
    else
      $this->set_attribute($key, $value);
  }

  /**
   * Adds a value to the beggining of an attribute. Example: append_attribute('class', 'btn') to <a class="link"></a> will transform it to <a class="btn link"></a>
   * @param String $key
   * @param String $value
   */
  public function prepend_attribute($key, $value) {
    if ($this->is_isolated_attribute($key)) {
      $this->remove_attribute($key);
      $this->set_attribute($key, $value);
    } else if ($this->is_valued_attribute($key, $value))
      $this->set_attribute($key, $value . ' ' . $this->get_attribute($key));
    else
      $this->set_attribute($key, $value);
  }

  // ====================================================================
  // Render methods

  /**
   * Renders the element
   * @return String
   */
  public function __toString() {
    return $this->render();
  }

  /**
   * Renders the attributes => 'attribute="value" attribute2="value2" isolated_attribute'
   * @return String
   */
  protected function render_attributes() {
    $attrs = array();
    foreach ($this->attributes as $key => $value) {
      if (gettype($key) == 'integer')
        $attr = "{$value}";
      else
        $attr = "{$key}=\"{$value}\"";

      $attrs[] = $attr;
    }
    return implode($attrs, " ");
  }
    
  /**
   * Renders the element
   * @return String
   */
  public function render() {
    $output = "<{$this->tag}";
    $output .= !empty($this->attributes) ? " " . $this->render_attributes() : "";
    $output .= $this->is_empty_tag() ? " />" : ">{$this->content}</{$this->tag}>";

    return $output;
  }

}