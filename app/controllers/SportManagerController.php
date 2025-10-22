<?php

class sportManagerController {
    public function index() {
        view('sports-manager/index');
    }

    public function schedule() {
        view('sports-manager/schedule');
    }

    public function events() {
        view('sports-manager/events');
    }
}