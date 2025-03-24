# JetSearch – Search by Custom Attributes

**Adds support for searching WooCommerce products by custom attributes using JetSearch.**  
This plugin allows you to include manually defined attributes in the JetSearch widget and make them available in the “Search in taxonomy terms” option.

## How It Works

- Custom attributes that are not taxonomy-based are stored as part of the product's metadata
- This plugin allows you to **manually define** which attributes should be searchable
- JetSearch will treat them as “taxonomies” in its widget dropdown

## Example Use Case

You create a custom product attribute called `quality` in the admin and set its value to `high` for your T-shirts.

Now you want JetSearch to include this attribute in search results.

To do this, open the plugin JetSearch – Search by Custom Attributes and edit the list of custom attributes in the following code snippet:

```php
public function add_custom_attribute_taxonomies( $taxonomies ) {
    $taxonomies['attribute_quality'] = 'Custom: Quality';

    return $taxonomies;
}
```
You can add more attributes in the same way, for example:

```php
$taxonomies['attribute_brand'] = 'Custom: Brand';
```

Once this is done, go to the JetSearch widget settings and:

Enable Search in taxonomy terms

Select Custom: Quality in the list

Now, when a user types "high" into the search bar, products with quality = high will appear in the results.
