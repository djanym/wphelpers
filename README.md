# wphelpers

Helper functions for custom themes or plugins

# Features

## Navwalker

### Usage

```php
<?php
wp_nav_menu( array(
	'menu_class'     => 'navbar-nav',
	'fallback_cb'    => '\Ricubai\WPHelpers\Navwalker::fallback',
	'walker'         => new \Ricubai\WPHelpers\Navwalker(),
) );
?>
```
