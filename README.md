# Axial hexagonal grid generator for PHP

This package is a port of [Axial hexagonal grid](https://github.com/RobertBrewitz/axial-hexagonal-grid) by Robert Brewitz, originally written in JS.

## Installation

You can install the package via composer:

```bash
composer require astatroth/php-hexgrid
```

## Usage

``` php
use Astatroth\HexGrid\Grid;

$grid = new Grid;
print_r($grid->hexagon(0, 0, 3, true));
```

The resulting array of coordinates can be user to draw a hexagonal grid itself. Visit the [original github page](https://github.com/RobertBrewitz/axial-hexagonal-grid/tree/master/example) if you want an example of how to draw the grid.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.