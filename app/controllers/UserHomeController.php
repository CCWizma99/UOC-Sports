<?php

class UserHomeController {
    public function index() {
        view('user-home');
    }
    public function news() {
        $postModel = new Post();
        $lostFoundModel = new Post();
    
        $recentNews = $postModel->getRecentPosts(6);
        $recentLostFound = $lostFoundModel->getRecentCases(6);
    
        view('general/news', [
            'recentNews' => $recentNews,
            'recentLostFound' => $recentLostFound
        ]);
    }
    public function facilityReservation() {
        view('general/facility-reservation');
    }
    public function contactUs() {
        view('general/contact-us');
    }
}
