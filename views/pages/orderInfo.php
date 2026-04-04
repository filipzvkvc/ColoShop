<?php
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user'];
	}

    $orderId = get('id');

    $orderInformation = getOrderInformation($orderId);

    $shippingMethods = getAllFromTable('shipping_method');

    $cities = getAllFromTable('city');
    $countries = getAllFromTable('country');

    $orderStatus = getAllFromTable('order_status');

?>
<!-- Loading overlay -->
<div id="loading-overlay">
    <div class="spinner"></div>
</div>

    <div class="checkout-container">
        <div class="checkout-body">
            <div class="shipping-form">
                <div class="checkout-header">
                    <h2>Order Information</h2>

                    <p class='order_number' style="font-weight: bold;">Order number: <?=$orderInformation->orderId?></p>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label> <p><?=$orderInformation->email?></p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first-name">First Name:</label> <p><?=$orderInformation->first_name?></p>
                    </div>
                    <div class="form-group">
                        <label for="last-name">Last Name:</label><p><?=$orderInformation->last_name?></p>
                    </div>
                </div>
                

                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Street name:</label><p><?=$orderInformation->street_name?></p>
                    </div>

                    <div class="form-group">
                        <label for="address">Street number:</label><p><?=$orderInformation->street_number?></p>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <?php
                            foreach($countries as $co){
                                foreach($cities as $ci){
                                    if($orderInformation->id_city == $ci->id_city){
                                        $countryId = $ci->id_country;
                                    }
                                }

                                if($co->id_country == $countryId){
                                    echo '<p>'.$co->name.'</p>';
                                }
                            }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <?php
                            foreach($cities as $ci){
                                if($ci->id_city == $orderInformation->id_city){
                                    echo '<p>'.$ci->name.'</p>';
                                }
                            }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="zip">ZIP Code:</label>
                        <?php
                            foreach($cities as $ci){
                                if($ci->id_city == $orderInformation->id_city){
                                    echo '<p>'.$ci->zip_code.'</p>';
                                }
                            }
                        ?>
                    </div>
                </div>

                
                <div class="form-group">
                    <label for="phone">Phone number:</label><p><?=$orderInformation->phone_number?></p>
                </div>

                <div class="form-group">
                    <label for="phone">Order status :</label>
                    <?php
                        foreach($orderStatus as $os){
                            if($os->id_order_status == $orderInformation->id_order_status){
                                echo '<span class="btn btn-sm ' . getOrderStatusClass($os->name) . '" style="pointer-events: none; cursor: default;">' . $os->name . '</span>';
                            }
                        }
                    ?>
                </div>

                <div class="form-group">
                    <label for="phone">Order date:</label><p><?= date('j F Y H:i', strtotime($orderInformation->date)); ?></p>
                </div>
                
                <div class="form-group shipping-method">
                    <label>Shipping Method:</label>
                    <?php
                        foreach($shippingMethods as $sm){
                            if($sm->id_shipping_method == $orderInformation->id_shipping_method){
                                $priceDisplay = ($sm->price == 0) ? 'Free' : '$' . number_format($sm->price, 2);
                                    echo '<p>' . $sm->name . ' Shipping' . ' (Duration: ' . $sm->min_days . '-' . $sm->max_days . ' business days, Price: ' . $priceDisplay . ')</p>';
                            }
                        }
                    ?>
                </div>
            </div>
            
            <div class="checkout-sidebar">

                <div class="order-items">
                   
                </div>
 

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="summary-totals">
                        <div class="total-row">

                            <?php
                                $orderItems = getOrderItems($orderId);

                                $itemsCount = 0;

                                foreach($orderItems as $oi){
                                    $itemsCount += (int)$oi->quantity;
                                }

                                $itemText = $itemsCount == 1 ? ' item' : ' items';

                                $shippingPrice = getOrderShippingPrice($orderId);
                                $orderTotal = getOrderTotalPrice($orderId);

                                $priceWithoutShipping = $orderTotal - (float)$shippingPrice->shippingPrice;

                                echo '<span>' . 'Subtotal (' . $itemsCount . $itemText . ')'. '</span>';
                                echo '<span>' . '$'. number_format($priceWithoutShipping, 2) . '</span>';
                            ?>
                        </div>
                        <?php
                            foreach ($shippingMethods as $sm) {
                                if ($sm->id_shipping_method == $orderInformation->id_shipping_method) {
                                    $priceDisplay = ($sm->price == 0) ? 'Free' : '$' . number_format($sm->price, 2);
                                    echo '<div class="total-row">';
                                    echo '<span>Shipping</span>';
                                    echo '<span>' . $priceDisplay . ' (' . htmlspecialchars($sm->name) . ' Shipping'. ')</span>';
                                    echo '</div>';
                                }
                            }
                        ?>
                        <div class="total-row grand-total">
                            <span>Total</span>

                            <?php
                                $totalPrice = getOrderTotalPrice($orderId);

                                echo '<span>' . '$' . number_format($totalPrice,2) . '</span>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>