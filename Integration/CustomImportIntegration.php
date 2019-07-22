<?php

namespace MauticPlugin\MauticCustomImportBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;
use MauticPlugin\MauticCustomImportBundle\Form\Type\ImportListType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomImportIntegration extends AbstractIntegration
{
    /**
     * @return string
     */
    public function getName()
    {
        // should be the name of the integration
        return 'CustomImport';
    }

    /**
     * @return string
     */
    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/MauticCustomImportBundle/Assets/img/icon.png';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ($formArea == 'features') {
            $builder->add(
                'template_from_import',
                ImportListType::class,
                [
                    'label'      => 'mautic.custom.import.form.template_from_import',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'    => 'form-control',
                    ],
                    'multiple'    => false,
                    'required'    => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

            $builder->add(
                'path_to_directory_csv',
                TextType::class,
                [
                    'label'      => 'mautic.custom.import.form.path_to_directory_csv',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

            $builder->add(
                'limit',
                NumberType::class,
                [
                    'label'      => 'mautic.custom.import.parallel.records.limit',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'tooltip' => 'mautic.custom.import.parallel.records.limit.tooltip',
                        'class'        => 'form-control',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );

            $builder->add(
                'tagsToRemove',
                'lead_tag',
                [
                    'add_transformer' => true,
                    'by_reference'    => false,
                    'label' => 'mautic.custom.import.remove.tags',
                    'attr'            => [
                        'data-placeholder'     => $this->translator->trans('mautic.lead.tags.select_or_create'),
                        'data-no-results-text' => $this->translator->trans('mautic.lead.tags.enter_to_create'),
                        'data-allow-add'       => 'true',
                        'onchange'             => 'Mautic.createLeadTag(this)',
                    ],
                ]
            );
        }
    }

}
