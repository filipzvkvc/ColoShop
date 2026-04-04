<?php
    $token = get('token');
?>

<div style="font-family: Arial; text-align: center; margin-top: 100px;">
    <h2>Are you sure you want to unsubscribe?</h2>
    <p>You will stop receiving all future newsletters from ColoShop.</p>

    <a href="https://coloshop.infinityfreeapp.com/index.php?page=unsubscribeNewsletter&token=<?= urlencode($token) ?>" 
       class="unsubscribe-btn">
        Yes, unsubscribe me
    </a>

    <a href="https://coloshop.infinityfreeapp.com/index.php?page=home"
       class="unsubscribe-btn cancel-btn">
        No, keep me subscribed
    </a>
</div>

<style>
.unsubscribe-btn {
    display: inline-block;
    padding: 10px 25px;
    font-size: 16px;
    background-color: #fe4c50;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    margin: 10px 5px;
    transition: background 0.3s, color 0.3s;
}

.unsubscribe-btn:hover {
    background-color: #2b2b34;
    color: #ffffff;
}

.cancel-btn {
    background-color: #777;
}

.cancel-btn:hover {
    background-color: #2b2b34;
    color: #ffffff;
}
</style>

