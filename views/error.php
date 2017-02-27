<div class="col-lg-8">
    <br>
    <h1 class="text-center"><span class="label label-danger">Error</span></h1>
    <br>
    <div class="alert alert-danger text-center"><strong><?php if(!isset($err)) echo 'Unknown error'; else echo $err; ?></strong></div>
    <br><br>
</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>