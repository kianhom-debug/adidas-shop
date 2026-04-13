<?php
class CaptchaHelper {
    
    public static function generateMathCaptcha() {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operation = rand(0, 1);
        
        if ($operation == 0) {
            $question = "$num1 + $num2";
            $answer = $num1 + $num2;
        } else {
            if ($num1 < $num2) {
                $temp = $num1;
                $num1 = $num2;
                $num2 = $temp;
            }
            $question = "$num1 - $num2";
            $answer = $num1 - $num2;
        }
        
        // Store in COOKIE instead of session
        setcookie('captcha_answer', $answer, time() + 300, '/');
        
        return $question;
    }
    
    public static function verifyMathCaptcha($userAnswer) {
        if (!isset($_COOKIE['captcha_answer'])) {
            return false;
        }
        
        $expected = intval($_COOKIE['captcha_answer']);
        $userAnswerInt = intval($userAnswer);
        
        // Clear cookie
        setcookie('captcha_answer', '', time() - 3600, '/');
        
        return ($userAnswerInt === $expected);
    }
}