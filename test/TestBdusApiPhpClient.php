<?php
namespace TestBdusApiPhpClient;

use BdusApiPhpClient\BdusApiPhpClient;


class TestBdusApiPhpClient
{
  private static $url = 'https://bdus.cloud/db/api/';
  private static $app = 'ghazni';

  public static function run(string $title, string $method, array $params) : string
  {
    $api = new BdusApiPhpClient(self::$url, self::$app);
    
    $res = call_user_func_array([$api, $method], $params);
      
    return self::res($title, $method, $params, $res);
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