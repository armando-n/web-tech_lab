<?php

class LoginView {
    
    public static function show($user = null) {
        HeaderView::show("Member Log In", false);
        LoginView::showBody($user);
        FooterView::show(false);
    }
    
    public static function showBody($user) {
        $uNameValue = is_null($user) ? '' : $user->getUserName();
        $uNameError = is_null($user) ? '' : $user->getError("userName");
        ?>
<section>
    <h2>Log In</h2>
    <form action="login" method="post">
        <fieldset>
            <legend>Log In</legend>
            <!-- Pattern attribute absent to avoid hints that weaken security -->
            User Name <input type="text" name="userName" value="<?=$uNameValue?>" size="15" autofocus="autofocus" required="required" maxlength="30" tabindex="1" />
            <span class="error"><?=$uNameError?></span><br />
            Password <input type="password" name="password" size="15" required="required" maxlength="30" tabindex="2" />
        </fieldset>
        <div>
            <input type="submit" tabindex="3" />
        </div>
    </form>
</section>
<?php
    }
}

?>