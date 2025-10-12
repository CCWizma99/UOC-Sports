<?php
class Post {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // assumes PDO connection
    }

    /**
     * Search posts by title or description
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "
            SELECT p.post_id, p.title, p.description, p.commenting, p.date_posted,
                   GROUP_CONCAT(pi.image_path) AS images
            FROM newsfeed_post p
            LEFT JOIN newsfeed_post_image pi ON p.post_id = pi.post_id
            WHERE p.title LIKE :query OR p.post_id LIKE :query
            GROUP BY p.post_id
            ORDER BY p.date_posted DESC
        ";
    
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['query' => "%$query%"]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Convert images string to array
        foreach ($results as &$post) {
            $post['images'] = $post['images'] ? explode(',', $post['images']) : [];
        }
    
        return $results;
    }
    

    /**
     * Add a new post with optional image uploads
     * @param string $title
     * @param string $description
     * @param string $commenting ('YES' or 'NO')
     * @param array $files - $_FILES['images']
     * @return string Inserted Post ID
     */
    public function addPost($title, $description, $commenting, $files = []) {
        $post_id = $this->generatePostId();

        $sql = "
            INSERT INTO newsfeed_post (post_id, title, description, commenting, date_posted, status)
            VALUES (:post_id, :title, :description, :commenting, CURDATE(), 'ACTIVE')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'post_id' => $post_id,
            'title' => $title,
            'description' => $description,
            'commenting' => strtoupper($commenting) === 'NO' ? 'NO' : 'YES'
        ]);

        // Handle multiple file uploads
        if (!empty($files['name'][0])) {
            $this->uploadPostImages($post_id, $files);
        }

        return $post_id;
    }

    /**
     * Upload and save multiple post images
     * @param string $post_id
     * @param array $files
     */
    private function uploadPostImages($post_id, $files) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uoc-sports/public/images/posts/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] !== UPLOAD_ERR_OK) continue;

            $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
            $fileName = uniqid('img_', true) . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $this->savePostImage($post_id, 'images/posts/' . $fileName);
            }
        }
    }

    /**
     * Save image path to DB
     * @param string $post_id
     * @param string $path
     */
    private function savePostImage($post_id, $path) {
        $sql = "
            INSERT INTO newsfeed_post_image (post_id, image_path)
            VALUES (:post_id, :path)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['post_id' => $post_id, 'path' => $path]);
    }

    /**
     * Get all active posts with image thumbnails
     * @return array
     */
    public function getAllPosts() {
        $sql = "
            SELECT 
                p.post_id,
                p.title,
                p.description,
                p.commenting,
                p.date_posted,
                p.status,
                GROUP_CONCAT(i.image_path) AS images
            FROM newsfeed_post p
            LEFT JOIN newsfeed_post_image i ON p.post_id = i.post_id
            WHERE p.status = 'ACTIVE'
            GROUP BY p.post_id
            ORDER BY p.date_posted DESC
        ";
        $stmt = $this->db->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($posts as &$post) {
            $post['images'] = $post['images'] ? explode(',', $post['images']) : [];
        }

        return $posts;
    }

    /**
     * Get a single post with all its images
     * @param string $post_id
     * @return array|null
     */
    public function getPostById($post_id) {
        $sql = "
            SELECT 
                p.post_id,
                p.title,
                p.description,
                p.commenting,
                p.date_posted,
                p.status,
                GROUP_CONCAT(i.image_path) AS images
            FROM newsfeed_post p
            LEFT JOIN newsfeed_post_image i ON p.post_id = i.post_id
            WHERE p.post_id = :post_id
            GROUP BY p.post_id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['post_id' => $post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            $post['images'] = $post['images'] ? explode(',', $post['images']) : [];
        }

        return $post ?: null;
    }

    /**
     * Soft delete post (mark as INACTIVE)
     * @param string $post_id
     * @return bool
     */
    public function deletePost($post_id) {
        $sql = "
            UPDATE newsfeed_post
            SET status = 'INACTIVE'
            WHERE post_id = :post_id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['post_id' => $post_id]);
    }

    /**
     * Generate new post ID like P0001, P0002, etc.
     * @return string
     */
    private function generatePostId() {
        $sql = "SELECT post_id FROM newsfeed_post ORDER BY post_id DESC LIMIT 1";
        $stmt = $this->db->query($sql);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last && preg_match('/P(\d+)/', $last['post_id'], $m)) {
            $num = (int)$m[1] + 1;
        } else {
            $num = 1;
        }

        return sprintf('P%04d', $num);
    }

    
}
