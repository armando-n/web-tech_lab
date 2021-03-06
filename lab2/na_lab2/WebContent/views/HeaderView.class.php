<?php
class HeaderView {
    public static function show($title = null, $loggedIn = false) {
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="author" content="Armando Navarro" />
    <title>DHMA | <?= $title ?></title>
</head>
<body>

<h1><?= $title ?></h1>

<header>
    <img src="images/logo.png" alt="DHMA Logo" width="99" height="58" />
    <nav id="main-nav">
        <h2>Site Navigation</h2>
        <ul>
            <li><a href="home">Home</a></li>
            <li><a href="past-measurements">Past Measurements</a></li><?php
            if ($loggedIn) { ?>
            <li><a href="logout">Logout</a></li><?php
            } else { ?> 
            <li><a href="login">Login</a></li>
            <li><a href="signup">Sign Up</a></li><?php
            } ?> 
        </ul>
    </nav>
</header>

<?php
    }
}
?>