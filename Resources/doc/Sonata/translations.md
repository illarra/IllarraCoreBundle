Using Knp DoctrineBehaviors Translatable with Sonata Admin
==========================================================

Add illarra translation extension and set the list of SonataAdmins which must
use the extension.

```yaml
sonata_admin:
    extensions:
        illarra_core.admin.translation.extension:
            admins:
                - sonata.admin.entity
```

Add the translations on the SonataAdmin class:

```php
class EntityAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('translations', 'illarra_sonata_translation', array('admin' => $this))
        ;
    }
}
```

Create a form type for the `EntityTranslation` named `EntityTranslationType` on 
the `Form` folder of the bundle. This form type is going to be used for the
translation embedded forms.

Remember to add the labels in the `EntityTranslationType`:

```php
class PlaceTranslationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'hidden')
            ->add('name', null, ['label' => 'form.label_name'])
            ->add('description', null, ['label' => 'form.label_description'])
            // ...
        ;
    }

    // ...
}
```
