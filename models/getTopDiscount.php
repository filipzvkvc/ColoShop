<?php
    require_once "../config/connection.php";
    require_once "functions.php";

    header('Content-Type: application/json; charset=utf-8');

    $topDiscount = getTopDiscount();

    if ($topDiscount) {
        $dateFinishTimestamp = strtotime($topDiscount->date_finish);
        $currentTimestamp = time();

        if ($dateFinishTimestamp > $currentTimestamp) {
            $timeDifference = $dateFinishTimestamp - $currentTimestamp;

            $response = [
                'success' => true,
                'time_difference' => $timeDifference
            ];
        } 
        else {
            $response = [
                'success' => true,
                'time_difference' => 0
            ];
        }
    } 
    else {
        $response = [
            'success' => false,
            'message' => 'No deals found'
        ];
    }

    echo json_encode($response);

    exit;
?>
