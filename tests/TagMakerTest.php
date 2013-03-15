<?php

use TagMaker\TagMaker;

class TagMakerTest extends PHPUnit_Framework_TestCase {

  public function setUp() {

  }

  public function test_create_element() {
    $element = TagMaker::create('a');
    $this->assertEquals('a', $element->tag);

    $element = TagMaker::create('.content');
    $this->assertEquals('div', $element->tag);
    $this->assertEquals('content', $element->get_class());

    $element = TagMaker::create('#main');
    $this->assertEquals('div', $element->tag);
    $this->assertEquals('main', $element->get_id());

    $element = TagMaker::create('p.content.subtitle');
    $this->assertEquals('p', $element->tag);
    $this->assertEquals('content subtitle', $element->get_class());

    $element = TagMaker::create('p', 'text here');
    $this->assertEquals('<p>text here</p>', $element->render());
  }

  public function test_create_element_with_attributes() {
    $element = TagMaker::create('a.btn#link', 'Click here', array(
      'class' => 'large-link',
      'href' => '#'
    ));
    $this->assertEquals('large-link', $element->get_class());
    $this->assertEquals('link', $element->get_id());
    $this->assertEquals('#', $element->get_href());
  }

}