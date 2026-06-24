<?php

return [

    /*
     * Options shown in per-page dropdowns across the UI.
     * The first value is used as the default when no preference is stored.
     */
    'per_page_options' => [10, 20, 30, 40, 50],

    'default_per_page' => 20,

    /*
     * API pagination — these are not user-selectable; they cap the ?per_page query param.
     */
    'api_default_per_page' => 15,
    'api_max_per_page'     => 100,

];
