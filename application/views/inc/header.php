<?php //var_dump($user); ?>
<!DOCTYPE html>
<html>
<head>
    <title>bleuelmedia pwdStore</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    
    <link rel="stylesheet" type="text/css" href="<?=base_url("/tpl/base.css")?>">
    <script type="text/javascript" src="<?=base_url("/tpl/jquery-1.8.3.min.js")?>"></script>
    
    <? if(isset($jsFiles) && is_array($jsFiles)): ?>
    <? foreach($jsFiles as $file): ?>
    <script type="text/javascript" src="<?=base_url("/js/".$file.".js")?>"></script>
    <? endforeach; ?>
    <? endif; ?>
</head>
<body>
    <input type="hidden" id="SITE_URL" value="<?=site_url()?>">
    
    <div id="document">
        <div id="navigation">
            
            <a href="<?=site_url("")?>">Startseite</a>
            <? if (AuthHelper::getInstance()->isLoggedIn()): ?>
            <a href="<?=site_url("settings")?>">Einstellungen</a>
            <a href="<?=site_url("secrets/new")?>">Neuer Eintrag</a>
            
            <a href="<?=site_url("login/logout")?>">Logout</a>
            <? endif; ?>
            
            <? if (isset($user["level"]) && $user["level"] == "0"): ?>
            <br>
            <a href="<?=site_url("users")?>">Benutzerverwaltung</a>
            <a href="<?=site_url("accounts")?>">Kontenverwaltung</a>
            <? endif; ?>
        
        </div>