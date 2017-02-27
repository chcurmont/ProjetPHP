<!-- Blog Entries Column -->
<div class="col-md-8">
    <h1 class="page-header">
        Blog Home
        <small>
            <?php echo $nbArtTotal; ?> article<?php if($nbArtTotal>1) echo 's';
                if(isset($_COOKIE['nbComm']))
                    if($_COOKIE['nbComm']>1)
                        echo ", you posted " . $_COOKIE['nbComm'] . " comments";
                    else
                        echo ", you posted " . $_COOKIE['nbComm'] . " comment";;
            ?>
        </small>
    </h1>
    <?php foreach ($articles as $row) { ?>
        <h2>
            <a href="<?php echo "index.php?action=article&id=" . $row->id; ?>"><?php echo $row->titre; ?></a>
        </h2>
        <p class="lead">
            by <?php echo $comptes[$row->id_auteur]->login; ?>
        </p>
        <p><span class="glyphicon glyphicon-time"></span> Posted on <?php echo $row->date_publication;
            if ($row->date_modif != NULL) echo ", last modified " . $row->date_modif; ?></p>
        <hr>
        <img class="img-responsive" src="<?php echo $row->image; ?>" alt="">
        <hr>
        <p><?php echo nl2br($row->synopsis); ?></p>
        <a class="btn btn-primary" href="<?php echo "index.php?action=article&id=" . $row->id; ?>">Read More <span class="glyphicon glyphicon-chevron-right"></span></a>
        <?php if (isset($admin))
            if ($admin) { ?>
                <a class="btn btn-warning" href="<?php echo "index.php?action=editArticle&id=" . $row->id; ?>">Edit</a>
                <a class="btn btn-danger"
                   href="<?php echo "index.php?action=deleteArticle&id=" . $row->id; ?>">Delete</a>
            <?php } ?>
        <hr>
    <?php } ?>

    <!-- Pager -->
    <ul class="pager">
        <li class="previous<?php if($prev<1) echo ' disabled'; ?>">
            <a href="<?php if($prev>0) echo 'index.php?action=home&numPage=' . $prev; else echo 'javascript:void(0)'; ?>">&larr; Newer</a>
        </li>
        <li class="next<?php if($next<1) echo ' disabled'; ?>">
            <a href="<?php if($next>0) echo 'index.php?action=home&numPage=' . $next; else echo 'javascript:void(0)'; ?>">Older &rarr;</a>
        </li>
    </ul>

</div>

<?php require('sidebar.php'); ?>
<?php require('footer.php'); ?>