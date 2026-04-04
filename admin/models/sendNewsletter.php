<?php
    session_start();
    require_once '../../config/connection.php';
    require_once 'adminFunctions.php';
    require_once '../../models/functions.php';

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $subject = post('subject');
        $content = post('content');

        if(!isset($_SESSION['send_newsletter'])) {
            $_SESSION['send_newsletter'] = new stdClass();
        }

        if(empty($subject)) {
            setFlash('subject', 'Subject cannot be empty!');
        } else {
            $_SESSION['send_newsletter']->subject = $subject;
        }

        if(empty($content)) {
            setFlash('content', 'Content cannot be empty!');
        } else {
            $_SESSION['send_newsletter']->content = $content;
        }

        if(hasFlash('subject') || hasFlash('content')) {
            header("Location: ../index.php?page=newsletter");
            exit;
        }

        try {
            $subscribers = getNewsletterSubscribers();
        } catch (Exception $e) {
            setFlash('error', 'Database error while fetching subscribers: ' . $e->getMessage());
            header("Location: ../index.php?page=newsletter");
            unset($_SESSION['send_newsletter']);
            exit;
        }

        if(empty($subscribers)) {
            setFlash('error', 'There are no subscribed users to send the newsletter to.');
            header("Location: ../index.php?page=newsletter");
            unset($_SESSION['send_newsletter']);
            exit;
        }

        $success = 0;
        $fail = 0;

        foreach($subscribers as $subscriber) {
            try {
                if(sendNewsletterEmail($subscriber, $subject, $content)) {
                    $success++;
                } else {
                    $fail++;
                }
            } catch (Exception $e) {
                $fail++;
                error_log("Error sending to {$subscriber['email']}: " . $e->getMessage());
            }
        }

        if($success > 0) {
            $subscriberText = ($success === 1) ? 'subscriber' : 'subscribers';
            setFlash('success', "Newsletter sent to $success $subscriberText.");

            unset($_SESSION['send_newsletter']);
        } else {
            setFlash('error', "Newsletter could not be sent to any subscribers.");
        }

        header('Location: ../index.php?page=newsletter');
        exit;
    }
?>
