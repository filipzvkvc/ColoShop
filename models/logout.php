<?php
    session_unset();
    session_destroy();

    header('Location: https://coloshop.infinityfreeapp.com/index.php?page=home');

    exit;
?>