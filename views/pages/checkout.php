<?php
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
	}

    $shippingMethods = getAllFromTable('shipping_method');
    $countries = getAllFromTable('country');
?>
<!-- Loading overlay -->
<div id="loading-overlay">
    <div class="spinner"></div>
</div>

    <div class="checkout-container">
        <div class="checkout-body">
            <div class="shipping-form">
                <div class="checkout-header">
                    <h2>Shipping Information</h2>
                </div>
                <form method='post'>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" value="<?= isset($user->email) ? $user->email : ''?>" id="email" class='form_input input_ph' placeholder="your@email.com">
						<span id="error_email" class="error-message"></span>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first-name">First Name</label>
                            <input type="text" value="<?= isset($user->first_name) ? $user->first_name : ''?>" id="first_name" class='form_input input_ph' placeholder="John">
						    <span id="error_first_name" class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="last-name">Last Name</label>
                            <input type="text" value="<?= isset($user->last_name) ? $user->last_name : ''?>" id="last_name" class='form_input input_ph' placeholder="Doe">
						    <span id="error_last_name" class="error-message"></span>
                        </div>
                    </div>
                    

                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Street name</label>
                            <input type="text" id="street_name" class='form_input input_ph' placeholder="Vojislava Ilica">
                            <span id="error_street_name" class="error-message"></span>
                        </div>

                        <div class="form-group">
                            <label for="address">Street number</label>
                            <input type="text" id="street_number" class='form_input input_ph' placeholder="20">
                            <span id="error_street_number" class="error-message"></span>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <select id="country" name="country" class='form_input input_ph'>
                                <option value="">Select country</option>
                                <?php
                                    foreach ($countries as $country) {
                                        echo "<option value='{$country->id_country}'>{$country->name}</option>";
                                    }
                                ?>
                            </select>
                        <span id="error_country" class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <select id="city" disabled class='form_input input_ph'>
                                <option value="">Select city</option>
                            </select>
                        <span id="error_city" class="error-message"></span>
                        </div>
                        <div class="form-group">
                            <label for="zip">ZIP Code</label>
                            <input type="text" id="zip" placeholder="ZIP Code" readonly disabled>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label for="phone">Phone number</label>
                        <input type="tel" id="phone_number" class='form_input input_ph' placeholder="(123) 456-7890">
                        <span id="error_phone_number" class="error-message"></span>
                    </div>
                    
                    <div class="form-group shipping-method">
                        <h3>Shipping Method</h3>

                    <div class="radio-group">
                        <?php
                            foreach ($shippingMethods as $index => $method) {
                                $checked = ($index === 0) ? 'checked' : '';

                                $priceDisplay = ($method->price == 0) ? 'Free' : '$' . number_format($method->price, 2);

                                echo "<label class='radio-option'>
                                        <input type='radio' name='shipping' value='{$method->id_shipping_method}' $checked>
                                        <div class='radio-content'>
                                            <span class='radio-title'>{$method->name}</span>
                                            <span class='radio-desc'>{$method->min_days}-{$method->max_days} business days</span>
                                        </div>
                                        <span class='radio-price'>{$priceDisplay}</span>
                                    </label>";
                            }
                        ?>
                    </div>
                        
                    </div>
                    
                    <button id="insert_order" type="button" class="btn-cart">Continue to Payment</button>
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-totals">

                </div>


                <a href='index.php?page=cart'><button class="btn-cart">Back to Cart</button></a>
                
           
                
                <div class="payment-methods">
                    <div class="payment-method">
                        <i class="fab fa-cc-visa"></i>
                    </div>
                    <div class="payment-method">
                        <i class="fab fa-cc-mastercard"></i>
                    </div>
                    <!-- <div class="payment-method">
                        <i class="fab fa-cc-amex"></i>
                    </div> -->
                    <div class="payment-method">
                        <i class="fab fa-cc-paypal"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Payment Modal -->
<div id="payment-modal" class="payment-modal">
    <div class="payment-modal-content">
        <button id="close-payment" class="payment-close">&times;</button>

        <h2>Complete Your Payment</h2>
        <p class="payment-subtitle">Secure checkout</p>

        <div id="paypal-button-container"></div>
    </div>
</div>


<script src="https://www.paypal.com/sdk/js?client-id=<?= PAYPAL_CLIENT_ID ?>&currency=USD"></script>