<?php

## This file is for the examples only!

Route::any('cart/(:any)/(:any?)', 'lara-cart::cart@(:1)');
Route::any('cart', 'lara-cart::cart@index');