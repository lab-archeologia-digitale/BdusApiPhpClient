# BdusApiPhpClient

An open source client, written in PHP, to interact with a [BraDypUS](https://github.com/jbogdani/BraDypUS) API database.

This library is aony a thin wrapper arount the BraDypUS API, wich is fully documented at [docs.bdus.cloud/api/](https://docs.bdus.cloud/api/).

A full example on how to use this library can be found in [test/test-BdusApiPhpClient.php](test/test-BdusApiPhpClient.php) file.

## ToC
- [Install and setup](#install-and-setup)
- [Initialize the class](#initialize-the-class)
- [Get API (BradypUS) version](#get-api-bradypus-version)
- [Get Unique values for a column](#get-unique-values-for-a-column)
- [Get Unique values for a column, with suggestion](#get-unique-values-for-a-column-with-suggestion)
- [Get Unique values for a column, with filter and suggestion](#get-unique-values-for-a-column-with-filter-and-suggestion)
- [Inspect database configuration](#inspect-database-configuration)
- [Inspect single table configuration](#inspect-single-table-configuration)
- [Search by providing an array](#search-by-providing-an-array)
- [Search by providing an array and pagination](#search-by-providing-an-array-and-pagination)
- [Search by providing ShortSQL](#search-by-providing-shortsql)
- [Search by providing ShortSQL and pagination](#search-by-providing-shortsql-and-pagination)
- [Get one record by ID](#get-one-record-by-id)

---

## Usage

### Install and setup
BdusApiPhoClient can be installed via Composer or manually.
Via Composer, require and download the library

```bash
composer require bdus-db/bdus-api-php-client
```
And then load it via Autoloader:

```php
<?php
require_once 'vendor/autoload.php';

use BdusApiPhpClient\BdusApiPhpClient;
```

For manual instalation, download the library and require it:


```php
<?php
require_once 'BdusApiPhpClient/src/BdusApiPhpClient.php';
use BdusApiPhpClient\BdusApiPhpClient;
```

### Initialize the class

```php
$api = new BdusApiPhpClient('https://bdus.cloud/db/api/', 'ghazni');
```

### Get API (BradypUS) version

```php
$api->getApiVersion();
```
Returns
```php
[
  'version' => '4.0.0-alpha.220',
]
```

### Get Unique values for a column

```php
// Gets unique values for column `provevance` of table `finds`
$api->getUniqueVal('finds', 'provevance');
```
Returns
```php
[
  'Tapa Sardar, Vihara 17',
  'Ghazni, Ghaznavid Palace Area',
  'Ghazni, Ghaznavid Palace',
  'Tapa Sardar, Room 36',
  'Tapa Sardar, Upper Terrace',
  'Tapa Sardar, II terrace',
  'Tapa Sardar, Area 64-100',
  ...
]
```

### Get Unique values for a column, with suggestion

```php
// Gets unique values for column `provevance` of table `finds`, get only values containing `Tapa`
$api->getUniqueVal('finds', 'provevance', 'Tapa');
```
Returns
```php
[
  'Tapa Sardar, Vihara 17',
  'Tapa Sardar, Room 36',
  'Tapa Sardar, Upper Terrace',
  'Tapa Sardar, II terrace',
  'Tapa Sardar, Area 64-100',
  ...,
]
```

### Get Unique values for a column, with filter and suggestion

```php
// Gets unique values for column `provevance` of table `finds`, get only values containing `Tapa` and limit search only to records having `Late period` in `relative_chronology` column
$api->getUniqueVal('finds', 'provevance', 'Tapa', 'relative_chronology|=|Late period');
```
Returns
```php
[
  'Tapa Sardar, Vihara 17',
  'Tapa Sardar, Room 36',
  'Tapa Sardar, Upper Terrace',
  'Tapa Sardar, Vihara 37',
  'Tapa Sardar, Vihara 23',
  ...,
]
```

### Inspect database configuration

```php
$api->inspect();
```
Returns
```php
[
  'finds' =>  [
    'name' => 'ghazni__finds',
    'label' => 'Finds',
    'order' => 'inv_no',
    'id_field' => 'inv_no',
    'preview' =>  [
      'inv_no',
      'archaeological_context',
      ...
    ],
    'plugin' =>  [
      'ghazni__m_biblio',
      'ghazni__m_inscriptions',
      ...
    ],
    'link' =>  [
      [
        'other_tb' => 'ghazni__funcomplex',
        'fld' =>  [
          [
            'my' => 'funcomplex',
            'other' => 'id',
          ]
        ]
      ]
    ],
    'fields' =>  [
      'id' =>  [
        'name' => 'id',
        'label' => 'ID',
        ...
      ],
      'creator' =>  [
        'name' => 'creator',
        'label' => 'Creator',
        'type' => 'text',
        'hide' => '1',
        'fullname' => 'ghazni__finds:ghazni__finds',
      ],
      'inv_no' => [
        'name' => 'inv_no',
        'label' => 'Inventory no.',
        'type' => 'text',
        'check' => [
          'no_dupl',
        ],
        'fullname' => 'ghazni__finds:ghazni__finds',
      ],
      'archaeological_context' =>  [
        'name' => 'archaeological_context',
        'label' => 'Cultural context',
        'type' => 'combo_select',
        'vocabulary_set' => 'archaeo_context',
        'fullname' => 'ghazni__finds:ghazni__finds',
      ],
      'provenance' => [
        'name' => 'provenance',
        'label' => 'Provenance',
        'type' => 'combo_select',
        'get_values_from_tb' => 'ghazni__finds:provenance',
        'fullname' => 'ghazni__finds:ghazni__finds',
      ],
      ...
    ],
    'stripped_name' => 'finds',
  ],
  'excavations' => [
    'name' => 'ghazni__excavations',
    'label' => 'Excavations',
    'order' => 'name',
    'id_field' => 'name',
    'preview' => [
      'name',
      'authors',
      'start_year',
      'end_year',
    ],
    'fields' => [
      'id' => [
        'name' => 'id',
        'type' => 'text',
        'hide' => true,
        'fullname' => 'ghazni__excavations:ghazni__excavations',
      ],
      'name' => [
        'name' => 'name',
        'label' => 'Name',
        'type' => 'text',
        'check' => [
          'no_dupl',
        ],
        'fullname' => 'ghazni__excavations:ghazni__excavations',
      ],
      ...
    ],
    'stripped_name' => 'excavations',
  ],
  'funcomplex' => [
    ...
  ],
  'bibliography' => [
    ...
  ],
  'files' => [
    ...
  ],
  'm_biblio' => [
    ...
  ]
]
```

### Inspect single table configuration

```php
$api->inspect('finds');
```
Returns
```php
[
  'stripped_name' => 'finds',
  'name' => 'ghazni__finds',
  'label' => 'Finds',
  'order' => 'inv_no',
  'id_field' => 'inv_no',
  'preview' => [
    'inv_no',
    ...
  ]
  ...
]
```

### Search by providing an array

**Pay attention:** search results are always paginated

```php
$api->searchShortSqlObj([
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
      ]);
```
Returns
```php
[
  'head' => [
    'total_rows' => 357,
    'total_pages' => 12,
    'stripped_table' => 'finds',
    'table_label' => 'Finds',
    'page' => 1,
    'no_records_shown' => 30,
    'fields' => [
      'id' => 'ID',
      'creator' => 'Creator',
      'inv_no' => 'Inventory no.',
      'archaeological_context' => 'Cultural context',
      ...
    ],
  ],
  'debug' => false,
  'records' => [
    [
      'id' => '168',
      'creator' => '11',
      'inv_no' => 'TS00038',
      'archaeological_context' => 'Buddhist',
      ...
    ],
    [
      'id' => '678',
      'creator' => '11',
      ...
    ]
  ]
]
```

### Search by providing an array and pagination

**Pay attention:** search results are always paginated

```php
$api->searchShortSqlObj([
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
      ], [
        'page' => 2
      ]);
```
Returns
```php
[
  'head' => [
    'total_rows' => 357,
    'total_pages' => 12,
    'stripped_table' => 'finds',
    'table_label' => 'Finds',
    'page' => 2,
    ...
  ]
  ...
]
```

### Search by providing ShortSQL


```php
$api->searchShortSql('@finds~?archaeological_context|=|Buddhist||and|provenance|like|Tapa Sardar');
```
This is actually the same query as the previous, provided as array

### Search by providing ShortSQL and pagination

```php
$api->searchShortSql('@finds~?archaeological_context|=|Buddhist||and|provenance|like|Tapa Sardar', ['page' => 2]);
```
This is actually the same query as the previous, provided as array


### Get one record by ID

```php
// Get record with id = 1 from table finds
$api->getOne('finds', '1');
```
Result:

```php
[
  'metadata' => [
    'tb_id' => 'ghazni__finds',
    'rec_id' => [
      'name' => 'id',
      'label' => 'ID',
      'val' => '1',
    ],
    'tb_stripped' => 'finds',
    'tb_label' => 'Finds',
  ],
  'core' => [
    'id' => [
      'name' => 'id',
      'label' => 'ID',
      'val' => '1',
    ],
    'creator' => [
      'name' => 'creator',
      'label' => 'Creator',
      'val' => NULL,
    ],
    'inv_no' => [
      'name' => 'inv_no',
      'label' => 'Inventory no.',
      'val' => 'TS01092',
    ],
    'archaeological_context' => [
      'name' => 'archaeological_context',
      'label' => 'Cultural context',
      'val' => 'Buddhist',
    ],
    ...
  ],
  'plugins' => [
    'ghazni__m_biblio' => [
      'metadata' => [
        'tb_id' => 'ghazni__m_biblio',
        'tb_stripped' => 'm_biblio',
        'tb_label' => 'Bibliographic data',
        'tot' => 2,
      ],
      'data' => [
        25 => [
          'id' => [
            'name' => 'id',
            'label' => false,
            'val' => '25',
          ],
          'table_link' => 
          ...
        ],
      ],
    ],
  ],
  'links' =>  [ ],
  'backlinks' =>  [ ],
  'manualLinks' => [ ],
  'files' =>  [
    [
      'id' => '3',
      'creator' => NULL,
      'ext' => 'jpg',
      'keywords' => NULL,
      'description' => NULL,
      'printable' => NULL,
      'filename' => 'TS 1092',
    ],
    ...
  ],
  'geodata' =>  [ ],
  'rs' =>  [ ],
]
```
