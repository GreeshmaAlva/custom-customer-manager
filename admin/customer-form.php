<div class="wrap">
    <h1><?php echo $customer ? 'Edit Customer' : 'Add New Customer'; ?></h1>
    <a href="<?php echo admin_url('admin.php?page=custom-customer-manager'); ?>" class="page-title-action">Back to List</a>
    <hr>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'ccm_save_customer_action', 'ccm_customer_nonce' ); ?>
        <input type="hidden" name="id" value="<?php echo absint( $customer['id'] ?? 0 ); ?>">
        <input type="hidden" name="action" value="<?php echo $customer ? 'ccm_update' : 'ccm_add'; ?>">

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="name">Name <span class="description">(required)</span></label></th>
                    <td><input name="name" type="text" id="name" value="<?php echo esc_attr( $customer['name'] ?? '' ); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="email">Email <span class="description">(required)</span></label></th>
                    <td><input name="email" type="email" id="email" value="<?php echo esc_attr( $customer['email'] ?? '' ); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="phone">Phone Number <span class="description">(required, numeric only)</span></label></th>
                    <td><input name="phone" type="text" pattern="[0-9]*" id="phone" value="<?php echo esc_attr( $customer['phone'] ?? '' ); ?>" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dob">Date of Birth</label></th>
                    <td><input name="dob" type="date" id="dob" value="<?php echo esc_attr( $customer['dob'] ?? '' ); ?>" class="regular-text">
                        <?php if ( $customer ): ?>
                            <p class="description">Current Age: <strong><?php echo $this->calculate_age( $customer['dob'] ); ?></strong></p> 
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gender">Gender</label></th>
                    <td>
                        <select name="gender" id="gender">
                            <option value="Male" <?php selected( $customer['gender'] ?? '', 'Male' ); ?>>Male</option>
                            <option value="Female" <?php selected( $customer['gender'] ?? '', 'Female' ); ?>>Female</option>
                            <option value="Other" <?php selected( $customer['gender'] ?? '', 'Other' ); ?>>Other</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cr_number">CR Number <span class="description">(alphanumeric)</span></label></th>
                    <td><input name="cr_number" type="text" id="cr_number" value="<?php echo esc_attr( $customer['cr_number'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="address">Address</label></th>
                    <td><textarea name="address" id="address" rows="3" cols="50" class="large-text"><?php echo esc_textarea( $customer['address'] ?? '' ); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="city">City</label></th>
                    <td><input name="city" type="text" id="city" value="<?php echo esc_attr( $customer['city'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="country">Country</label></th>
                    <td><input name="country" type="text" id="country" value="<?php echo esc_attr( $customer['country'] ?? '' ); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">Status</label></th>
                    <td>
                        <select name="status" id="status">
                            <option value="active" <?php selected( $customer['status'] ?? 'active', 'active' ); ?>>Active</option>
                            <option value="inactive" <?php selected( $customer['status'] ?? '', 'inactive' ); ?>>Inactive</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button( 'Save Customer', 'primary', 'ccm_save_customer' ); ?>
    </form>
</div>