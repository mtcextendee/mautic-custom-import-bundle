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
        'integrations' => [
            'mautic.integration.customimport' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Integration\CustomImportIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'logger',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
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
                    'doctrine.orm.entity_manager'
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
            'mautic.custom.remove.tags.command' => [
                'class'     => \MauticPlugin\MauticCustomImportBundle\Command\RemoveTagsCommand::class,
                'arguments' => [
                    'mautic.custom.import.factory',
                    'translator'
                ],
                'tag' => 'console.command',
            ],
        ],
    ],
];