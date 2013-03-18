<?php

use TagMaker\Element;

class ElementTest extends PHPUnit_Framework_TestCase {
    
  public function setUp() {
    $this->div = new Element('div', '', array(
      'class' => 'box',
      'name' => 'header',
      'touched'
    ));
    $this->body = new Element('body');
    $this->li[0] = new Element('li');
    $this->li[1] = new Element('li');
  }

  /**
   * @expectedException TagMaker\BlankTagException
   */
  public function test_empty_tag() {
    $element = new Element("");
  }

  /**
   * @expectedException TagMaker\BlankTagException
   */
  public function test_null_tag() {
    $element = new Element(null);
  }

  public function test_set_and_get_tag() {
    $tag = 'p';
    $element = new Element($tag);
    $this->assertEquals($tag, $element->get_tag());

    $tag2 = 'li';
    $element->set_tag($tag2);
    $this->assertEquals($tag2, $element->get_tag());
  }

  public function test_set_and_get_content() {
    $element = new Element('p', 'Lorem ipsum');
    $this->assertEquals('Lorem ipsum', $element->get_content());
    $element->set_content('Another content');
    $this->assertEquals('Another content', $element->get_content());
  }

  public function test_set_attributes_at_construct() {
    $element = new Element('section', '', array('class' => 'post', 'id' => 'post-section'));

    $this->assertEquals($element->get_attribute('class'), 'post');
    $this->assertEquals($element->get_attribute('id'), 'post-section');
  }

  public function test_set_and_get_attributes() {
    $attributes = array('class' => 'active', 'target' => '_blank');
    $element = new Element('a', '', $attributes);
    $this->assertEquals($attributes, $element->get_attributes());

    $attributes = array_merge($attributes, array('another', 'attr'));
    $element->set_attributes($attributes);
    $this->assertEquals($attributes, $element->get_attributes());
  }

  public function test_set_and_get_empty_tag() {
    $element = new Element('div');
    $this->assertFalse($element->is_empty_tag());
    $element->set_empty_tag(true);
    $this->assertTrue($element->is_empty_tag());
  }

  public function test_default_empty_tag() {
    $element = new Element('BR');
    $this->assertTrue($element->is_empty_tag());
    $element2 = new Element('inPut');
    $this->assertTrue($element2->is_empty_tag());
  }

  public function test_copy_element() {
    $new_div = $this->div->copy();
    $this->assertEquals($new_div, $this->div);
    $new_div->clear_attributes();
    $this->assertEmpty($new_div->get_attributes());
    $this->assertEquals($this->div->get_class(), 'box');
  }

  public function test_clone() {
    $new_div = clone $this->div;
    $this->assertEquals($new_div, $this->div);
  } 

  // ========================================================================
  // Attributes methods test

  public function test_attribute_exists() {
    $element = $this->div;

    $this->assertTrue($element->attribute_exists('class'));
    $this->assertFalse($element->attribute_exists('id'));
    $this->assertFalse($element->attribute_exists('header'));
    $this->assertTrue($element->attribute_exists('touched'));
  }


  public function test_merge_attributes() {
    $element = new Element('a', '', array('class' => 'btn', 'name' => 'title'));
    $element->merge_attributes(array('class' => 'link', 'href' => '#'));
    $this->assertEquals('link', $element->get_class());
    $this->assertEquals('title', $element->get_name());
    $this->assertEquals('#', $element->get_href());
  }

  /**
   * @expectedException TagMaker\UndefinedAttributeException
   */
  public function test_get_attribute() {
    $element = $this->div;

    $this->assertEquals('box', $element->get_attribute('class'));
    $this->assertTrue($element->get_attribute('touched'));
    $this->assertFalse($element->get_attribute('id', true));
    $element->get_attribute('id'); // Throws the exception
  }

  public function test_set_attribute() {
    $this->div->set_attribute('class', 'box-large');
    $this->assertEquals('box-large', $this->div->get_attribute('class'));    
  }

  /**
   * @expectedException TagMaker\ExistentAttributeException
   */
  public function test_add_attribute() {
    $this->div->add_attribute('onclick', 'alert("Hello")');
    $this->assertEquals($this->div->get_attribute('onclick'), 'alert("Hello")');
    $this->div->add_attribute('class', 'container');
  }

  public function test_clear_attributes() {
    $this->div->clear_attributes();
    $this->assertEmpty($this->div->get_attributes());
  }

  /**
   * @expectedException TagMaker\UndefinedAttributeException
   */
  public function test_remove_attribute() {
    $element = new Element('a', '', array('class' => 'link'));
    $element->remove_attribute('class');
    $this->assertEmpty($element->get_attributes());

    $element->remove_attribute('not_a_attr');

    $element = new Element('a', '', array('class' => 'link', 'touched'));
    $element->remove_attribute('touched');
    $this->assertEquals($element->get_attribute('class'), 'link');
    $element->get_attribute('touched'); // Expected exception
  }

  public function test_append_attribute() {
    // Existent attribute
    $element = new Element('a', '', array('class' => 'link'));
    $element->append_attribute('class', 'btn');
    $this->assertEquals($element->get_attribute('class'), 'link btn');

    // Isolated attribute
    $element = new Element('input', '', array('checked'));
    $element->append_attribute('checked', 'checked');
    $this->assertEquals($element->get_attribute('checked'), 'checked');

    // Non-existent attribute
    $element = new Element('input');
    $element->append_attribute('checked', 'checked');
    $this->assertEquals($element->get_attribute('checked'), 'checked');
  }

  public function test_prepend_attribute() {
    $element = new Element('a', '', array('class' => 'link'));
    $element->prepend_attribute('class', 'btn');
    $this->assertEquals($element->get_attribute('class'), 'btn link');

    // Isolated attribute
    $element = new Element('input', '', array('checked'));
    $element->prepend_attribute('checked', 'checked');
    $this->assertEquals($element->get_attribute('checked'), 'checked');

    // Non-existent attribute
    $element = new Element('input');
    $element->prepend_attribute('checked', 'checked');
    $this->assertEquals($element->get_attribute('checked'), 'checked');
  }  

  public function test_return_itself() {
    $element = new Element('section');
    $element->set_empty_tag(false)
            ->set_tag('div')
            ->set_content('Content here')
            ->set_attributes(array('class' => 'main'))
            ->merge_attributes(array('id' => 'content'))
            ->set_name('main-div')
            ->clear_attributes()
            ->add_attribute('title', 'Title here')
            ->remove_attribute('title')
            ->prepend_class('row')
            ->append_class('row-large')
            ->render();
  }

  // ========================================================================
  // Render tests

  public function test_render() {
    $this->assertEquals($this->div->render(), '<div class="box" name="header" touched></div>');

    $element = new Element('p', 'Lorem ipsum.');
    $this->assertEquals($element->render(), '<p>Lorem ipsum.</p>');

    $element = new Element('br', 'Lorem ipsum.');
    $this->assertEquals($element->render(), '<br />');

    $element = new Element('a', 'Link', array('class' => 'btn'));
    $this->assertEquals($element->render(), '<a class="btn">Link</a>');
  }

  // ========================================================================
  // Magic methods tests

  /**
   * @expectedException TagMaker\UndefinedAttributeException
   */
  public function test_get_magic_method() {
    $this->assertEquals($this->div->tag, $this->div->get_tag());
    $this->assertEquals($this->div->content, $this->div->get_content());
    $this->assertEquals($this->div->attributes, $this->div->get_attributes());

    $this->div->undefined; // Exception here
  }

  /**
   * @expectedException TagMaker\UndefinedAttributeException
   */
  public function test_set_magic_method() {
    $this->div->tag = 'body';
    $this->assertEquals($this->div->get_tag(), 'body');

    $this->div->content = 'Lorem ipsum.';
    $this->assertEquals($this->div->get_content(), 'Lorem ipsum.');

    $this->div->attributes = array('class' => 'container');
    $this->assertEquals($this->div->get_attribute('class'), 'container');
    $this->assertEquals('container', $this->div->attributes["class"]);

    $this->div->undefined = ""; // Exception here
  }

  public function test_set_attribute_magic_method() {
    $this->body->set_name('container');
    $this->assertEquals($this->body->get_attribute('name'), 'container');
    $this->body->set_name('main');
    $this->assertEquals($this->body->get_attribute('name'), 'main');
  }

  public function test_append_attribute_magic_method() {
    $this->body->append_class('container');
    $this->assertEquals($this->body->get_attribute('class'), 'container');
    $this->body->append_class('main');
    $this->assertEquals($this->body->get_attribute('class'), 'container main');
  }

  public function test_prepend_attribute_magic_method() {
    $this->body->prepend_id('container');
    $this->assertEquals($this->body->get_attribute('id'), 'container');
    $this->body->prepend_id('main');
    $this->assertEquals($this->body->get_attribute('id'), 'main container');
  }

  public function test_get_attribute_magic_method() {
    $this->div->set_class('box');
    $this->assertEquals($this->div->get_class(), 'box');
  }
  
  public function test_add_attribute_magic_method() {
    $element = new Element('div');
    $element->add_class('row');
    $this->assertEquals($element->get_class(), 'row');
  }

  /**
   * @expectedException TagMaker\ExistentAttributeException
   */
  public function test_add_attribute_magic_method_exception() {
    $element = new Element('div');
    $element->add_class('row');
    $element->add_class('row-large'); 
  }

}