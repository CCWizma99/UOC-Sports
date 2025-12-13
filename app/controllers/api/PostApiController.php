<?php
class PostApiController {

    /**
     * Add a new post
     */
    public function addPost() {
        header('Content-Type: application/json');

        try {
            // Since form-data includes files, use $_POST and $_FILES (not json_decode)
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $commenting = $_POST['commenting'] ?? 'YES';
            $files = $_FILES['files'] ?? null;

            // Validate
            if (empty($title) || empty($description)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Both title and description are required.'
                ]);
                return;
            }

            // Call model
            $postModel = new Post();
            $postId = $postModel->addPost($title, $description, $commenting, $files);

            echo json_encode([
                'status' => 'success',
                'message' => 'Post added successfully.',
                'post_id' => $postId
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search posts by title or description
     */
    public function search() {
        header('Content-Type: application/json');

        try {
            $query = $_GET['q'] ?? '';

            if (empty($query)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing search query parameter.'
                ]);
                return;
            }

            $postModel = new Post();
            $results = $postModel->search($query);

            if (empty($results)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No matching posts found.'
                ]);
                return;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unexpected error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update an existing post
     */
    public function updatePost() {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $post_id = $data['post_id'] ?? '';
            $title = $data['title'] ?? '';
            $description = $data['description'] ?? '';
            $commenting = $data['commenting'] ?? 'YES';
            $status = $data['status'] ?? 'ACTIVE';

            if (empty($post_id) || empty($title) || empty($description)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Post ID, title, and description are required.'
                ]);
                return;
            }

            $postModel = new Post();
            $success = $postModel->updatePost($post_id, $title, $description, $commenting, $status);

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Post updated successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update post.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle commenting on a post
     */
    public function toggleCommenting() {
        header('Content-Type: application/json');

        try {
            $post_id = $_POST['post_id'] ?? '';

            if (empty($post_id)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Post ID is required.'
                ]);
                return;
            }

            $postModel = new Post();
            $success = $postModel->toggleCommenting($post_id);

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Commenting toggled successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to toggle commenting.'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update commenting status for a post
     */
    public function updateCommenting() {
        header('Content-Type: application/json');

        try {
            $post_id = $_POST['post_id'] ?? '';
            $commenting = $_POST['commenting'] ?? 'YES';

            if (empty($post_id)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Post ID is required.'
                ]);
                return;
            }

            $postModel = new Post();
            $success = $postModel->updateCommenting($post_id, $commenting);

            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Commenting status updated successfully.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update commenting status.'
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
