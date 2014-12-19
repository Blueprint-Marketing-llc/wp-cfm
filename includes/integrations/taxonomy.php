<?php

class WPCFM_Taxonomy
{
    /**
     * Filters for displaying the Taxonomy settings in admin
     * and pulling in taxonomy data
     */
    function __construct() {
        add_filter( 'wpcfm_configuration_items', array( $this, 'get_configuration_items' ) );
        //add_filter( 'wpcfm_pull_callback', array( $this, 'pull_callback' ), 10, 2 );
    }


    /**
     * Handler for wpcfm_configuration_items
     */
    function get_configuration_items( $items ) {
        global $wpdb;

        $taxonomies = get_taxonomies( $args = array(), $output = 'objects' );

        foreach ( $taxonomies as $slug => $tax_object ) {

            $term_lookup = get_terms( $slug, array( 'hide_empty' => false, 'fields' => 'id=>slug' ) );

            // Load values for each taxonomy
            $sql = "
            SELECT t.name, t.slug, tt.description, tt.parent FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id AND tt.taxonomy = '$slug'";
            $results = $wpdb->get_results( $sql, ARRAY_A );


            // Replace "parent" with the term slug (instead of ID)
            foreach ( $results as $key => $result ) {
                if ( 0 < (int) $result['parent'] ) {
                    $results[ $key ]['parent'] = $term_lookup[ $result['parent'] ];
                }
            }


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
     * Import (overwrite) taxonomy terms into DB
     * @param string $params['name']
     * @param string $params['group']
     * @param string $params['old_value'] The previous settings data
     * @param string $params['new_value'] The new settings data
     */
    function pull_callback( $params ) {
        $old_value = json_decode( $params['old_value'], true );
        $new_value = json_decode( $params['new_value'], true );
    }
}

new WPCFM_Taxonomy();
