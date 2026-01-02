<?php
$conn = mysqli_connect("localhost", "root", "", "sw_db");

if (!$conn) {
    die("DB Connection Failed");
}
