<form method='post' name='password' action='usercp.php?do=pwd&action=post&uid={$changepassword_uid}'>
<table class='lista' width='100%' align='center'>
<tr>
    <td class='header'>{$lang_changepassword.old_password}</td>
    <td class='lista'><input type='password' name='old_pwd' size='40' maxlength='40' /></td>
</tr>
<tr>
    <td class='header'>{$lang_changepassword.new_password}</td>
    <td class='lista'><input type='password' name='new_pwd' size='40' maxlength='40' /></td>
</tr>
<tr>
    <td class='header'>{$lang_changepassword.password_again}</td>
    <td class='lista'><input type='password' name='new_pwd1' size='40' maxlength='40' /></td>
</tr>
</table>

<br />

<table class='lista' width='100%' align='center'>
<tr>
    <td class='lista' align='center'><input type='submit' name='confirm' value='{$lang_changepassword.confirm}'/></td>
    <td class='lista' align='center'><input type='submit' name='confirm' value='{$lang_changepassword.cancel}'/></td>
</tr>
</table>
</form>
<br />
