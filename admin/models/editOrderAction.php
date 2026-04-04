<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idOrder = post('id_order');
        $orderInformation = getOrderInformation($idOrder);

        $email = post('email');
        $firstName = post('first_name');
        $lastName = post('last_name');

        $streetName = post('street_name');
        $streetNumber = post('street_number');

        $id_city = (int)post('id_city');

        $phoneNumber = post('phone_number');

        $id_shipping_method = (int)post('id_shipping_method');
        $id_order_status = (int)post('id_order_status');


        $_SESSION['edit_order_form_data'] = new stdClass();


        if(empty($email)){
            $email = $orderInformation->email;
        }
        else{
            if (!testFields('email', $email)) {
                setFlash('email', messageText('email'));
            }

            $_SESSION['edit_order_form_data']->email = $email;
        }

        if(empty($firstName)){
            $firstName = $orderInformation->first_name;
        }
        else
        {
            if (!testFields('name', $firstName)) {
                setFlash('first_name', messageText('firstName'));
            }

            $_SESSION['edit_order_form_data']->first_name = $firstName;
        }


        if(empty($lastName)){
            $lastName = $orderInformation->last_name;
        }
        else{
            if (!testFields('name', $lastName)) {
                setFlash('last_name', messageText('lastName'));
            }

            $_SESSION['edit_order_form_data']->last_name = $lastName;
        }



        if(empty($streetName)){
            $streetName = $orderInformation->street_name;
        }
        else{
            if (!testFields('streetName', $streetName)) {
                setFlash('street_name', messageText('streetName'));
            }

            $_SESSION['edit_order_form_data']->street_name = $streetName;
        }

        if(empty($streetNumber)){
            $streetNumber = $orderInformation->street_number;
        }
        else{
            if (!testFields('streetNumber', $streetNumber)) {
                setFlash('street_number', messageText('streetNumber'));
            }

            $_SESSION['edit_order_form_data']->street_number = $streetNumber;
        }

        if(empty($phoneNumber)){
            $phoneNumber = $orderInformation->phone_number;
        }
        else{
            if (!testFields('phoneNumber', $phoneNumber)) {
                setFlash('phone_number', messageText('phoneNumber'));
            }

            $_SESSION['edit_order_form_data']->phone_number = $phoneNumber;
        }


        if (hasFlash('email') || hasFlash('first_name') || hasFlash('last_name') || hasFlash('street_name') || hasFlash('street_number') || hasFlash('phone_number')) {

            setFlash('error', messageText('orderInformationUpdateError'));

            header("Location: ../index.php?page=editOrderForm&id=$idOrder");
            exit;
        }

        $success = updateOrderInformation($idOrder, $email, $firstName, $lastName, $streetName, $streetNumber, $id_city, $phoneNumber, $id_shipping_method, $id_order_status);

        if($success){
            setFlash('success', messageText('orderInformationUpdateSuccess'));
        }
        else{
            setFlash('error', messageText('orderInformationUpdateError'));
        }

        header("Location: ../index.php?page=editOrderForm&id=$idOrder");
        exit;

    }
?>