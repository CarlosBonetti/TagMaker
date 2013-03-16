# TagMaker #

A dynamic builder of tag elements

## Quick Start ##

### Instalation ###

Add to your composer.json:
```
"require": {
  "tagmaker/tagmaker": "dev-master"
}
```

### Usage ###

```php
$element = TagMaker::create('a', 'Link');
echo $element; // Will render '<a>Link</a>'
$element->set_class('btn');
$element->set_href('#');
$element->append_class('btn-large');
echo $element; // Output '<a href="#" class="btn btn-large">Link</a>'
```

## CSS-Like element creation

```php
$element = TagMaker::create('form.form-vertical#new-post[name=new-post,method=post]');
echo $element; // Output: '<form name="new-post" method="post" class="form-vertical" id="new-post"></form>'
```

## Magic methods for attributes manipulation

```php
$element = TagMaker::create('.content', 'Lorem ipsum');
$element->set_id('main-content');
$element->prepend_class('span6');

// Output: <div class="span6 content" id="main-content">Lorem ipsum</div>
```

Available attributes manipulation magic methods are: 
`append_{$attribute}($value)`, `prepend_{$attribute}($value)`, `set_{$attribute}($value)`, `get_{$attribute}()`
