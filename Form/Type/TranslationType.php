<?php

namespace Illarra\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['admin']);

        $resolver->setDefaults(array(
            'allow_add'    => false,
            'by_reference' => true,
            'type'         => function (Options $options) {
                $class = str_replace(['Entity', 'Document'], 'Form', $options['admin']->getClass()) . 'TranslationType';

                return new $class();
            },
            'options'      => function (Options $options) {
                return [
                    'data_class' => $options['admin']->getClass() . 'Translation',
                ];
            }
        ));
    }

    public function getParent()
    {
        return 'collection';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'illarra_sonata_translation';
    }
}