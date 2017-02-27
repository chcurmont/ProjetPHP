<div class="col-lg-8">
    <h1>Edit a comment</h1>
    <div class="well">
        <form method="post" role="form">
            <div class="form-group">
                <label for="text">Posted by:</label>
                <h4 class="media-heading<?php if(in_array($comm->pseudo_auteur,$comptes)) echo ' text-danger'; ?>"><?php echo $comm->pseudo_auteur; ?>
                    <small><?php echo $comm->date; ?></small>
                </h4>
            </div>
            <div class="form-group">
                <label for="text">New text:</label>
                <textarea class="form-control" rows="3" id="text" name="text"><?php echo $comm->contenu; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>