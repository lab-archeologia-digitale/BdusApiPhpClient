# bdusApiClient

Two open source clients, written in PHP and in Javascript to interact with a [BraDypUS](https://github.com/jbogdani/BraDypUS) database.

* [PHP Version](#usage-of-the-php-version)
* [JavaScript version](#usage-of-the-javascript-version)
* [BraDypUS data structure](#bradypus-data-structure)
* [MIT license](mit-license)

---

## Usage of the PHP version

***NB:*** *This guide uses a fake database application named here **mydb** and a
fake table named **mytable**. Change these values to match your needs.*

1. Include the library

        require_once '../php/bduscms-api-client.php';

2. Initialize the class

        $api = new bdusApiClient('http://db.bradypus.net/api', 'mydb');

3. Change the number of records per page. This is optional and the default value is 30.

        $api->setRecordsPerPage(15);

3. Start querying the database, using the available methods. All methods will
return arrays of data

  * Get all data about a single record, using it's ID, eg. 10

          $data = $api->getOne('mytable', '10');

  * Get all records

          $data = $api->searchAll('mytable');

  * Search for a string in all fields of the table

          $data = $api->searchString('mytable', 'something');

    ***NB*** *The **LIKE** sql operator will be used and the string will be wrapped in the **%** wildcard*

  * Perform an advanced search

          $data = $api->searchAdv('mytable', [
            'adv' => [
              'one' => [
                'fld' => 'mudb__mytable:field1',
                'operator' => 'LIKE',
                'value' => 'something'
              ]
            ],
            [
            'two' => [
              'connector' => 'AND',
              'fld' => 'mydb__mytable:field2',
              'operator' => 'LIKE',
              'value' => 'something else'
              ]
            ]
          ]);

  * Execute some custom SQL code

          $data = $api->searchSQL('mytable', '`field1` LIKE \'%something%\'');

    ***NB*** *The SQL code will be checked and cleaned before usage*

  * Go to a certain page of the results obtained, eg. of a get all records query

          $data = $api->searchAll('mytable');

          $data2 = $api->go2page('mytable', 2, $data1['head']['query_encoded']);

      **You do not have to manully enter the encoded query as third parameter of this method. This can be retrieved from the results of a previously executed query**


## Usage of the Javascript version
The methods of javascript version share the same names and usage with the PHP version,
except that each one accepts as last parameter a callback function that will be executed as soon as the data will be available from the database.

This function accepts two parameters

  * a javascript object with all data received from the database
  * an instance of itself

The callback function can be used to create the visual layout of the data.

The javascript client requires [jQuery](https://jquery.com/).

1. Include jQuery and the library

        <script src="https://code.jquery.com/jquery-3.1.0.min.js"   integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s="   crossorigin="anonymous"></script>

        <script src="../js/bdusApiClient.js"></script>

2. Initialize the class

        var api = new bdusApiClient({
          'url': 'http://db.bradypus.net/api/',
          'app': 'mydb'
        });

3. Change the number of records per page. This is optional and the default value is 30.

        api.setRecordsPerPage(15);

3. Start querying the database, using the available methods. All methods will
return arrays of data

  * Get all records

              api.searchAll('mytable', function(data, this){
                console.log('Showing ' + data.head.no_records_shown + ' records of ' + data.head.total_rows');

                $.each(data.records, function(i, record){
                  console.log(record);
                });
              });

  * etc..


## BraDypUS data structure

The API returns arrays of two different types, one for the single record and one for the list of records.

### Single record

The single record structure is returned only by the `getOne` method. The main array is composed by:

* `fields`: associative array of field ids (indexes) and field labels (values), eg:

        "fields" => [
          "id" => "Id field",
          "creator" => "First creator of the record",
          ...
        ]

* `core`: associative array of field ids (indexes) and their values (values) of the main table

        "core" => [
          "id" => 10,
          "creator" => 7,
          ...
        ]

* `corelinks`: associative array with automatic links data, for all tables

        "coreLinks" => [
          "table1" => [         // Name of the linked table
            "tot" => 10         // Total number of links in table 1
            "query" => "{SQL}"  // SQL to retrieve linked records
          ],
          "table2" => [ ... ]
        ]

* `allPlugins`: array of data from plugins (1-n) tables

        "allPlugins" => [
          "plugin1" => [
            "fld1" => "value1",
            ...
          ],
          "plugin2" => [...]
        ]

* `fullFiles`: indexed array with list of attached files

        "fullFiles" => [
          [
            "id" => 245
            "creator" => 7
            "ext" => "jpg"
            "keywords" => ""
            "description" => ""
            "printable" => ""
            "filename" => "C0272_MR2004"
            "linkid" => 323
          ],
          [...]
        ]

* `geodata`: indexed array with list of attached geodata

        "geodata" => [
          [
            "id" => "",
            "table_link" => "",
            "id_link" => "",
            "geometry" => "",
            "geo_el_elips" => "",
            "geo_el_asl" => ""
          ],
          [...]
        ]

* `userlinks`: indexed array with links entered manually

        "userLinks" => [
          [
            "id" => "",
            "tb" => "",
            "ref_id" => ""
          ],
          [...]
        ]

* `rs`: indexed array with stratigraphic relationship      

        "rs" => [
          [
            "id" => "",
            "tb" => "",
            "first" => "",
            "second" => "",
            "relation" => ""
          ],
          [ ... ]
        ]

### List of records

The list record structure is returned by all other methods other then `getOne`.
The main array is composed by two parts, the `head` section and the `records` section.
The `records` section is an indexed array of record arrays, as described above.
The `head` section is structured as follows:
* `query_arrived`, string, full SQL text of the query
* `query_encoded`, string, base64 encoded full text of the query
* `total_rows`, int, total of records found by the query
* `page`, int, current page of results
* `total_pages`, int, total number of pages found by the query
* `table`, string, full name of the reference table
* `stripped_table`, string, short name of the referenced table, stripped of application name
* `no_records_shown`, int, total number of records shown in current page
* `query_executed`, string, the SQL text of the query executed, complete of pagination information
* `fields`, array, associative array of field ids (indexes) and field labels (values).

#### Example

        [
          "head" => [
            "query_arrived" => "",
            "query_encoded" => "",
            "total_rows" => "",
            "page" => "",
            "total_pages" => "",
            "table" => "",
            "stripped_table" => "",
            "no_records_shown" => "",
            "query_executed" => "",
            "fields" => [
              "id" => "Id field",
              "creator" => "First creator of the record",
              ...
            ]
          ],
          "records" => [
            "corelinks" => [ ... ],
            "corelinks" => [ ... ],
            "allPlugins" => [ ... ],
            "fullFiles" => [ ... ],
            "geodata" => [ ... ],
            "userlinks" => [ ... ],
            "rs" => [ ... ]
          ]
        ]

---

## MIT license

The MIT License (MIT)
Copyright (c) 2016 Julian Bogdani (BraDypUS)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
