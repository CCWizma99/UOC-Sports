<?php

class AdminHomeController {
    public function index() {
        view('admin-home', ['title' => 'Home']);
    }
}
