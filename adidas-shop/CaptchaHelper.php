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
        
        $_SESSION['captcha_answer'] = $answer;
        $_SESSION['captcha_expires'] = time() + 300;
        
        return $question;
    }
    
    public static function verifyMathCaptcha($userAnswer) {
        if (!isset($_SESSION['captcha_answer']) || !isset($_SESSION['captcha_expires'])) {
            return false;
        }
        
        if (time() > $_SESSION['captcha_expires']) {
            unset($_SESSION['captcha_answer']);
            unset($_SESSION['captcha_expires']);
            return false;
        }
        
        $isValid = (intval($userAnswer) === $_SESSION['captcha_answer']);
        
        unset($_SESSION['captcha_answer']);
        unset($_SESSION['captcha_expires']);
        
        return $isValid;
    }
}