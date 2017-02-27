<div class="col-lg-8">
    <h1><?php echo $article->titre; ?></h1>
    <p class="lead">
        by <?php echo $auteur->login; ?>
    </p>
    <hr>
    <p>
        <span class="glyphicon glyphicon-time"></span> Posted on <?php echo $article->date_publication;
        if ($article->date_modif != NULL) echo ", last modified " . $article->date_modif; ?>
    </p>
    <hr>
    <img class="img-responsive" src="<?php echo $article->image; ?>" alt="">
    <hr>
    <p class="lead"><?php echo nl2br($article->synopsis); ?></p>
    <p><?php echo nl2br($article->texte); ?></p>
    <?php if (isset($admin))
        if ($admin) { ?>
            <a class="btn btn-warning" href="<?php echo "index.php?action=editArticle&id=" . $article->id; ?>">Edit article</a>
            <a class="btn btn-danger" href="<?php echo "index.php?action=deleteArticle&id=" . $article->id; ?>">Delete article</a>
        <?php } ?>
    <hr>
    <div class="well">
        <form id="writeComm" style="padding-top: 70px;margin-top: -70px;" role="form" method="post" action="index.php?action=addComm&articleId=<?php echo $article->id; ?>"><!-- ancre avec fix pour barre de menu -->
            <?php if($_SESSION['role']!='admin'){ ?>
            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" class="form-control" id="login" name="login"<?php if(isset($_SESSION['login'])) echo ' value="' . $_SESSION['login'] . '"';?>>
            </div>
            <?php } ?>
            <div class="form-group">
                <label for="comm">Leave a Comment:</label>
                <textarea class="form-control" rows="3" id="comm" name="comm"><?php if(isset($_REQUEST['comm'])) echo $_REQUEST['comm']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <div id="comms"></div><!-- ancre avec fix pour barre de menu -->
        </form>
    </div>
    <?php
    if(count($coms))
        echo '<hr style="margin-bottom: 5px;">';
    else
        echo 'Be the first to post a comment!';
    foreach ($coms as $c) {
        ?>
        <div class="media" style="padding-top: 70px;margin-top: -55px;" id="<?php echo "comm" . $c->id; ?>"><!-- ancre avec fix pour barre de menu -->
            <div class="media-body">
                <h4 class="media-heading<?php if(in_array($c->pseudo_auteur,$comptes)) echo ' text-danger'; ?>"><?php echo $c->pseudo_auteur; ?>
                    <small><?php echo $c->date; ?></small>
                </h4>
                <p><?php echo nl2br($c->contenu); ?></p>
                <?php if (isset($admin))
                    if ($admin) { ?>
                        <a class="btn btn-warning" href="<?php echo "index.php?action=editComm&id=" . $c->id; ?>">Edit</a>
                        <a class="btn btn-danger" href="<?php echo "index.php?action=deleteComm&id=" . $c->id; ?>">Delete</a>
                    <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>