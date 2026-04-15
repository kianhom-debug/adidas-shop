<?php
require_once 'config.php';

class RememberToken {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function create($userId, $expiresInDays = 30) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiresInDays} days"));
        
        // Delete any existing tokens for this user
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Create new token
        $stmt = $this->pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt]);
        
        return $token;
    }
    
    public function validate($token) {
        $stmt = $this->pdo->prepare("SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
    
    public function delete($token) {
        $stmt = $this->pdo->prepare("DELETE FROM remember_tokens WHERE token = ?");
        return $stmt->execute([$token]);
    }
}