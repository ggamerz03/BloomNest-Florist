<?php
require '../_base.php';
//-----------------------------------------------------------------------------

// ----------------------------------------------------------------------------
$_title = 'Login';
include '../_head.php';
?>
    <form action="login.php" method="post">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="checkbox" id="remember" name="remember">
        <label for="remember">Remember Me</label><br><br>
        
        <input type="reset" value="Reset">
        <input type="submit" value="Login">
        
    </form>
<?php
include '../_foot.php';