<?php

class SportManagerController {

    public function index() {
        view('sports-manager/index');
    }

    public function events() {
        view('sports-manager/events');
    }
}