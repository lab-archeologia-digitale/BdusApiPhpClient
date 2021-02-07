<?php

require_once '../BdusApiPhpClient.php';

class TestBdusApiPhpClient
{
  private static $url = 'https://bdus.cloud/db/api/';
  private static $app = 'ghazni';

  public static function run()
  {
    $api = new BdusApiPhpClient(self::$url, self::$app);
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
      $res = call_user_func_array([$api, $m['method']], $m['params']);
      
      echo self::res($m['title'], $m['method'], $m['params'], $res);
      unset($res);
    }
  }

  private static function res(string $title, string $method, array $params, array $res): string
  {
    $params_list = implode(', ', array_map(function($el){
      if (is_array($el)){
        return self::varexport($el);
      } else {
        return "'{$el}'";
      }
    }, (array)$params));
  
    $html = "<h3>$title</h3>";
    $html .= "<code>BdusApiPhpClient::{$method}({$params_list});</code><br>";
    $html .= '<table style="width:100%;table-layout:fixed;">'
      . '<tr>'
        . '<td style="width:50%; border: 1px solid; padding:1rem;background-color:#ebebeb;">'
          . '<pre style="max-height:100px;overflow:auto;">' . var_export($res, true) . '</pre>'
        . '</td>'
        . '<td style="width:50%; max-height:100px;border: 1px solid; padding:1rem;background-color:#ebebeb;">'
          . '<pre style="max-height:100px;overflow:auto;">' . json_encode($res, JSON_PRETTY_PRINT) . '</pre>'
        . '</td>'
      .'</tr>'
    . '</table>';
    
    return $html;
  }


  private static function varexport(array $expression): string
  {
    $export = var_export($expression, true);
    $patterns = [
        "/array \(/" => '[',
        "/^([ ]*)\)(,?)$/m" => '$1]$2',
        "/=>[ ]?\n[ ]+\[/" => '=> [',
        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
    ];
    $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
    return $export;
  }
}


TestBdusApiPhpClient::run();