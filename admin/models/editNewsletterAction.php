<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id_newsletter = post('id_newsletter');
        $newsletterInformation = getNewsletterInformation2($id_newsletter);

        $email = post('email');
        $subscribed = (int)post('radio');

        $_SESSION['edit_newsletter_form_data'] = new stdClass();

        if (empty($email)) {
            $email = $newsletterInformation->email;
        } else {
            if (!testFields('email', $email)) {
                setFlash('email', messageText('email'));
            }

            if (newsletterEmailExists($email, $id_newsletter)) {
                setFlash('email', 'This email is already subscribed.');
            }

            $_SESSION['edit_newsletter_form_data']->email = $email;
        }

        if (hasFlash('email')) {
            setFlash('error', 'An error occurred while updating the newsletter information.');
            header("Location: ../index.php?page=editNewsletterForm&id=$id_newsletter");
            exit;
        }

        $success = updateNewsletterInformation($id_newsletter, $email, $subscribed);

        if ($success) {
            setFlash('success', 'Newsletter information updated successfully.');
        } else {
            setFlash('error', 'An error occurred while updating the newsletter information.');
        }

        header("Location: ../index.php?page=editNewsletterForm&id=$id_newsletter");
        exit;
    }

?>