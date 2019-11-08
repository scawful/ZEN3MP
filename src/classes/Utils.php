<?php
namespace zen3mp;
use \DateTime;

class Utils {

    // Authentication Code from phppot.com by vincy@phppot.com
    public function getToken($length)
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet) - 1;
        for ($i = 0; $i < $length; $i ++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max)];
        }
        return $token;
    }

    public function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) {
            return $min; // not so random...
        }
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    public function redirect($url) {
        header("Location:" . $url);
        exit;
    }

    public function clearAuthCookie() {
        if (isset($_COOKIE["user_login"])) {
            setcookie("user_login", "");
        }
        if (isset($_COOKIE["random_password"])) {
            setcookie("random_password", "");
        }
        if (isset($_COOKIE["random_selector"])) {
            setcookie("random_selector", "");
        }
    }
    // ========================================================================

    public function datetime($date_time) {

        //post timeframe
        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($date_time); // Time of post
        $end_date = new DateTime($date_time_now); // current time

        $interval = $start_date->diff($end_date);
        if($interval->y >= 1) {
            if($interval == 1)
                $time_message = $interval->y . " year ago"; // 1 year ago
            else
                $time_message = $interval->y . " years ago"; // 1+ year ago
        }
        else if ($interval-> m >= 1) {
            if($interval->d == 0) {
                $days = " ago";
            }
            else if($interval->d == 1) {
                $days = $interval->d . " day ago";
            }
            else {
                $days = $interval->d . " days ago";
            }

            if($interval->m == 1){
                $time_message = $interval->m . " month " . $days;
            } else {
                $time_message = $interval->m . " months " . $days;
            }
        }
        else if($interval->d >= 1) {
            if($interval->d == 1) {
                $time_message = "Yesterday";
            }
            else {
                $time_message = $interval->d . " days ago";
            }
        }
        else if($interval->h >= 1) {
            if($interval->h == 1) {
                $time_message = $interval->h . " hour ago";
            }
            else {
                $time_message = $interval->h . " hours ago";
            }
        }
        else if($interval->i >= 1) {
            if($interval->i == 1) {
                $time_message = $interval->i . " minute ago";
            }
            else {
                $time_message = $interval->i . " minutes ago";
            }
        }
        else {
            if($interval->s < 30) {
                $time_message = "Just now";
            }
            else {
                $time_message = $interval->s . " seconds ago";
            }
        }
        return $time_message;
    }




}
