<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles all database operations for the Custom Customer Manager plugin.
 */
class CCM_Customer_DB {

    private static $table_name;

    /**
     * Gets the full name of the custom database table.
     */
    public static function get_table_name() {
        global $wpdb;
        if ( ! self::$table_name ) {
            // Table name: wp_ccm_customers
            self::$table_name = $wpdb->prefix . 'ccm_customers';
        }
        return self::$table_name;
    }

    /**
     * Creates the custom database table upon plugin activation. (Requirement 2)
     */
    public static function create_table() {
        global $wpdb;
        $table_name = self::get_table_name();
        $charset_collate = $wpdb->get_charset_collate();

        // SQL definition for the customer table (Requirement 1)
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL UNIQUE,
            phone varchar(20) NOT NULL,
            dob date NOT NULL,          
            gender varchar(10) NOT NULL,
            cr_number varchar(20) NOT NULL,
            address text NOT NULL,
            city tinytext NOT NULL,
            country tinytext NOT NULL,
            status tinytext NOT NULL,   -- 'active' or 'inactive'
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Required library for the dbDelta function
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        }
        
        dbDelta( $sql );
    }

    // --- CRUD FUNCTIONS (Requirement 3) ---

    /**
     * Creates/Insert a new customer record.
     */
    public static function insert_customer( $data ) {
        global $wpdb;
        $table_name = self::get_table_name();
        
        // Data format definition (used by $wpdb->insert/update)
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'); 
        
        return $wpdb->insert( $table_name, $data, $format );
    }

    /**
     * Reads/Gets a single customer record by ID.
     */
    public static function get_customer( $id ) {
        global $wpdb;
        $table_name = self::get_table_name();
        $id = absint( $id );
        if ( ! $id ) return null;
        
        return $wpdb->get_row( 
            $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), 
            ARRAY_A 
        );
    }

    /**
     * Gets all customer records for the admin list, supporting search and pagination. (Requirement 6)
     */
    public static function get_customers( $per_page = 20, $current_page = 1, $search = '' ) {
        global $wpdb;
        $table_name = self::get_table_name();
        $offset = ( $current_page - 1 ) * $per_page;
        $where = 'WHERE 1=1';
        
        if ( ! empty( $search ) ) {
            $search = '%' . $wpdb->esc_like( $search ) . '%';
            $where .= $wpdb->prepare( 
                " AND (name LIKE %s OR email LIKE %s OR cr_number LIKE %s)", 
                $search, 
                $search, 
                $search 
            );
        }

        $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name $where" );

        $results = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT * FROM $table_name $where ORDER BY name ASC LIMIT %d OFFSET %d", 
                $per_page, 
                $offset 
            ), 
            ARRAY_A
        );

        return array(
            'results' => $results,
            'total'   => $total_items,
        );
    }

    /**
     * Updates an existing customer record.
     */
    public static function update_customer( $id, $data ) {
        global $wpdb;
        $table_name = self::get_table_name();
        $id = absint( $id );
        if ( ! $id ) return false;
        
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'); 
        
        return $wpdb->update( 
            $table_name, 
            $data, 
            array( 'id' => $id ), 
            $format,
            array( '%d' )
        );
    }

    /**
     * Deletes a customer record.
     */
    public static function delete_customer( $id ) {
        global $wpdb;
        $table_name = self::get_table_name();
        $id = absint( $id );
        if ( ! $id ) return false;
        
        return $wpdb->delete( 
            $table_name, 
            array( 'id' => $id ), 
            array( '%d' )
        );
    }
}