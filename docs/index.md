---
title: Introduction
intro: Matrices of real numbers for PHP. Every matrix is an immutable value object.
group: Getting started
order: 10
---

## Requirements

The library needs a current PHP version and `phpmath/vector`, which Composer installs for you. The vector classes are what you transform with a matrix.

- PHP 8.4 or newer
- `phpmath/vector` 2.0 or newer

## Installation

Install the library with Composer.

```bash
composer require phpmath/matrix
```

## A first example

A matrix is a grid of numbers arranged in rows and columns. Create one from a list of rows, then combine it with other matrices.

```php
use PHPMath\Matrix\Matrix;

$a = Matrix::fromRows([[1, 2], [3, 4]]);
$b = Matrix::fromRows([[0, 1], [1, 0]]);

echo $a->add($b);      // [[1, 3], [4, 4]]
echo $a->multiply($b); // [[2, 1], [4, 3]]

echo $a->determinant(); // -2
echo $a->inverse();     // [[-2, 1], [1.5, -0.5]]
```

Calling `add()` did not change `$a`. It returned a new `Matrix`. This is true for every operation in the library.

## Where to go next

Start with the core concepts, or jump straight to the topic you need.

<div class="cards"><a href="concepts.md"><strong>Core concepts</strong><span>Immutability, creating matrices and reading values.</span></a><a href="arithmetic.md"><strong>Arithmetic</strong><span>Add, subtract, scale and multiply.</span></a><a href="shape.md"><strong>Shape and transpose</strong><span>Size, transpose, trace and square matrices.</span></a><a href="determinant-and-inverse.md"><strong>Determinant and inverse</strong><span>Solve and invert square matrices.</span></a><a href="comparison.md"><strong>Comparison</strong><span>Compare matrices with a tolerance.</span></a><a href="transformations.md"><strong>Transformations</strong><span>Move, scale and rotate points with 4x4 matrices.</span></a><a href="api.md"><strong>API reference</strong><span>Every method with its parameters and errors.</span></a></div>
