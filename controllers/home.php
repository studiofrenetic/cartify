<?php

## This file is for the examples only!

use Cartify\Models\Products;

class Cartify_Home_Controller extends Controller
{
// Make it RESTful
//
	public $restful = true;


   /**
 * --------------------------------------------------------------------------
 * Function: get_index()
 * --------------------------------------------------------------------------
 *
 * Show a list of the products on the page.
 *
 * @access   public
 * @return   void
 */
public function get_index()
{
// Show the page.
//
return View::make('cartify::home')->with('products', Products::get_list());
}
}
