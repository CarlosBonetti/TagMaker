# TagMaker #

A dynamic builder of tag elements

## Quick Start ##

### Instalation ###

Add to your composer.json:
```
"require": {
  "tagmaker/tagmaker": "~0.5"
}
```

### Usage ###

```php
$element = TagMaker::create('a', 'Link', array('class' => 'btn'));
echo $element; // Will render '<a>Link</a>'
$element->set_href('#');
$element->append_class('btn-large');
echo $element; // Output '<a href="#" class="btn btn-large">Link</a>'
```

## CSS-Like element creation

```php
$element = TagMaker::create('form.form-vertical#new-post[name=new-post,method=post]');
echo $element; // Output: '<form name="new-post" method="post" class="form-vertical" id="new-post"></form>'
```

```php
$element = TagMaker::create('.row.span6#main {Lorem ipsum}');
echo $element; // Output: '<div class="row span6" id="main">Lorem ipsum</div>'
```

## Magic methods for attributes manipulation

```php
$element = TagMaker::create('.content', 'Lorem ipsum');
$element->set_id('main-content');
$element->prepend_class('span6');

// Output: <div class="span6 content" id="main-content">Lorem ipsum</div>
```

Available attributes manipulation magic methods are: 
`append_{$attribute}($value)`, `prepend_{$attribute}($value)`, `set_{$attribute}($value)`, `add_{$attribute}()` and `get_{$attribute}()`.

`add_{$attribute}()` tries to add an attribute to an Element, throwing a ExistentAttributeException if that attribute already exists.

`set_{$attribute}()` add an attribute to an Element, overriding it if already exists.

# HTML Decoder

TagMaker provides a way to decodes single HTML elements and transforms it to a `TagMaker\Element`. Examples:

```php
$element = TagMaker::decode('<div class="main" id="content">Lorem ipsum...</div>');
// Will create a TagMaker\Element based at the given HTML
```

Decoder does not support multiple elements (see Limitations).


# Limitations

The actual version does not support multiple elements.

Is pretended to support multiple elements (for decoding and creation) after 1.0 version.
