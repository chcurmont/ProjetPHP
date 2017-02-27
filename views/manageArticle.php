<div class="col-lg-8">
    <h1><?php if(isset($article)) echo 'Edit'; else echo 'Add'; ?> an article</h1>
    <div class="well">
        <form method="post" role="form">
            <div class="form-group">
                <label for="titre">Title:</label>
                <input type="text" class="form-control" id="titre" name="titre"<?php    if(isset($_REQUEST['titre'])) echo " value=" . $_REQUEST['titre'];
                                                                                        if(isset($article)) echo " value=" . $article->titre; ?>>
            </div>
            <div class="form-group">
                <label for="synopsis">Synopsis:</label>
                <textarea class="form-control" rows="3" id="synopsis" name="synopsis"><?php if(isset($_REQUEST['synopsis'])) echo $_REQUEST['synopsis'];
                                                                                            if(isset($article)) echo $article->synopsis; ?></textarea>
            </div>
            <div class="form-group">
                <label for="text">Text:</label>
                <textarea class="form-control" rows="10" id="text" name="text"><?php if(isset($_REQUEST['text'])) echo $_REQUEST['text'];
                                                                                    if(isset($article)) echo $article->texte; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>
