Labels translation
==================

Define the label strategy in admin `services.yml` definition:

```yml
sonata.admin.entity:
    class: Acme\ExampleBundle\Admin\EntityAdmin
    tags:
        - name: sonata.admin
          manager_type: orm
          group: "group.places"                                               # <== label code
          label: "entity.place"                                               # <== label code
          label_translator_strategy: "sonata.admin.label.strategy.underscore" # <== strategy
    arguments:
        - ~
        - Acme\ExampleBundle\Entity\Entity
        - 'SonataAdminBundle:CRUD'
```

The default catalogue is "messages", optionally change it in your admin class:

```php
class EntityAdmin extends Admin
{
    protected $translationDomain = 'messages';
}
```

Add translation file in:

```
Acme\ExampleBundle\Resources\translations\messages.<locale>.yml
```

Remember to clear the cache to refresh the translation catalogues:

```
$ ./app/console cache:clear
```

Remember also to add the labels in you FormTypes (see [EntityTranslationType: translations.md](translations.md))