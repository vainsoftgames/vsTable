# vsTable

## Introduction

`vsTable` is a dynamic table generation library designed to simplify the creation of interactive tables in web applications. With support for sorting, pagination, and customizable columns, `vsTable` provides a robust alternative to Google Tables, tailored for PHP-based projects.
`vsTable` was primaryly created due to Google Tables JS taking to long to generate tables on slower end clients, even more so when you have lots of tables. So this is a server side approach to generating tables.

## Installation

To use `vsTable` in your project, simply include the `vsTable.php` file in your PHP script:

```php
require_once 'path/to/vsTable.php';
```


## How to Use
To create a new table with vsTable, you need to prepare your data in a specific format and then instantiate the vsTable class with this data.
### Basic Example
```php
// Define your table data
$tableData = [
    'cols' => [
        ['label' => 'ID', 'type' => 'numeric', 'sort' => 'id'],
        ['label' => 'Name', 'type' => 'text', 'sort' => 'name'],
        // Add more columns as needed
    ],
    'rows' => [
        ['c' => [['v' => 1], ['v' => 'John Doe']]],
        ['c' => [['v' => 2], ['v' => 'Jane Doe']]],
        // Add more rows as needed
    ]
];

// Initialize vsTable with your data
$table = new vsTable($tableData);

// Render the table
echo $table->buildTable();
```

## Enabling Sorting

Sorting is built-in and can be enabled by setting the sort parameter in the column definitions. To fully utilize sorting, you'll need to implement the backend logic to reorder your data based on the user's selection. The ajaxRequest_tableSort function (which you should define in your JavaScript) is triggered when a sortable column is clicked.

## Pagination

vsTable supports pagination controls. To utilize this feature, you need to manually handle page navigation logic and regenerate the table data based on the current page. Use the buildTFOOTER method to add pagination controls, specifying the current page number, total pages, and items per page.

## Customization

You can customize the appearance of your tables with CSS. vsTable assigns CSS classes based on the column type and additional properties (e.g., sortable, optional). Here's a basic example:
```css
.vsTable {
    border-collapse: collapse;
    width: 100%;
}

.vsTable td, .vsTable th {
    border: 1px solid #ddd;
    padding: 8px;
}

/* Add more styles as needed */
```

For advanced customization, you can add additional properties to your columns and rows, such as custom classes or styles, and handle them accordingly in your PHP script or frontend code.

## Contributing

Contributions to vsTable are welcome! Please feel free to submit issues, pull requests, or suggestions to improve the library.
