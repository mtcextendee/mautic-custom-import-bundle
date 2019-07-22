<?php
return [
    'name'        => 'CustomImport',
    'description' => 'Mautic Custom import',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',

    'services' => [
        'forms' => [
            'mautic.custom.import.form.import.list.type' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Form\Type\ImportListType::class,
                'arguments' => [
                    'mautic.lead.model.import',
                ],
            ],
        ],
        'other'=>[
            'mautic.custom.import.factory' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Import\CustomImportFactory::class,
                'arguments' => [
                    'mautic.custom.import.directory',
                    'mautic.custom.import.parallel',
                    'mautic.helper.integration',
                ],
            ],
            'mautic.custom.import.directory' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Import\ImportFromDirectory::class,
                'arguments' => [
                    'mautic.lead.model.import',
                ],
            ],
            'mautic.custom.import.parallel' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Import\ParallelImport::class,
                'arguments' => [
                    'mautic.lead.model.import',
                    'mautic.helper.paths'
                ],
            ],
        ],
        'command' => [
            'mautic.custom.parallel.import.command' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Command\ParallelImportCommand::class,
                'arguments' => [
                    'mautic.custom.import.factory',
                    'translator'
                ],
                'tag' => 'console.command',
            ],
            'mautic.custom.directory.import.command' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Command\ImportFromDirectoryCommand::class,
                'arguments' => [
                    'mautic.custom.import.factory',
                    'translator'
                ],
                'tag' => 'console.command',
            ],
        ],
    ],
];