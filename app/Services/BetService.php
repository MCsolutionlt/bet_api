<?php

namespace App\Services;


/**
 * Class BetService
 * @package App\Services
 */
class BetService
{
    /**
     * @var int
     */
    private $odds = 0;

    /**
     * @var array
     */
    private $errors_glob = [];

    /**
     * @var array
     */
    private $errors_all = [];

    /**
     * Get error message by key
     * @param $message
     * @param string $value
     * @return array
     */
    public function getErrorMessage($message, $value = '')
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

    /**
     * Check bet head information
     * @param array $bet
     * @return array|string
     */
    public function checkBet(array $bet)
    {
        $errors_glob = [];
        $necessary_keys = ['stake_amount', 'player_id', 'selections'];

        if ($this->checkArrayKeys($bet, $necessary_keys)) {
            if (!$this->checkInterval($bet['stake_amount'], 0.3, 10000)) {
                $errors_glob[] = $this->getErrorMessage(2);
                $errors_glob[] = $this->getErrorMessage(3);
            }

            if ($this->checkInterval(count($bet['selections']), 1, 20)) {
                $bet['selections'] = $this->checkBetEvents($bet['selections']);
            } else {
                $errors_glob[] = $this->getErrorMessage(4);
                $errors_glob[] = $this->getErrorMessage(5);
            }

            $max_win = $bet['stake_amount'] * $this->odds;

            if ($max_win > 20000) {
                $errors_glob[] = $this->getErrorMessage(9);
            }

            if (!empty($errors_glob)) {
                $bet['errors'] = array_merge($this->errors_glob, $errors_glob);
            }

            $this->errors_all = array_merge($this->errors_all, $errors_glob);

            if (!empty($this->errors_all)) {
                return $bet;
            }

            return '';
        }

        return $bet['errors'] = $this->getErrorMessage(1);
    }


    /**
     * Check bet events
     * @param array $events
     * @return array
     */
    private function checkBetEvents(array &$events)
    {
        if (isset($events)) {
            $count_id = $this->countSameValueInArray($events, 'id');
            $necessary_keys = ['id', 'odds'];

            foreach ($events as &$item) {
                if (!$this->checkArrayKeys($item, $necessary_keys)) {
                    $this->errors_glob[] = $this->getErrorMessage(1);

                    return $events;
                }

                $errors_selection = [];

                if (!$this->checkInterval($item['odds'], 1, 10000)) {
                    $errors_selection[] = $this->getErrorMessage(6);
                    $errors_selection[] = $this->getErrorMessage(7);
                }

                if ($count_id[$item['id']] > 1) {
                    $errors_selection[] = $this->getErrorMessage(8);
                }

                if (!empty($errors_selection)) {
                    $item['errors'] = $errors_selection;
                }

                $this->errors_all = array_merge($this->errors_all, $errors_selection);
                $this->odds = ($this->odds === 0 ? $this->odds + $item['odds'] : $this->odds * $item['odds']);
            }

            return $events;
        }

        return [];
    }

    /**
     * Check the value falls into the range
     * @param $value
     * @param $min
     * @param $max
     * @return bool
     */
    public function checkInterval($value, $min, $max)
    {
        return $value >= $min && $value <= $max;
    }

    /**
     * Check the array keys
     * @param $arr
     * @param $keys
     * @return bool
     */
    public function checkArrayKeys(array $arr, array $keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $arr)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Count same array value
     * @param array $arr
     * @param $key
     * @return array
     */
    public function countSameValueInArray(array $arr, $key)
    {
        $count_array = [];
        foreach ($arr as $item) {
            if (isset($count_array[$item[$key]])) {
                $count_array[$item[$key]]++;
            } else {
                $count_array[$item[$key]] = 1;
            }
        }

        return $count_array;
    }
}