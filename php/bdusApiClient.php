<?php
/**
 * Comunicates with BraDypUS DB public API and returns data structured in arrays
 * The main database should be set up to accept externa api calls
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014
 * @license			All rights reserved
 * @since				Mar 5, 2014
 */

class bdusApiClient
{
  /**
   * Full URL to BraDypUS' database, usually: http://db.bradypus.net
   * @var string
   */
  private $url;
  /**
   * BraDypUS application pplipcation ID
   * @var string
   */
  private $app;
  /**
   * Number of records to retrieve for each page
   * @var int
   */
  private $records_per_page;

  /**
   * Sets the base URL and the application id.
   * @param string $url BraDypUS' base url
   * @param string $app BraDypUS' application id
   */
  public function __construct($url,$app)
  {
    $this->url = ((substr($url, -1) === '/') ? $url : $url . '/');

    $this->app = $app;
  }

  /**
   * Sets the number of the records to retrieve for each page
   * @param int $records_per_page Total number of records per page
   */
  public function setRecordsPerPage($records_per_page)
  {
    $this->records_per_page = $records_per_page;
  }

  /**
   * Returns formatted data for a record
   * @param  string $tb reference table
   * @param  int $id record ID
   * @return array     Formatted data
   */
  public function getOne($tb, $id)
  {
    //$this->get['id']
    $url = $this->getUrl($tb) . 'id=' . $id;

    return $this->getData($url);
  }

  /**
   * Returns formatted data for entire table
   * @param  string table $tb reference
   * @return array     Formatted data
   */
  public function searchAll($tb)
  {
    $url = $this->getUrl($tb) . 'type=all';

    return $this->getData($url);
  }

  /**
   * Returns formatted data after perforing a string search in all fields of the table
   * @param  string $tb     Reference table
   * @param  string $string string to search
   * @return array     Formatted data
   */
  public function searchString($tb, $string)
  {
    $url = $this->getUrl($tb)
      . 'type=fast&string=' . $string;

    $data = $this->getData($url);

    return $data;
  }

  /**
   * Performs an advanced search and returns data
   * @param  string $tb     Reference table
   * @param  array $data array of data with search parameters, es:
   *                     [
     *                     adv => [
     *                      row1 => [
     *                        fld => string         name of the field to look in, in the formf of appname__tablename:fieldname
     *                        operator => string    operator, eg.: =, >, <, LIKE, etc.
     *                        value => string       value to search for
     *                      ],
     *                      row2 => [               Row 2 is optional, but all rows other than the first should start with a connector
     *                        connector => string   connector, eg.: AND, OR, NOT, etc.
     *                        fld => string
     *                        operator => string
     *                        value => string
     *                      ]
     *                     ],
     *                     rowN => [ ... ]
     *                     order => [               optional, sorting options
     *                      index1 => string,       name of the (first) field to use for the sorting
     *                      index2 => string        optional, name of the second field to use for the sorting
     *                      index3 => ...,
   *                       ]
   *                     ]
   * @return array     Formatted data
   */
  public function searchAdv($tb, $data)
  {
    $url = $this->getUrl($tb)
      . 'type=advanced';

    if (is_array($data))
    {
      $url .= '&' . http_build_query($data);
    }
    return $this->getData($url);
  }

  /**
   * Executes a pre-formatted search and returns data
   * @param  string $tb  Reference table
   * @param  string $sql Query text
   * @return array     Formatted data
   */
  public function searchSQL($tb, $sql)
  {
    $url = $this->getUrl($tb)
      . 'type=sqlExpert&querytext=' . urlencode($sql);

    return $this->getData($url);
  }

  /**
   * Jumps to resuts of a different page, for paginted results
   * @param  string $tb       Reference table
   * @param  int $page        Page number
   * @param  string $q_encoded Encoded query text
   * @return array     Formatted data
   */
  public function go2page($tb, $page, $q_encoded)
  {
    $url = $this->getUrl($tb)
      . 'type=encoded&q_encoded=' . $q_encoded . '&page=' . $page;

    return $this->getData($url);
  }

  /**
   * Retrieves data from the API
   * @param  string $url API endpoint
   * @return array      Structrured (array) data
   */
  private function getData($url)
  {
    $data = file_get_contents($url);

    if (!$data) return false;

    $array = json_decode($data, true);

    if (!$array || !is_array($array)) return false;

    return $array;
  }

  /**
   * Returns formatted URL
   * @param  string $tb Reference table
   * @return string     Formatted URL
   */
  private function getUrl($tb)
  {
    return $this->url
      . $this->app
      . '/' . $tb . '/'
      . ($this->records_per_page ? 'records_per_page=' . $this->records_per_page . '&': '')
      ;
  }
}
?>
