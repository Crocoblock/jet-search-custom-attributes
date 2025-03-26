# JetSearch – Search by Custom Attributes

**Adds support for searching WooCommerce products by custom attributes using JetSearch.**  
This plugin allows you to include manually defined attributes in the JetSearch widget and make them available in the “Search in taxonomy terms” option.

## How It Works

- Custom attributes that are not taxonomy-based are stored as part of the product's metadata
- This plugin allows you to define which attributes should be searchable via a filter
- JetSearch will treat them as “taxonomies” in its widget dropdown

## Example Use Case

You create a custom product attribute called `quality` in the admin and set its value to `high` for your T-shirts.

Now you want JetSearch to include this attribute in search results.

To do this, use the following snippet in your `functions.php` file or a custom plugin:

```php
add_filter( 'jet_search/custom_attributes_list', function() {
    return [
        'quality' => 'Custom: Quality',
    ];
} );
```

Once this is done, go to the JetSearch widget settings and:

Enable `Search in taxonomy terms`

Select `Custom: Quality` in the list

Now, when a user types "high" into the search bar, products with quality = high will appear in the results.
