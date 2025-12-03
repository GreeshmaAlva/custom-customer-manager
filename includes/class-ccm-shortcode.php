<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles the registration and rendering of the [ccm_customers] shortcode.
 */
class CCM_Customer_Shortcode {

    public function __construct() {
        // Register the shortcode
        add_shortcode( 'ccm_customers', array( $this, 'render_customer_list' ) );
    }

    /**
     * Renders the front-end read-only customer list.
     */
    public function render_customer_list( $atts ) {
        // Only fetch active customers for the front-end (a good security/privacy practice)
        $data_result = CCM_Customer_DB::get_customers( 100, 1 ); // Fetching up to 100 for simplicity

        if ( empty( $data_result['results'] ) ) {
            return '<p>No customer records are currently available.</p>';
        }

        $output = '<div class="ccm-customer-list-frontend">';
        $output .= '<h2>Registered Customers</h2>';
        $output .= '<style>
            .ccm-customer-list-frontend table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            .ccm-customer-list-frontend th, .ccm-customer-list-frontend td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .ccm-customer-list-frontend th { background-color: #f2f2f2; }
            .ccm-customer-list-frontend .status-active { color: green; font-weight: bold; }
        </style>';
        $output .= '<table>';
        $output .= '<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>CR Number</th><th>City, Country</th></tr></thead>';
        $output .= '<tbody>';

        foreach ( $data_result['results'] as $customer ) {
            // Only show ACTIVE customers on the front-end
            if ( $customer['status'] !== 'active' ) {
                continue;
            }
            
            // Format phone number to hide part of it for privacy (optional, but recommended)
            $phone_display = substr( $customer['phone'], 0, 3 ) . 'XXX' . substr( $customer['phone'], -4 );
            
            $output .= '<tr>';
            $output .= '<td>' . esc_html( $customer['name'] ) . '</td>';
            $output .= '<td>' . esc_html( $customer['email'] ) . '</td>';
            $output .= '<td>' . esc_html( $phone_display ) . '</td>';
            $output .= '<td>' . esc_html( $customer['cr_number'] ) . '</td>';
            $output .= '<td>' . esc_html( $customer['city'] . ', ' . $customer['country'] ) . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</tbody>';
        $output .= '</table>';
        $output .= '</div>';

        return $output;
    }

    // Helper function used by the front-end shortcode to calculate age
    private function calculate_age( $dob_string ) {
        if ( empty( $dob_string ) || $dob_string === '0000-00-00' ) return 'N/A';
        try {
            $dob = new DateTime( $dob_string );
            $now = new DateTime();
            return $now->diff( $dob )->y;
        } catch ( Exception $e ) {
            return 'Invalid Date';
        }
    }
}