<?php
/**
 * Created by PhpStorm.
 * User: Mindaugas
 * Date: 2017-11-09
 * Time: 19:33
 */

namespace App\Helper;


class Helper
{
    public static function getErrorMessage($message, $value = '')
    {
        $errors_message = [
            [0, 'Unknown error'],
            [1, 'Betslip structure mismatch'],
            [2, 'Minimum stake amount is :min_amount'],
            [3, 'Maximum stake amount is :max_amount'],
            [4, 'Minimum number of selections is :min_selections'],
            [5, 'Maximum number of selections is :max_selections'],
            [6, 'Minimum odds are :min_odds'],
            [7, 'Maximum odds are :max_odds'],
            [8, 'Duplicate selection found'],
            [9, 'Maximum win amount is :max_win_amount'],
            [10, 'Your previous action is not finished yet'],
            [11, 'Insufficient balance']
        ];

        $errors = [
            'code'    => $errors_message[$message][0],
            'message' => $errors_message[$message][1]
        ];

        return $errors;
    }

    public static function checkInterval($value, $min, $max)
    {
        return $value >= $min && $value <= $max;
    }

    public static function countSameId(array $arr)
    {
        $count_array = array();
        foreach ($arr as $item) {
            if (isset($count_array[$item['id']])) {
                $count_array[$item['id']]++;
            } else {
                $count_array[$item['id']] = 1;
            }
        }

        return $count_array;
    }
}