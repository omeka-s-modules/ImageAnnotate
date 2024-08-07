<?php
namespace ImageAnnotate;

return [
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => sprintf('%s/../language', __DIR__),
                'pattern' => '%s.mo',
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            sprintf('%s/../view', __DIR__),
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            sprintf('%s/../src/Entity', __DIR__),
        ],
        'proxy_paths' => [
            sprintf('%s/../data/doctrine-proxies', __DIR__),
        ],
    ],
    'block_layouts' => [
        'invokables' => [
            'imageAnnotateAsset' => Site\BlockLayout\ImageAnnotateAsset::class,
        ],
        'factories' => [
            'imageAnnotateMedia' => Service\Site\BlockLayout\ImageAnnotateMediaFactory::class,
        ],
    ],
    'resource_page_block_layouts' => [
        'factories' => [
            'imageAnnotateMedia' => Service\Site\ResourcePageBlockLayout\ImageAnnotateMediaFactory::class,
            'imageAnnotateItem' => Service\Site\ResourcePageBlockLayout\ImageAnnotateItemFactory::class,
        ],
    ],
];
