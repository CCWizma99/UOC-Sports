<?php
class InquiryController extends BaseController {

    // Load main search page
    public function index() {
        view('inquiry/search');
    }

    // API endpoint for live search
    public function search() {
        $query = trim($_GET['q'] ?? '');
        $inquiryModel = new Inquiry();

        if (empty($query)) {
            echo json_encode([]);
            return;
        }

        $results = $inquiryModel->search($query);
        echo json_encode($results);
    }
}
