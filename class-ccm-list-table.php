<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// We already require WP_List_Table in class-ccm-admin.php, so we can extend it here.

class CCM_Customer_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
            'singular' => 'customer',
            'plural'   => 'customers',
            'ajax'     => false
        ) );
    }

    /**
     * Defines the columns for the table.
     */
    public function get_columns() {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name'      => 'Name',
            'email'     => 'Email',
            'phone'     => 'Phone',
            'dob'       => 'Date of Birth',
            'age'       => 'Age',      // Calculated field
            'cr_number' => 'CR Number',
            'city'      => 'City',
            'status'    => 'Status'
        );
        return $columns;
    }

    /**
     * Define which columns are sortable (optional, but good practice)
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'name'      => array( 'name', false ),
            'email'     => array( 'email', false ),
            'status'    => array( 'status', false )
        );
        return $sortable_columns;
    }

    /**
     * Handles the display of the data for each column.
     * We calculate Age here (Requirement 1)
     */
    public function column_default( $item, $column_name ) {
        // Need to instantiate the Admin class temporarily to use the calculate_age helper
        static $admin_class = null;
        if ( is_null( $admin_class ) ) {
            $admin_class = new CCM_Customer_Admin();
        }

        switch ( $column_name ) {
            case 'age':
                return $admin_class->calculate_age( $item['dob'] );
            case 'dob':
                return date( 'F j, Y', strtotime( $item['dob'] ) );
            case 'status':
                $class = $item['status'] === 'active' ? 'status-active' : 'status-inactive';
                return '<span class="' . $class . '">' . ucfirst( $item['status'] ) . '</span>';
            default:
                return $item[ $column_name ];
        }
    }

    /**
     * Displays the content for the 'name' column, including row actions (Edit/Delete).
     */
    public function column_name( $item ) {
        $edit_url = admin_url( 'admin.php?page=custom-customer-manager&action=edit&id=' . $item['id'] );
        $delete_url = wp_nonce_url( 
            admin_url( 'admin.php?page=custom-customer-manager&action=delete&id=' . $item['id'] ),
            'ccm_delete_customer_action_' . $item['id'],
            'ccm_delete_nonce'
        );

        $actions = array(
            'edit'   => sprintf( '<a href="%s">Edit</a>', esc_url( $edit_url ) ),
            'delete' => sprintf( '<a href="%s" onclick="return confirm(\'Are you sure you want to delete this customer? This will also delete the associated WordPress user.\')">Delete</a>', esc_url( $delete_url ) ),
        );

        return sprintf( '<strong><a href="%s">%s</a></strong> %s', $edit_url, $item['name'], $this->row_actions( $actions ) );
    }

    /**
     * Prepares the item list for display, including pagination and search. (Requirement 6)
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page     = 10;
        $current_page = $this->get_pagenum();
        $search       = $_REQUEST['s'] ?? ''; // Search term

        // Fetch data using the CCM_Customer_DB function
        $data_result = CCM_Customer_DB::get_customers( $per_page, $current_page, $search );

        $this->items = $data_result['results'];
        
        // Set up pagination data
        $this->set_pagination_args( array(
            'total_items' => $data_result['total'],
            'per_page'    => $per_page,
            'total_pages' => ceil( $data_result['total'] / $per_page )
        ) );
    }

    /**
     * Message to display when no customer data is found.
     */
    public function no_items() {
        echo 'No customers found. Click "Add New Customer" to get started.';
    }
}