<?php

return [
    "dictdir_list" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/dictdir/list/?(?P<table>\w+)?",
            ],
            "action" => ['admin', 'list'],
        ],
    "dictdir_add" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/dictdir/add/(?P<table>\w+)",
            ],
            "action" => ['admin', 'add'],
        ],
    "dictdir_edit" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/dictdir/edit/(?P<table>\w+)/(?P<id>\w+)",
            ],
            "action" => ['admin', 'edit'],
        ],
    "dictdir_rm" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_GET],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/dictdir/rm/(?P<table>\w+)/(?P<id>\w+)",
            ],
            "action" => ['admin', 'rm'],
        ],
    "dictdir_save" =>
        [
            "methods" => [\DeltaRouter\Route::METHOD_POST],
            "patterns" => [
                "type" => \DeltaRouter\RoutePattern::TYPE_REGEXP,
                "value" => "^/admin/dictdir/save/(?P<table>\w+)",
            ],
            "action" => ['admin', 'save'],
        ],
];
