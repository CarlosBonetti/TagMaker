# TagMaker #

A dynamic builder of tag elements

## Quick Start ##

### Instalation ###

Add to your composer.json:
```
"require": {
  "tag-maker/tag-maker": "~0.1.0"
}
```

### Usage ###

```php
$element = TagMaker::create('a', 'Link');
echo $element; // Will render '<a>Link</a>'
$element->set_class('btn');
$element->set_href('#');
$element->append_class('btn-large');
echo $element; // Will render '<a href="#" class="btn btn-large">Link</a>'
```

### CSS-like element rules

```php
$element = TagMaker::create('form.form-vertical#new-post[name=new-post,method=post]');
echo $element; // Will render '<form name="new-post" method="post" class="form-vertical" id="new-post"></form>'
```