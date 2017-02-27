<div class="col-lg-8">
    <h2>Profile of <?php echo $compte->login; ?></h2>
    <hr>
    <h3>Stats :</h3>

    <div>Number of articles : <?php echo $nbArt; ?></div>
    <div>Number of comments : <?php echo $nbComms; ?></div>

</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>