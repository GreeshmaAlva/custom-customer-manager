<div class="wrap">
    <h1>Customers 
        <a href="<?php echo admin_url('admin.php?page=custom-customer-manager&action=add'); ?>" class="page-title-action">Add New Customer</a>
    </h1>

    <form method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php 
            // Instantiate the list table class
            $list_table = new CCM_Customer_List_Table();
            
            // Fetch and prepare items
            $list_table->prepare_items();
            
            // Display the search box (Requirement 6)
            $list_table->search_box('Search Customers', 'customer-search-id');

            // Display the table itself
            $list_table->display();
        ?>
    </form>
</div>
<style>
/* Simple CSS for status display */
.status-active { color: green; font-weight: bold; }
.status-inactive { color: red; }
</style>