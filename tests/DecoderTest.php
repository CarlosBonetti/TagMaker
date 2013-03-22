<?php

use TagMaker\Decoder;

class DecoderTest extends PHPUnit_Framework_TestCase {
    
  public function setUp() {
    
  }

  public function test_extract_tag() {
    $this->assertEquals('div', Decoder::extract_tag('<div class="main">Lorem ipsum</div>'));
    $this->assertEquals('section', Decoder::extract_tag('<section>Lorem ipsum</section>'));
    $this->assertEquals('input', Decoder::extract_tag('<input type="text" />'));
    $this->assertEquals(null, Decoder::extract_tag('Lorem ipsum'));
    $this->assertEquals('94tag', Decoder::extract_tag('     <94tag>Lorem ipsum</94tag>    '));
  }

  public function test_extract_content() {
    $this->assertEquals('Lorem ipsum', Decoder::extract_content('<div class="main">Lorem ipsum</div>'));
    $this->assertEquals('', Decoder::extract_content('<a></a>'));
    $this->assertEquals(null, Decoder::extract_content('<input type="checkbox" />'));
    $this->assertEquals(null, Decoder::extract_content('<input type="text">'));
    $this->assertEquals('Pure text', Decoder::extract_content('Pure text'));
  }

  public function test_extract_attributes() {
    $this->assertEmpty(Decoder::extract_attributes('Pure text="yes"'));
    $this->assertEmpty(Decoder::extract_attributes('<   div    ></div>'));
    $this->assertEquals(array('class' => 'main', 'id' => 'content'), Decoder::extract_attributes('<div class="main" id="content">Lorem ipsum</div>'));
    $this->assertEquals(array('lonely1', 'lonely2'), Decoder::extract_attributes('<div lonely1 lonely2>Lorem ipsum</div>'));
    $this->assertEquals(array('class' => 'row', 'lonely1', 'id' => 'menu', 'lonely2'), Decoder::extract_attributes('<ul class="row" lonely1 id="menu" lonely2>Lorem ipsum</ul>'));
    $this->assertEquals(array('type' => 'checkbox', 'value' => 'Brazil', 'checked'), Decoder::extract_attributes('<input type="checkbox" value="Brazil" checked />'));

    // Testing single quote: ''
    $this->assertEquals(array('class' => 'main', 'id' => 'content', 'lonely'), Decoder::extract_attributes("<div class='main' id='content' lonely>Lorem ipsum</div>"));
  }

  public function test_decode_element() {
    $element = Decoder::decode_element('<div class="row" id="main" lonely>Lorem ipsum</div>');
    $this->assertTrue($element instanceof TagMaker\Element);
    $this->assertEquals('div', $element->tag);
    $this->assertEquals('Lorem ipsum', $element->content);
    $this->assertEquals(array('class' => 'row', 'id' => 'main', 'lonely'), $element->attributes);
  }

}