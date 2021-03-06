<?php //var_dump($user); ?>
<!DOCTYPE html>
<html>
<head>
    <title>bleuelmedia pwdStore</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, height: device-height">
    
    <link rel="stylesheet" type="text/css" href="<?=base_url("/tpl/base.css")?>" media="screen and (min-width: 800px),print and (min-width: 800px)">
    <link rel="stylesheet" type="text/css" href="<?=base_url("/tpl/smaller800.css")?>" media="screen and (max-width: 800px), print and (max-width: 800px)">
    <link rel="stylesheet" type="text/css" href="<?=base_url("/tpl/print.css")?>" media="print">
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
            
            <a href="<?=site_url("")?>"><?=lang("navigation_Home")?></a>
            <? if (AuthHelper::getInstance()->isLoggedIn()): ?>
            <a href="<?=site_url("settings")?>"><?=lang("navigation_Settings")?></a>
            <a href="<?=site_url("secrets/create")?>"><?=lang("navigation_NewEntry")?></a>
            
            <a href="<?=site_url("login/logout")?>"><?=lang("navigation_Logout")?></a>
            <? endif; ?>
            
            <? if (isset($user["level"]) && $user["level"] == "0"): ?>
            <br>
            <a href="<?=site_url("users")?>"><?=lang("navigation_ManageUsers")?></a>
            <a href="<?=site_url("categories")?>"><?=lang("navigation_ManageCategories")?></a>
            <? endif; ?>
        
        </div>