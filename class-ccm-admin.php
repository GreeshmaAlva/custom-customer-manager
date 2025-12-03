<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Include WP_List_Table functionality needed for the admin list view (Requirement 5)
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
// Note: The actual CCM_Customer_List_Table class will be created in the next step.

class CCM_Customer_Admin {

    public function __construct() {
        // Add admin menu items (Requirement 5)
        add_action( 'admin_menu', array( $this, 'add_plugin_menu' ) );
        // Placeholder hook to handle form submission (logic added in Step 5)
        add_action( 'admin_init', array( $this, 'handle_actions' ) );
    }

    /**
     * Adds the top-level menu item for the Customer Manager.
     */
    public function add_plugin_menu() {
        add_menu_page(
            'Customer Manager',            // Page title
            'Customers',                   // Menu title
            'manage_options',              // Capability
            'custom-customer-manager',     // Menu slug
            array( $this, 'customers_page_handler' ), // Callback function
            'dashicons-groups',            // Icon
            20                             // Position
        );
    }

    /**
     * Handles the routing and display of all admin pages.
     */
    public function customers_page_handler() {
        $action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : 'list';
        $customer_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

        // Display success/error messages if present
        $this->display_admin_notices();

        switch ( $action ) {
            case 'add':
            case 'edit':
                $this->render_customer_form( $customer_id );
                break;
            case 'delete':
                // Deletion logic is handled in handle_actions
                $this->render_customer_list(); 
                break;
            case 'list':
            default:
                $this->render_customer_list();
                break;
        }
    }

    /**
     * Renders the Add/Edit form.
     */
    private function render_customer_form( $customer_id = 0 ) {
        $customer = null;
        if ( $customer_id > 0 ) {
            $customer = CCM_Customer_DB::get_customer( $customer_id ); 
        }
        // $this is available inside the included file to call calculate_age()
        include CCM_PLUGIN_PATH . 'admin/customer-form.php'; 
    }
    
    /**
     * Renders the customer list table (defined in customers-list.php).
     */
    private function render_customer_list() {
        include CCM_PLUGIN_PATH . 'admin/customers-list.php';
    }

    /**
     * Helper function to calculate Age (Requirement 1)
     */
    public function calculate_age( $dob_string ) {
        if ( empty( $dob_string ) || $dob_string === '0000-00-00' ) return 'N/A';
        try {
            // Calculated based on Date of Birth and the current month
            $dob = new DateTime( $dob_string );
            $now = new DateTime();
            return $now->diff( $dob )->y;
        } catch ( Exception $e ) {
            return 'Invalid Date';
        }
    }
    
    /**
     * Handles all admin actions (save, delete)
     */
    public function handle_actions() {
        // Handle Save/Update Form Submission
        if ( isset( $_POST['ccm_save_customer'] ) ) {
            $this->save_customer_data();
        }

        // Handle Delete Action from List View
        if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' ) {
            $this->delete_customer_data();
        }
    }
    /**
     * Handles form submissions for adding/editing a customer.
     * Implements Email Verification and User Creation (Requirement 4).
     */
    private function save_customer_data() {
        // 1. Check nonce and permission
        if ( ! isset( $_POST['ccm_customer_nonce'] ) || ! wp_verify_nonce( $_POST['ccm_customer_nonce'], 'ccm_save_customer_action' ) ) {
            wp_die( 'Security check failed.' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have permission to perform this action.' );
        }
        
        $customer_id = absint( $_POST['id'] ?? 0 );
        $is_new_customer = ( $customer_id == 0 );

        // 2. Sanitize and prepare data
        $customer_data = array(
            'name'      => sanitize_text_field( $_POST['name'] ),
            'email'     => sanitize_email( $_POST['email'] ),
            'phone'     => sanitize_text_field( $_POST['phone'] ),
            'dob'       => sanitize_text_field( $_POST['dob'] ),
            'gender'    => sanitize_text_field( $_POST['gender'] ),
            'cr_number' => sanitize_text_field( $_POST['cr_number'] ),
            'address'   => sanitize_textarea_field( $_POST['address'] ),
            'city'      => sanitize_text_field( $_POST['city'] ),
            'country'   => sanitize_text_field( $_POST['country'] ),
            'status'    => sanitize_text_field( $_POST['status'] ),
        );

        // Basic required field validation
        if ( empty( $customer_data['name'] ) || empty( $customer_data['email'] ) || empty( $customer_data['phone'] ) ) {
             wp_die( 'Error: Name, Email, and Phone are required fields.' );
        }

        if ( $is_new_customer ) {
            // --- NEW CUSTOMER LOGIC: EMAIL VERIFICATION & USER CREATION ---

            // 3. Email Verification (Requirement 4)
            if ( email_exists( $customer_data['email'] ) ) {
                wp_die( 'Error: Cannot add customer. This email is already associated with an existing WordPress user.' );
            }

            // 4. Insert Customer Record
            $inserted = CCM_Customer_DB::insert_customer( $customer_data );

            if ( $inserted ) {
                // 5. Create New WordPress User (Requirement 4)
                $username = sanitize_user( strtolower( str_replace( ' ', '', $customer_data['name'] ) ), true );
                $password = $customer_data['phone']; // Use phone number as password
                
                $user_id = wp_insert_user( array(
                    'user_login'   => $username,
                    'user_pass'    => $password,
                    'user_email'   => $customer_data['email'],
                    'display_name' => $customer_data['name'],
                    'role'         => 'contributor' // Set role to Contributor
                ) );

                if ( is_wp_error( $user_id ) ) {
                    // Log error but proceed, or display error if critical
                    // For now, we display a fatal error if WP user creation fails.
                    wp_die( 'Customer saved, but WordPress user creation failed: ' . $user_id->get_error_message() );
                }
                
                $redirect_message = 1; // Success message ID (Add)
            } else {
                wp_die( 'Error inserting customer into the database.' );
            }

        } else {
            // --- EDIT CUSTOMER LOGIC: UPDATE ---

            // Check if email is changing and if the new email exists as a WP user
            $old_customer = CCM_Customer_DB::get_customer( $customer_id );

            if ( $old_customer['email'] !== $customer_data['email'] && email_exists( $customer_data['email'] ) ) {
                 wp_die( 'Error: Cannot change email to one that already exists as a WordPress user.' );
            }
            
            // 6. Update Customer Record
            CCM_Customer_DB::update_customer( $customer_id, $customer_data );
            
            $redirect_message = 2; // Success message ID (Update)
        }

        // 7. Redirect back to the list page with success message
        wp_redirect( admin_url( 'admin.php?page=custom-customer-manager&message=' . $redirect_message ) );
        exit;
    }


    /**
     * Handles the deletion of a customer and their associated WP user.
     */
    private function delete_customer_data() {
        $customer_id = absint( $_GET['id'] ?? 0 );
        $nonce       = $_GET['ccm_delete_nonce'] ?? '';

        if ( ! $customer_id || ! wp_verify_nonce( $nonce, 'ccm_delete_customer_action_' . $customer_id ) ) {
            wp_die( 'Invalid delete request or nonce check failed.' );
        }

        // 1. Get customer info to find the associated WP user email
        $customer = CCM_Customer_DB::get_customer( $customer_id );
        
        if ( $customer ) {
            $user = get_user_by( 'email', $customer['email'] );

            // 2. Delete the associated WordPress user (if found)
            if ( $user && $user->ID !== get_current_user_id() ) { // Do not allow deleting the current admin user!
                require_once( ABSPATH . 'wp-admin/includes/user.php' );
                wp_delete_user( $user->ID );
            }

            // 3. Delete the customer record from the custom table
            CCM_Customer_DB::delete_customer( $customer_id );
        }

        // 4. Redirect with success message
        wp_redirect( admin_url( 'admin.php?page=custom-customer-manager&message=3' ) );
        exit;
    }

    /**
     * Displays admin notices (e.g., success messages after save/delete).
     */
    private function display_admin_notices() {
        if ( isset( $_GET['message'] ) ) {
            $message = absint( $_GET['message'] );
            if ( $message === 1 ) {
                echo '<div class="notice notice-success is-dismissible"><p>Customer added successfully and WordPress user created.</p></div>';
            } elseif ( $message === 2 ) {
                echo '<div class="notice notice-success is-dismissible"><p>Customer updated successfully.</p></div>';
            } elseif ( $message === 3 ) {
                echo '<div class="notice notice-success is-dismissible"><p>Customer deleted successfully.</p></div>';
            }
        }
    }
}