<?php

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
        global $wpdb;

        $taxonomies = get_taxonomies( $args = array(), $output = 'objects' );

        foreach ( $taxonomies as $slug => $tax_object ) {

            // Load values for each taxonomy
            $sql = "
            SELECT t.term_id, t.name, t.slug, tt.term_taxonomy_id, tt.description, tt.parent
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id AND tt.taxonomy = '$slug'
            ";
            $results = $wpdb->get_results( $sql );

            $items["tax_$slug"] = array(
                'value'     => json_encode( $results ),
                'label'     => $tax_object->labels->name,
                'group'     => 'Taxonomy Terms',
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
