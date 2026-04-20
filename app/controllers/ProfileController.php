<?php

class ProfileController {
    
    public function uploadProfileImage() {
        header('Content-Type: application/json');
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized. Please login first.'
            ]);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Check if file was uploaded
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
            echo json_encode([
                'status' => 'error',
                'message' => 'No file uploaded.'
            ]);
            return;
        }

        $file = $_FILES['profile_image'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File upload failed with error code: ' . $file['error']
            ]);
            return;
        }

        // Validate file size (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $maxSize) {
            echo json_encode([
                'status' => 'error',
                'message' => 'File size exceeds 5MB limit.'
            ]);
            return;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid file type. Only JPG, PNG, and GIF images are allowed.'
            ]);
            return;
        }

        // Get file extension
        $extension = '';
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
        }

        // Generate filename: user_id.extension
        $filename = $user_id . '.' . $extension;
        $uploadDir = __DIR__ . '/../internal/profile_img/';
        $uploadPath = $uploadDir . $filename;

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        try {
            $userModel = new User();

            // Delete old profile image if exists
            $userModel->deleteOldProfileImage($user_id);

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Failed to save uploaded file.');
            }

            // Update database
            if (!$userModel->updateProfileImage($user_id, $filename)) {
                // If database update fails, delete the uploaded file
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                throw new Exception('Failed to update database.');
            }

            // Generate image URL for response
            $imageUrl = '/uoc-sports/app/internal/profile_img/' . $filename . '?t=' . time();

            echo json_encode([
                'status' => 'success',
                'message' => 'Profile image updated successfully.',
                'imageUrl' => $imageUrl
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    public function deactivateAccount() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $user_type = strtoupper($_SESSION['user_type'] ?? 'PUBLIC');
        $staffRoles = ['ADMIN', 'REG', 'SPT', 'EQP', 'EXECUTIVE', 'COACH'];

        if (in_array($user_type, $staffRoles)) {
            echo json_encode(['status' => 'error', 'message' => 'Internal staff accounts cannot be deactivated from this profile.']);
            return;
        }

        try {
            $userModel = new User();
            if ($userModel->updateUserStatus($user_id, 'INACTIVE')) {
                // Clear session but don't redirect yet (let frontend handle it after showing toast)
                $_SESSION = [];
                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }
                session_destroy();

                echo json_encode(['status' => 'success', 'message' => 'Your account has been deactivated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to deactivate account.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }
}
