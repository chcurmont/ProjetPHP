<!--ajouter remember me-->
<div class="col-lg-2"></div>
<div class="col-lg-4">
    <h1>Connect</h1>
    <div class="well">
        <form method="post" class="form-">
            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" class="form-control" id="login" name="login" <?php if(isset($_REQUEST['login'])) echo ' value=\''.$_REQUEST['login'].'\''; ?>>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <?php if(isset($err)){ ?>
                <small style="color: red; display: block; margin-bottom: 15px;"><?php echo $err; ?></small>
            <?php } ?>
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </div>
</div>
<div class="col-lg-2"></div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>