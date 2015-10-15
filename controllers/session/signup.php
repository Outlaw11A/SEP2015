<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $createUser = User::createUser($_POST['signup']);

    if ($createUser) {
        Session::setSuccess('Signup successful! You can now login.');
        Session::redirect('/login');
    }

    Session::setError('Unable to sign you up, please try again.');
    Session::redirect('/signup');

}

?>