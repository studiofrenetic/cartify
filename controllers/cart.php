<?php

## This file is for the examples only!

class Lara_Cart_Cart_Controller extends Controller
{
    // Make it RESTful
    //
    public $restful = true;

    // Declare our static products, this is just for the example !!!
    //
    public $products = array(
        'sku_123ABC' => array(
            'id'      => 'sku_123ABC',
            'price'   => 39.95,
            'name'    => 'T-Shirt',
            'image'   => 'tshirt.jpg',
            'options' => array(
                'Size'  => array(
                    'S' => 'S', 
                    'M' => 'M',
                    'L' => 'L'
                ), 
                'Color' => array(
                    'Red'    => 'Red',
                    'Blue'   => 'Blue',
                    'Yellow' => 'Yellow',
                    'White'  => 'White'
                ),
                'Style' => array(
                    'Unisex' => 'Unisex',
                    'Womens' => 'Womens'
                )
            )
        ),
        'sku_567ZYX' => array(
            'id'      => 'sku_567ZYX',
            'price'   => 9.95,
            'name'    => 'Coffee Mug',
            'image'   => 'coffee_mug.jpg',
            'options' => array(
                'Design' => array(
                    'Design 1'    => 'Design 1',
                    'Design XpT0' => 'Design XpT0'
                )
            )
        ),
        'sku_965QRS' => array(
            'id'      => 'sku_965QRS',
            'price'   => 29.95,
            'name'    => 'Shot Glass',
            'image'   => 'shot_glass.jpg'
        )
    );



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
        return View::make('lara-cart::home');
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
            Cart::update( $items );

            // Redirect back to the cart home.
            //
            return Redirect::to('cart')->with('success', 'Your shopping cart was updated.');
        // If we are emptying the cart.
        //
        elseif ( Input::get('empty') ):
            Cart::destroy();

            // Redirect back to the cart home.
            //
            return Redirect::to('cart')->with('warning', 'Your shopping cart was cleared !');
        endif;

    }


   /**
     * --------------------------------------------------------------------------
     * Function: get_products()
     * --------------------------------------------------------------------------
     *
     * Show a list of the products on the page.
     *
     * @access   public
     * @return   void
     */
    public function get_products()
    {
        // Show the page.
        //
        return View::make('lara-cart::products')->with('products', $this->products);
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
        // Retrieve some data.
        //
        $item_id = Input::get('item_id');
        $qty     = Input::get('qty');
        $options = Input::get('options', array());

        // Get the product information.
        //
        $info = $this->products[ $item_id ];

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
        Cart::add( $product );

        // Redirect back to the cart home.
        //
        return Redirect::to('cart')->with('success', 'Product was added to the shopping cart!');
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
        Cart::remove( $item_id );

        // Redirect back to the cart home.
        //
        return Redirect::to('cart')->with('warning', 'The item was removed from the shopping cart');
    }
}