<div id="loginForm">
    <div class="header">Benutzerdaten eingeben:</div>
    <div class="body">
        <?=form_open("login/doLogin")?>
        <input type="text" name="user" placeholder="Benutzername"><br>
        <input type="password" name="pass" placeholder="Passwort"><br>
        <input type="submit" value="Login">
        </form>
    </div>
</div>