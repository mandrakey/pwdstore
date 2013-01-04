<div id="loginForm">
    <div class="header"><?=lang("login_EnterUserData")?>:</div>
    <div class="body">
        <?=form_open("login/doLogin")?>
        <input type="text" name="user" placeholder="<?=lang("login_Username")?>"><br>
        <input type="password" name="pass" placeholder="<?=lang("login_Password")?>"><br>
        <input type="submit" value="<?=lang("login_Login")?>">
        </form>
    </div>
</div>