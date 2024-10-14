<?php

abstract class CheckToken
{
    public static function checkTokenInSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION["token"]) || !preg_match("/^[A-Za-z0-9_-]{2,}(?:\.[A-Za-z0-9_-]{2,}){2}$/", $_SESSION["token"])) {
            require "pleaseReconnect.php";
            exit;
        }
    }
}

CheckToken::checkTokenInSession();