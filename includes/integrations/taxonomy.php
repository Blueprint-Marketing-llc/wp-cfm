<?php

return;

class WPCFM_Taxonomy
{
    /**
     * Filters for displaying the Taxonomy settings in admin
     * and pulling in taxonomy data
     */
    function __construct() {
        add_filter( 'wpcfm_configuration_items', array( $this, 'get_configuration_items' ) );
        add_filter( 'wpcfm_pull_callback', array( $this, 'pull_callback' ), 10, 2 );
    }


    /**
     * Handler for wpcfm_configuration_items
     */
    function get_configuration_items( $items ) {
        $taxonomies = get_taxonomies( $args = array(), $output = 'objects' );

        foreach ( $taxonomies as $slug => $tax_object ) {
            $items["tax_$slug"] = array(
                'value'     => '',
                'label'     => $tax_object->labels->name,
                'group'     => 'Taxonomies',
                'callback'  => array( $this, 'pull_callback' ),
            );
        }

        return $items;
    }


    /**
     * Handler for wpcfm_pull_callback
     */
    function pull_callback( $callback, $callback_params ) {

    }
}

new WPCFM_Taxonomy();
