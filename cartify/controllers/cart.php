<?php

## This file is for the examples only!

use Cartify\Models\Products;

class Cartify_Cart_Controller extends Controller
{
    // Make it RESTful
    //
    public $restful = true;


    /**
     * --------------------------------------------------------------------------
     * Function: get_index()
     * --------------------------------------------------------------------------
     *
     * Shows the cart contents.
     *
     * @access   public
     * @return   void
     */
    public function get_index()
    {
        // Show the page.
        //
        return View::make('cartify::cart');
    }


    /**
     * --------------------------------------------------------------------------
     * Function: post_index()
     * --------------------------------------------------------------------------
     *
     * Updates or empties the cart contents.
     *
     * @access   public
     * @return   void
     */
    public function post_index()
    {
        // If we are updating the quantities.
        //
        if ( Input::get('update') ):
            // Get the items to be updated.
            //
            $items = array();
            foreach( Input::get('items') as $item_id => $qty ):
                $items[] = array('rowid' => $item_id, 'qty'=> $qty);
            endforeach;

            // Update the cart contents.
            //
            Cartify::cart()->update( $items );

            // Redirect back to the cart home.
            //
            return Redirect::to('cartify/cart')->with('success', 'Your shopping cart was updated.');
        // If we are emptying the cart.
        //
        elseif ( Input::get('empty') ):
            Cartify::cart()->destroy();

            // Redirect back to the cart home.
            //
            return Redirect::to('cartify/cart')->with('warning', 'Your shopping cart was cleared !');
        endif;
    }


   /**
     * --------------------------------------------------------------------------
     * Function: post_add()
     * --------------------------------------------------------------------------
     *
     * Adds a product to the shopping cart.
     *
     * @access   public
     * @return   void
     */
    public function post_add()
    {
        $products = Products::get_list();

        // Retrieve some data.
        //
        $item_id = Input::get('item_id');
        $qty     = Input::get('qty');
        $options = Input::get('options', array());

        // Get the product information.
        //
        $info = $products[ $item_id ];

        // Add the qty to the product information.
        //
        $product = array(
            'id'      => $info['id'],
            'qty'     => $qty,
            'price'   => $info['price'],
            'name'    => $info['name'],
            'image'    => $info['image'],
            'options' => $options
        );

        // Add the item to the cart.
        //
        Cartify::cart()->add( $product );

        // Redirect back to the cart home.
        //
        return Redirect::to('cartify/cart')->with('success', 'Product was added to the shopping cart!');
    }


   /**
     * --------------------------------------------------------------------------
     * Function: get_remove()
     * --------------------------------------------------------------------------
     *
     * Removes an item from the shopping cart.
     *
     * @access   public
     * @param    string
     * @return   void
     */
    public function get_remove( $item_id = null )
    {
        // Remove the item from the cart.
        //
        Cartify::cart()->remove( $item_id );

        // Redirect back to the cart home.
        //
        return Redirect::to('cartify/cart')->with('warning', 'The item was removed from the shopping cart');
    }
}
