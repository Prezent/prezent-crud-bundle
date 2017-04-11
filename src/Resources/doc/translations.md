Translations
============

All messages created by the CrudBundle are translated. Translation keys contain the configured name,
which defaults to your controller name (e.g. `product` for your `ProductController`). You should define
the following keys:

```yml
crud:
    <name>:
        index:
            title: 'Index title'
            action:
                add: 'Add new object button'
        add:
            title: 'Add title'
        edit:
            title: 'Edit title'

form:
    <name>:
        submit: 'Save object'
        cancel: 'Cancel'

flash:
    <name>:
        add:
            success: 'Object was added'
            error: 'Object could not be added'
        edit:
            success: 'Object was edited'
            error: 'Object could not be edited'
        delete:
            success: 'Object was deleted'
            error: 'Object could not be deleted'

modal:
    <name>:
        delete:
            title: 'Are you sure?'
            text: 'Are you sure you want to delete the object?'
            action:
                cancel: 'Cancel'
                delete: 'Delete'
```
