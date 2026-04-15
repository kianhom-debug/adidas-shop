<?php
class UploadHelper {
    
    public static function uploadProfilePhoto($file, $userId) {
        $targetDir = __DIR__ . '/uploads/profiles/';
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Only JPG, PNG, GIF, and WEBP files are allowed'];
        }
        
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File size must be less than 2MB'];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
        $targetPath = $targetDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            self::resizeImage($targetPath, 300, 300);
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'error' => 'Failed to upload file'];
    }
    
    private static function resizeImage($path, $width, $height) {
        $info = getimagesize($path);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($path);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($path);
        } else {
            return false;
        }
        
        $resized = imagecreatetruecolor($width, $height);
        
        if ($info['mime'] == 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $width, $height, $transparent);
        }
        
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        
        if ($info['mime'] == 'image/jpeg') {
            imagejpeg($resized, $path, 90);
        } elseif ($info['mime'] == 'image/png') {
            imagepng($resized, $path);
        } elseif ($info['mime'] == 'image/gif') {
            imagegif($resized, $path);
        }
        
        imagedestroy($image);
        imagedestroy($resized);
        
        return true;
    }
    
    public static function deleteProfilePhoto($filename) {
        $path = __DIR__ . '/uploads/profiles/' . $filename;
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
}