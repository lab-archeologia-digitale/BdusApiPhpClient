# bdusApiClient

Two open source clients, written in PHP and in Javascript to interact with a [BraDypUS](https://github.com/jbogdani/BraDypUS) database.

* [PHP Version](#usage-of-the-php-version)
* [JavaScript version](#usage-of-the-javaScript-version)
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


## Usage of the JavaScript version
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



### MIT license

The MIT License (MIT)
Copyright (c) 2016 Julian Bogdani (BraDypUS)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
