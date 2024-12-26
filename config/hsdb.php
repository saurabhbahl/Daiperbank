<?php

return [
    'pickup-date-lead-time-hours' => 113, // 113-hour lead time.

    'labels' => [
        'per_page' => 30,
        'products_per_label' => 12,
        // 'template' => 'Avery.8163.Label.Form.pdf',
        'template' => 'Avery.18160.Child.Label.Form.pdf',
    ],

    'wkhtmltopdf' => [
        'bin' => env('WKHTMLTOPDF_BIN', '/usr/bin/wkhtmltopdf'),
    ],

    'storage_folder' => 'pdf',

    'notifications' => [
        'widget_limit' => 10,
    ],

    'min-order-id' => 1000, // minimum order id, used during initial order seeding

    'low_water' => [
        'diaper' => [
            'warning' => 20000,
            'critical' => 5000,
        ],
        'pullup' => [
            'warning' => 500,
            'critical' => 200,
        ],
    ],

    'inventory' => [
        'adjustment_types' => [
            4 => 'Adjustment',
            2 => 'Diaper Drive',
            1 => 'General Contribution',
            7 => 'Gift Card Purchase',
            5 => 'Order',
            3 => 'Purchase',
            6 => 'Other',
        ],

        'adjustment_map' => [
            'DONATION' => 1,
            'DIAPER_DRIVE' => 2,
            'PURCHASE' => 3,
            'ADJUSTMENT' => 4,
            'ORDER' => 5,
            'OTHER' => 6,
            'GD_PURCHASE' => 7,
        ],
    ],
];