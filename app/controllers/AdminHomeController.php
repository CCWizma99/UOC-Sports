<?php

class AdminHomeController {
    public function index() {
        view('admin-home', ['title' => 'Home']);
    }
    public function users() {
        view('admin/users', ['title' => 'Users']);
    }
    public function reservations() {
        view('admin/reservations', ['title' => 'Users']);
    }
    public function players() {
        view('admin/players', ['title' => 'Users']);
    }
    public function equipments() {
        view('admin/equipments', ['title' => 'Users']);
    }
    public function events() {
        view('admin/events', ['title' => 'Users']);
    }
    public function teams() {
        view('admin/teams', ['title' => 'Users']);
    }
    public function budget() {
        view('admin/budget', ['title' => 'Users']);
    }
    public function news() {
        view('admin/news', ['title' => 'Users']);
    }
    public function inquiry() {
        view('admin/inquiry', ['title' => 'Users']);
    }
}