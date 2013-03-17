<?php

use TagMaker\Interpreter;

class InterpreterTest extends PHPUnit_Framework_TestCase {

  public function setUp() {

  }

  public function test_extract_attributes() {
    $attrs = Interpreter::extract_attributes('');
    $this->assertEmpty($attrs);

    $attrs = Interpreter::extract_attributes(false);
    $this->assertEmpty($attrs);

    $attrs = Interpreter::extract_attributes('key1=value1,key2=value2');
    $this->assertEquals('value1', $attrs['key1']);
    $this->assertEquals('value2', $attrs['key2']);

    $attrs = Interpreter::extract_attributes('key=value');
    $this->assertEquals('value', $attrs['key']);

    $attrs = Interpreter::extract_attributes('key=value,alone,alone2,a=b');
    $this->assertEquals('value', $attrs['key']);
    $this->assertEquals('alone', $attrs[0]);
    $this->assertEquals('alone2', $attrs[1]);
    $this->assertEquals('b', $attrs['a']);
  }

  /**
   * @expectedException TagMaker\InvalidRuleException
   */
  public function test_invalid_extract_attributes() {
    Interpreter::extract_attributes('key=v=alue');
  }

  /**
   * @dataProvider valid_rule_provider
   */
  public function test_valid_element_rules($valid_rule) {
    Interpreter::element_rule($valid_rule);
  }

  public function valid_rule_provider() {
    return array(
      array('a '),
      array('ul#main'),
      array('li.active'),
      array('a.free-link'),
      array('div.span1.span3'),
      array('div.valid_class'),
      array('div#valid_id'),      
      array('form[name=new]'),
    );
  }

  public function test_extract_tag() {
    $this->assertEquals('div', Interpreter::extract_tag(''));
    $this->assertEquals('a', Interpreter::extract_tag('a'));
    $this->assertEquals('div', Interpreter::extract_tag('.post#test'));
    $this->assertEquals('section', Interpreter::extract_tag('section'));
    $this->assertEquals('article', Interpreter::extract_tag('article#post'));
    $this->assertEquals('tag-with-dash', Interpreter::extract_tag('tag-with-dash.post'));
    $this->assertEquals('tag_with_underscore', Interpreter::extract_tag('tag_with_underscore#post'));
    $this->assertEquals('ul', Interpreter::extract_tag('   ul   '));
  }

  public function test_extract_id() {
    $this->assertEquals('id', Interpreter::extract_id('#id'));
    $this->assertEquals('id', Interpreter::extract_id('a.class#id'));
    $this->assertEquals('id-dash', Interpreter::extract_id('article.class#id-dash.post'));
    $this->assertEquals('id_underscore', Interpreter::extract_id('article.class#id_underscore.post'));
  }

  public function test_extract_classes() {
    $this->assertEquals(array('class'), Interpreter::extract_classes('.class'));
    $this->assertEquals(array('main'), Interpreter::extract_classes('div.main'));
    $this->assertEquals(array('class-dash'), Interpreter::extract_classes('div.class-dash'));
    $this->assertEquals(array('class_underscore'), Interpreter::extract_classes('div.class_underscore'));
    $this->assertEquals(array('class1', 'class2', 'class3'), Interpreter::extract_classes('li.class1.class2#id.class3'));
  }

  public function test_element_rule() {
    $element = Interpreter::element_rule('section');
    $this->assertEquals('section', $element["tag"]);

    $element = Interpreter::element_rule('.content');
    $this->assertEquals('div', $element["tag"]);

    $this->assertEquals('content', $element["attributes"]["class"]);

    $element = Interpreter::element_rule('#main');
    $this->assertEquals('main', $element["attributes"]["id"]);

    $element = Interpreter::element_rule('body.content.span12.box');
    $this->assertEquals('content span12 box', $element["attributes"]["class"]);
    $this->assertEquals('body', $element["tag"]);

    $element = Interpreter::element_rule('form.form-inline.small[name=new]#new');
    $this->assertEquals('form-inline small', $element["attributes"]["class"]);
    $this->assertEquals('new', $element["attributes"]["id"]);
    $this->assertEquals('new', $element["attributes"]["name"]);
    $this->assertEquals('form', $element["tag"]);
  }

}