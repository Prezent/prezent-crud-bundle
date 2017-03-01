Configuration
=============

The Configuration model allows you to configure how the default CRUD actions work. There
are thee required settings: the entity class, the form type and the grid type. All other
settings are optional and have sane defaults.


### `setEntityClass(string $entityClass)`

Set the classname of the Doctrine object to be managed. This can be an Entity or a Document (MongoDB,
CouchDB, PHPRC), as long as its ObjectManager is known by the registry in the DoctrineBundle.


### `setFormType(string $formType)`

The Symfony form type to use for creating and editing objects.


### `setFormTheme(string $template)`

The template to use as a custom form theme.


### `setFormOptions(array $options)`

Options to pass to the form builder.


### `setGridType(string $gridType)`

The grid type to use for creating and editing objects.


### `setGridTheme(string $template)`

The template to use as a custom grid theme.


### `setGridOptions(array $options)`

Options to pass to the grid builder.


### `setName(string $name)`

The name of the controller. A default name is automatically constructed from your controller
class. A `ProductController` would have `"product"` as a name. A `ProductTypeController` would
have `"producttype"` as a name. Use this setting to override the name.


### `setRoutePrefix(string $routePrefix)`

The route prefix to use. This is the part of the route name without the action. This is automatically
determined from the controller class, see also the
[Symfony @Route documentation](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/routing.html#route-name).
A controller class of `Vendor\AppBundle\ProductController` would have a prefix of `"vendor_app_product_"`.


### `setRouteParameters(array $routeParameters)`

Pass extra parameters to the generated routes. By default no extra parameters are required.


### `setDefaultSortField(string $field)`

The field on which your grid will be sorted. Defaults to `"id"`.


### `setDefaultSortOrder(string $order)`

The order in which your grid will be sorted, can be `"ASC"` or `"DESC"`. Defaults to `"ASC"`.


### `setTranslationDomain(string $domain)`

The translation domain to use for all messages in the templates. Note that this does not effect the translation
domain used in the form and the grid. You should still specify those using the `translation_domain` option in
the from and grid itself. The default translation domain is `"messages"`.


### `setTemplateVariables(array $vars)`

Extra variables that you want to pass to the Twig template.


### `setResultsPerPage(int $resultsPerPage)`

The number of results per page displayed by the grid. Defaults to 10.