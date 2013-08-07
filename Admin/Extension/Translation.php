<?php

namespace Illarra\CoreBundle\Admin\Extension;

use Sonata\AdminBundle\Admin\AdminExtension;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class Translation extends AdminExtension
{
    protected $locales;

    public function __construct($locales)
    {
        $this->locales = $locales;
    }

    protected function updateLabels(FormInterface $form)
    {
        foreach ($form as $field) {
            $resolver = $field->getConfig()->getType()->getOptionsResolver();

            $resolver->replaceDefaults(array(
                'label' => 'form.label_xxx',
            ));

            if (!!count($field->all())) {
                $this->updateLabels($field);
            }
        }
    }

    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $entity = $event->getData();

            // Wait for the object to be ready
            if (!is_null($entity)) {
                $translations = $entity->getTranslations();

                // Initialize locales
                foreach ($this->locales as $locale) {
                    if (is_null($translations[$locale])) {
                        $entity->translate($locale);
                    }
                }

                $entity->mergeNewTranslations();
            }
        });

        /*
        $formMapper->getFormBuilder()->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();

            // Wait for the object to be ready
            if (!is_null($entity)) {
                // Change labels
                $form = $event->getForm();
                $this->updateLabels($form['translations']);
            }
        });
        */
    }
}
