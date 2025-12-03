# Custom Customer Manager Plugin

## Project Overview
This WordPress plugin manages customer data in a separate, custom database table (`wp_ccm_customers`). It automatically integrates with WordPress user management, creating a 'contributor' user for every new customer entry, and provides front-end display via a shortcode.

## Requirements Met
This plugin successfully addresses all specified requirements:
1. **Customer Fields & Age Calculation:** All required fields (Name, Email, DOB, Gender, CR Number, Address, City, Country, Status) are implemented. Age is calculated from Date of Birth in the admin list view.
2. **Custom Database Table:** A dedicated table, `wp_ccm_customers`, is created upon plugin activation.
3. **Full CRUD Operations:** All customer data can be C/R/U/D (Create, Read, Update, Delete) from the custom Admin interface.
4. **WP User Creation & Verification:** When a customer is added, a new WordPress user (Role: Contributor; Password: Phone Number) is created. The system prevents adding customers with existing WordPress emails.
5. **Admin Interface:** Implements a custom top-level admin menu and uses the built-in WordPress List Table API (`WP_List_Table`).
6. **Search & Pagination:** Search functionality (by Name, Email, CR Number) and pagination framework are included in the admin list view.
7. **Front-End Shortcode:** The shortcode `[ccm_customers]` displays a read-only list of all **Active** customers on any front-end page.

## Configuration and Setup Instructions

### 1. Code Placement
1. Clone the repository into your WordPress plugins directory: `wp-content/plugins/`
2. Ensure all code functions are well-commented, maintaining a structured and readable code format.

### 2. Database Setup (Crucial for Review)
The table structure is automatically created upon activation. To quickly populate data for testing:
1. Access your database management tool (e.g., Adminer, phpMyAdmin).
2. Select your existing WordPress database.
3. Import the provided SQL dump file: `db-dump/ccm_customers_dump.sql`

### 3. Plugin Activation and Verification
1.  Navigate to **WordPress Admin -> Plugins** and **Activate** the "Custom Customer Manager" plugin.
2.  **Admin Test:** Navigate to the **Customers** menu item. Add a new customer to verify the **WP User** is created (check **Users -> All Users**).
3.  **Front-End Test:** Create a new page and insert the shortcode: `[ccm_customers]`. Publish and view the page to verify the active customer list appears.
