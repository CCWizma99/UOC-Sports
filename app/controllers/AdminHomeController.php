<?php

class AdminHomeController {
    public function index() {
        view('admin-home', ['title' => 'Home']);
    }
    public function users() {
        view('admin/users', ['title' => 'Users']);
    }
}