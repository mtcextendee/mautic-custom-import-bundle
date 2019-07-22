<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomImportBundle\Form\Type;

use Mautic\LeadBundle\Entity\Import;
use Mautic\LeadBundle\Model\ImportModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class  ImportListType extends AbstractType
{
    /**
     * @var ImportModel
     */
    private $importModel;

    /**
     * ImportListType constructor.
     *
     * @param ImportModel $importModel
     */
    public function __construct(ImportModel $importModel)
    {
        $this->importModel = $importModel;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $entities  = $this->importModel->getRepository()->getEntities([
                    'ignore_paginator' => true,
                ]);
                $choices = [];
                /** @var Import $entity */
                foreach ($entities as $entity) {
                    $choices[$entity->getId()] = $entity->getOriginalFile();
                }
                return $choices;
            },
            'attr'        => [
                'class' => 'form-control',

            ],
            'label'       => '',
            'expanded'    => false,
            'multiple'    => false,
            'required'    => false,
            'empty_value' => '',
        ]);
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

}
