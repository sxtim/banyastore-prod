<?php

return [
    'filters' => [
        'option_preview_limit' => 6,

        /*
         * Important filters are intentionally configured outside Vue components.
         * New category layouts can be tuned without duplicating UI logic.
         */
        'featured_property_ids' => [
            'peci-dlia-bani-i-sauny' => [1, 2, 3, 7, 4, 8],
        ],
        'brand_property_id' => [
            'peci-dlia-bani-i-sauny' => 1,
        ],
        'always_open_property_ids' => [
            'peci-dlia-bani-i-sauny' => [1, 2, 3],
        ],
    ],
];
