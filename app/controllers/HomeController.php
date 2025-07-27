<?php

class HomeController {
    public function index() {
        view('admin-home', ['title' => 'Home']);
    }
}
