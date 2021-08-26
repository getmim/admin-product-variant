<?php

return [
    '__name' => 'admin-product-variant',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-product-variant.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-product-variant' => ['install','update','remove'],
        'theme/admin/product/variant' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'admin-product' => NULL
            ],
            [
                'product-variant' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminProductVariant\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-product-variant/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminProductVariant' => [
                'path' => [
                    'value' => '/product/(:product)/variant',
                    'params' => [
                        'product' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProductVariant\\Controller\\Variant::index'
            ],
            'adminProductVariantEdit' => [
                'path' => [
                    'value' => '/product/(:product)/variant/(:id)',
                    'params' => [
                        'product' => 'number',
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminProductVariant\\Controller\\Variant::edit'
            ],
            'adminProductVariantRemove' => [
                'path' => [
                    'value' => '/product/(:product)/variant/(:id)/remove',
                    'params' => [
                        'product' => 'number',
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProductVariant\\Controller\\Variant::remove'
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.product-variant.edit' => [
                'name' => [
                    'type' => 'text',
                    'label' => 'Variant Name',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'price' => [
                    'type' => 'number',
                    'label' => 'Price',
                    'rules' => [
                        'required' => true,
                        'numeric' => [
                            'min' => 0
                        ]
                    ]
                ],
                'image' => [
                    'type' => 'image',
                    'label' => 'Image',
                    'form' => 'std-image',
                    'rules' => [
                        'required' => TRUE,
                        'upload' => TRUE
                    ]
                ]
            ]
        ]
    ]
];
