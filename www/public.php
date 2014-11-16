<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= FILE_PREFIX ?>css/foundation.css"/>
    <link rel="stylesheet" href="<?= FILE_PREFIX ?>css/app.css"/>
    <script src="<?= FILE_PREFIX ?>js/vendor/modernizr.js"></script>
</head>
<body>
<header>
    <div class="row">
        <div class="large-3 large-centered columns page-title"><h1><?= $title ?></h1>
            <small><?= $description ?></small>
        </div>
    </div>
    <div class="row">
        <div class="large-12 columns">
            <ul class="nav">
                <?php
                foreach ($related['categories'] as $category):
                    ?>
                    <li>
                        <a href="<?= LINK_PREFIX ?>/Categories/View/<?= $category['id'] ?>"><?= $category['title'] ?></a>
                    </li>
                <?php
                endforeach;
                ?>
            </ul>
        </div>
    </div>
</header>
<div class="row">
    <?php
    if (isset($message)):
        ?>
        <div data-alert class="alert-box">
            <?= $message ?>
            <a href="#" class="close">&times;</a>
        </div>
    <?php
    endif;
    ?>
    <?php echo $content; ?>
</div>
<footer class="row genel-footer">
    <div class="large-12 columns">
        <?= $copyright ?>
    </div>
</footer>
<script src="<?= FILE_PREFIX ?>js/vendor/jquery.js"></script>
<script src="<?= FILE_PREFIX ?>js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
</body>
</html>
