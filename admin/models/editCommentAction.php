<?php
session_start();
require_once '../../config/config.php';
require_once '../../config/connection.php';
require_once '../../models/functions.php';
require_once 'adminFunctions.php';

if (!isset($_SESSION['edit_comment_form_data'])) {
    $_SESSION['edit_comment_form_data'] = new stdClass();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = 0;

    $commentId = post('id_comment');
    $content = post('content');

    if (empty($content)) {
        setFlash('content', 'Comment content is required.');
        $errors++;
    } else {
        if (!testFields('description', $content)) {
            setFlash('content', messageText('description'));
            $errors++;
        }
        $_SESSION['edit_comment_form_data']->content = $content;
    }

    if ($errors > 0) {
        setFlash('error', 'Comment edit failed.');
        header("Location: ../index.php?page=editCommentForm&id=$commentId");
        exit;
    }

    $success = updateCommentContent($commentId, $content);

    if ($success) {
        setFlash('success', 'Comment successfully updated.');
        unset($_SESSION['edit_comment_form_data']);
    } else {
        setFlash('error', 'Error updating comment.');
    }

    header("Location: ../index.php?page=editCommentForm&id=$commentId");
    exit;
}
