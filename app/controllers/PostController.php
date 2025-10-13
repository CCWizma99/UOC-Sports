<?php
class PostController extends BaseController {
    public function view($id) {
        $postModel = new Post();
        $post = $postModel->findById($id);

        if ($post) {
            view('general/post', ['post' => $post]);
        } else {
            view('404', ['message' => 'Post not found']);
        }
    }
    public function viewPost($postId) {
        $postModel = new Post();
        $post = $postModel->getPostById($postId);
        $comments = $postModel->getComments($postId);
    
        if (!$post) {
            echo "<h2>Post not found</h2>";
            return;
        }
    
        view('general/post', [
            'post' => $post,
            'comments' => $comments,
            'user_id' => $_SESSION['user_id'] ?? null
        ]);
    }

    public function addComment() {
        try {
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'You must be logged in to comment.'
                ]);
                return;
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            $postId = trim($input['post_id'] ?? '');
            $content = trim($input['content'] ?? '');
    
            if (empty($postId) || empty($content)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing post ID or content.'
                ]);
                return;
            }
    
            $userId = $_SESSION['user_id'];
            $postModel = new Post();
            $success = $postModel->addComments($postId, $userId, $content);
    
            if ($success) {
                // Get user name for immediate frontend display
                $userModel = new User();
                $user = $userModel->getUserById($userId);
    
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Comment added successfully.',
                    'user_name' => $user['fname'] . ' ' . $user['lname']
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to add comment.'
                ]);
            }
    
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    
}