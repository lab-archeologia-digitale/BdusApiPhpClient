<?php
/**
 * Comunicates with BraDypUS DB public API and returns data structured in arrays
 * The main database should be set up to accept external API calls
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		Julian Bogdani, 2007-2021
 * @license			AGPL-3.0
 */

class BdusApiPhpClient
{
    /**
     * Full URL to BraDypUS' database, such as: https://bdus.cloud/db/
     * @var string
     */
    private $url;
    
    /**
     * BraDypUS application ID
     * @var string
     */
    private $app;


    /**
     * Initializes class and sets base URL and application name
     *
     * @param string $url: base URL to running application, es. https://bdus.cloud/db
     * @param string $app: Application name
     */
    public function __construct(string $url, string $app)
    {
        $this->url = ((substr($url, -1) === '/') ? $url : $url . '/');

        $this->app = $app;
    }

    /**
     * Returns the current version of bdus
     * e.g. [ "version": "4.0.0-alpha.220" ]
     *
     * @return array
     */
    public function getApiVersion () : array
    {
        return $this->getData([
            "verb" => "getApiVersion"
        ]);
    }

    /**
     * Returns chart data for chart id
     *
     * @param integer $id   Id of saved chart to return
     * @return array
     */
    public function getChart ( int $id ) : array
    {
        return $this->getData([
            "verb"  => "getChart",
            "id"    => $id
        ]);
    }

    /**
     * Returns unique values used in a specific column of a specific table
     * With the posisbility of offering suggestions (filter values as you type)
     * or limiting the results to a sub set by defining a filter
     *
     * @param string $tb            required, table name
     * @param string $fld           required, field name
     * @param string $suggestion    optional, part of string to filter results
     * @param string $filter        optional, ShortSQL where statement to look only in a subset
     * @return array                e.g. [ "value 1", "value n"]
     */
    public function getUniqueVal ( string $tb, string $fld, string $suggestion = null, string $filter = null ) : array
    {
        $p = [
            "verb"  => "getUniqueVal",
            "tb"    => $tb,
            "fld"    => $fld
        ];
        if ($suggestion){
            $p['s'] = $suggestion;
        }
        if ($filter){
            $p['w'] = $filter;
        }
        return $this->getData($p);
    }

    /**
     * Returns full configuration for a specific table (id $tb is provided)
     * or for entire database
     *
     * @param string $tb    optional, table name
     * @return array
     */
    public function inspect ( string $tb = null ) : array
    {
        $p["verb"] = "inspect";

        if ($tb){
            $p["tb"] = $tb;
        }
        return $this->getData($p);
    }

    
    /**
     * Builds ShortSQL from data object ($do),
     * runs query through the API
     * and returns data array
     *
     * @param array $do  data Object, as described below
     *      tb,     string, required, table name
     *      cols,   array, optional, columns array
     *      where,  array, optional, where statement, array of arrays: [ [fls, op, val], [conn, fld, op, val]]
     *      sort, optional, ordering statement, fld:dir
     *      limit, optional, limiting  statement, total:offset
     *      group, optional, array of fields to use for grouping
     *      join, optional, join statement, array of arrays: [ joinedtb: [[fls, op, val ], [conn, fld, op, val]] ]
     * @param array $params, optional parameters:
     *      total_rows          int, optional, default false
     *      page                int, optional, default 1:page number to return
     *      geojson             bool, optional, default false. If true, records will be returned as geojson
     *      records_per_page    int, optiona, default 30: number of records to return for each page
     *      full_records        bool, optional, default false: if true full information will be returned for each record
     * @return array
     */
    public function searchShortSqlObj (array $do, array $params = null ) : array
    {
        $str_arr = [];

        // tb
        if (!$do['tb']){
            return [];
        }
        $str[] = "@{$do['tb']}";

        // flds
        if (@$do['cols'] && is_array($do['cols'])){
            $str[] = "[" . implode(",", $do['cols']);
        }

        // where
        if (@$do['where'] && is_array($do['where'])){
            $wh_part = [];

            foreach ($do['where'] as $w) {
                $wh_part[] = $this->wherePartToShortSQL($w);
            }
            $str[] = "?" . implode("||", $wh_part);
        }

        // sort
        if (@$do['sort']){
            $str[] = ">{$do['sort']}";
        }

        // limit
        if (@$do['limit']){
            $str[] = "-{$do['limit']}";
        }

        // group
        if (@$do['group'] && is_array($do['group'])){
            $str[] = "*" . implode(",", $do['group']);
        }

        // join
        if (@$do['join'] && is_array($do['join'])){

            foreach ($do['join'] as $joinedtb => $joinw) {
                $j_part = [];

                foreach ($joinw as $j_part_arr) {
                    $j_part[] = implode('|', $j_part_arr);
                }

                $str[] = "]{$joinedtb}" . implode("||", $j_part);
            }
        }

        $shortsql = implode('~', $str);

        return $this->searchShortSQL($shortsql, $params);

    }

    /**
     * @param array $w      array, required: array of where data
     *      connector,      optional if first, string
     *      open-bracket,   optional, string
     *      fld,            required, string
     *      operator:       required, string,
     *      value,          required if subQuery not set, string
     *      subQuery: QueryObject, required if value not set, Object [Not implemented: passed as string]
     *      close-bracket,  optional, string
     */
    private function wherePartToShortSQL( array $w ) : string
    {
        $ret = [];
        if (!$w['fld'] || !$w['operator'] || !$w['value']){
            throw new Exception("Invalid where part: " . json_encode($w));
        }
        if (@$w['connector']) {
            array_push($ret, $w['connector']);
        }
        if (@$w['open-bracket'] && @$w['open-bracket'] === '(') {
            array_push($ret, '(');
        }
        array_push($ret, $w['fld']);
        array_push($ret, $w['operator']);
        array_push($ret, $w['value']);
        if (@$w['close-bracket'] && @$w['close-bracket'] === ')') {
            array_push($ret, ')');
        }
        return implode('|', $ret);

    }
    /**
     * Runs ShortSQl on the database and returns results
     *
     * @param string $shortsql  required, valid ShortSQL text
     * @param array $params, optional parameters:
     *      total_rows          int, optional, default false
     *      page                int, optional, default 1:page number to return
     *      geojson             bool, optional, default false. If true, records will be returned as geojson
     *      records_per_page    int, optiona, default 30: number of records to return for each page
     *      full_records        bool, optional, default false: if true full information will be returned for each record
     * @return void
     * @return array
     */
    public function searchShortSQL ( string $shortsql, array $params = null ) : array
    {
        if (!$params){
            $params = [];
        }
        $params = array_merge($params, [
            "verb" => "search",
            "shortsql" => $shortsql
        ]);
        return $this->getData($params);

    }

    /**
     * Returns formatted data for a single record
     * 
     * @param  string $tb   required, table name
     * @param  int $id      required, record id
     * @return array        array of formatted record data
     */
    public function getOne(string $tb, int $id) : array
    {
        return $this->getData([
            "tb" => $tb, 
            "verb" => "read",
            "id" => $id
        ]);
    }

    /**
     * Formats input array to query parameters and 
     * Gets data from the API
     *
     * @param array $params     required, array of parameters
     * @return array
     */
    private function getData(array $params) : array
    {
        $url = $this->url 
            . 'v2/' 
            .  $this->app 
            .  '?' . http_build_query($params);
 
        $data = file_get_contents($url);

        if (!$data) {
            return false;
        }

        $array = json_decode($data, true);

        if (!$array || !is_array($array)) {
            return [];
        }

        return $array;
    }
}
