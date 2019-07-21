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
            'mautic.custom.import' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Import\CustomImport::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'mautic.lead.model.import',
                    'mautic.security'
                ],
            ],
        ],
        'command' => [
            'mautic.custom.parallel.import' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Command\ParallelImportCommand::class,
                'arguments' => [
                    'mautic.lead.model.import',
                    'mautic.helper.paths'
                ],
                'tag' => 'console.command',
            ],
            'mautic.custom.import.command' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Command\ImportFromDirectoryCommand::class,
                'arguments' => [
                    'mautic.custom.import',
                ],
                'tag' => 'console.command',
            ],
        ],
    ],
];