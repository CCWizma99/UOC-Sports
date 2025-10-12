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
            $commenting = isset($_POST['allow_comments']) && $_POST['allow_comments'] === 'no' ? 'NO' : 'YES';
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
}
