<div id="loading-overlay">
    <div class="spinner"></div>
</div>

    <div class="cart-container">
        <div class="cart-body">
            <div class="cart-items">

            </div>

            <div class="order-summary">
                <h2>Order Summary</h2>
                <!-- <div class="promo-code">
                    <input type="text" placeholder="Enter promo code">
                    <button class="btn-apply">Apply</button>
                </div> -->
                
                <div class="summary-totals">

                </div>
                
                <a href='index.php?page=checkout'><button class="btn-checkout">Proceed to Checkout</button></a>
                
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


    <div id="confirmModal" class="custom-modal">
		<div class="modal-content">
			<p id="confirmMessage"></p>
			<div class="modal-buttons">
				<button id="confirmYes" class="modal-btn yes-btn">Yes</button>
				<button id="confirmNo" class="modal-btn no-btn">No</button>
			</div>
		</div>
	</div>