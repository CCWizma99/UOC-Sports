<?php
function view($name, $data = []) {
    extract($data);
    require "../app/views/$name.php";
}
