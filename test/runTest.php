<?php
require_once '../vendor/autoload.php';

use TestBdusApiPhpClient\TestBdusApiPhpClient;

foreach ([
    [
      "title" => "Get Version",
      "method" => "getApiVersion",
      "params" => []
    ],
    [
      "title" => "Get Chart",
      "method" => "getChart",
      "params" => [1]
    ],
    [
      "title" => "Get Unique values for a column",
      "method" => "getUniqueVal",
      "params" => ['finds', 'provenance']
    ],
    [
      "title" => "Get Unique values for a column, with suggestion",
      "method" => "getUniqueVal",
      "params" => ['finds', 'provenance', 'Tapa']
    ],
    [
      "title" => "Get Unique values for a column, with filter and suggestion",
      "method" => "getUniqueVal",
      "params" => ['finds', 'provenance', 'Tapa', 'relative_chronology|=|Late period']
    ],
    [
      "title" => "Inspect database configuration",
      "method" => "inspect",
      "params" => []
    ],
    [
      "title" => "Inspect single table configuration",
      "method" => "inspect",
      "params" => ['finds']
    ],
    [
      "title" => "Search by providing an array",
      "method" => "searchShortSqlObj",
      "params" => [
        [
        'tb' => 'finds',
        'cols' => 'inv_no,archaeological_context,provenance,relative_chronology',
        'where' => [
            [
              'fld' => 'archaeological_context',
              'operator' => '=',
              'value' => 'Buddhist'
            ],
            [
              'connector' => 'and',
              'fld' => 'provenance',
              'operator' => 'like',
              'value' => 'Tapa Sardar'
            ],
          ],
        ]
      ]
    ],
    [
      "title" => "Search by providing an array and pagination",
      "method" => "searchShortSqlObj",
      "params" => [
        [
          'tb' => 'finds',
          'cols' => 'inv_no,archaeological_context,provenance,relative_chronology',
          'where' => [
            [
              'fld' => 'archaeological_context',
              'operator' => '=',
              'value' => 'Buddhist'
            ],
            [
              'connector' => 'and',
              'fld' => 'provenance',
              'operator' => 'like',
              'value' => 'Tapa Sardar'
            ]
          ],
        ],
        [
          'page' => 2
        ]
      ]
    ],
    [
      "title" => "Search by providing ShortSQL",
      "method" => "searchShortSQL",
      "params" => ['@finds~?archaeological_context|=|Buddhist||and|provenance|like|Tapa Sardar']
    ],
    [
      "title" => "Search by providing ShortSQL, with pagination",
      "method" => "searchShortSQL",
      "params" => ['@finds~?archaeological_context|=|Buddhist||and|provenance|like|Tapa Sardar', ["page" => 2]]
    ],
    [
      "title" => "Get one record by ID",
      "method" => "getOne",
      "params" => ['finds', 1]
    ],
  ] as $m) {
    echo TestBdusApiPhpClient::run($m['title'], $m['method'], $m['params']);
  }
