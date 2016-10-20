Delete modals
-------------

The grid view has a built-in modal that can pop up a confirmation dialog before deleting
an object. To use this modal you must include the following two javascript files:

* foundation.reveal.js (from Zurb Foundation, not included)
* bundles/prezentcrud/js/crud.js

The modal will trigger when it finds a grid action with a class of `"delete"`. You can configure
this class in your grid when you add the action:


```php
public function buildGrid(GridBuilder $builder, array $options = [])
{
    $builder
        // ...
        ->addAction('delete', [
            'attr' => ['class' => 'delete'],
            'route' => 'app_product_delete',
            'route_parameters' => ['id' => '{id}'],
        ])
    ;
}
```

The text of the modal can be configured in your translation file, see [Translations](translations.md).
