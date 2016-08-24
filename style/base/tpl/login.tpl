<link rel='shortcut icon' href='favicon.ico' />
<link href='style/base/home.css' rel='stylesheet' type='text/css' />

<div class="fullscreen-bg">
    <video loop muted autoplay class="fullscreen-bg__video">
        <source src="images/raindrops.webm" type="video/webm">
        <source src="images/raindrops.mp4" type="video/mp4">
        <source src="images/raindrops.ogv" type="video/ogg">
    </video>
</div>

<table class='layout' id='maincontent'>
<tr>
    <td align='center' valign='middle'>
    <div id='logo'>
       <ul>
	  <li><a href='account.php'><b>Signup</b></a></li>
	  <li><a href='recover.php'><b>Recover</b></a></li>
        </ul>
     </div>

<form method='post' action='login.php?returnto={$returno}'>
<table class='layout glass'>
<tr>
    <td>Username&nbsp;</td>
    <td colspan='2'>
       <input type='text' name='uid' id='uid' value='{$user}' required='required' size='40' maxlength='40' pattern='[A-Za-z0-9_?]{1,20}' autofocus='autofocus' placeholder='Username' />
    </td>
</tr>
<tr>
    <td>Password&nbsp;</td>
    <td colspan='2'>
       <input type='password' name='pwd' id='pwd' required='required' size='40' maxlength='100' pattern='.{6,100}' placeholder='Password' />
    </td>
</tr>
<tr>
    <td></td>
    <td>
       <input type='checkbox' id='keeplogged' name='keeplogged' value='1' />
       <label for='keeplogged'>Remember me</label>
    </td>
    <td><input type='submit' name='login' value='Log in' class='btn' /></td>
</tr>
</table>
</form>
<br style='line-height: 4px;'>
Powered by BtiTracker (1.5.14) By <a href='https://github.com/Yupy/BtiTracker-1.5.1'>Yupy</a> &amp; <a href='http://www.btiteam.org'>BTiTeam</a>
    </td>
</tr>
</table>
