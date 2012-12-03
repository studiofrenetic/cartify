<?php

## This file is for the examples only!

use LaraCart\Models\Products;

class Lara_Cart_Wishlist_Controller extends Controller
{
    // Make it RESTful
    //
	public $restful = true;


   /**
     * --------------------------------------------------------------------------
     * Function: get_index()
     * --------------------------------------------------------------------------
     *
     * Show a list of the products on the wishlist.
     *
     * @access   public
     * @return   void
     */
	public function get_index()
	{
		// Show the page.
		//
		return View::make('lara-cart::wishlist');
	}


   /**
     * --------------------------------------------------------------------------
     * Function: get_index()
     * --------------------------------------------------------------------------
     *
     * Adds an item to the wishlist.
     *
     * @access   public
     * @param    string
     * @return   void
     */
	public function get_add( $id )
	{
        // Add the item to the wishlist.
        //
        //Wishlist::add( $product );

        // Redirect back to the cart home.
        //
        return Redirect::to('lara-cart/wishlist')->with('success', 'The product was added to your wishlist!');
	}
}