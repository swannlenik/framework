<html>
    <head>
        <title><?php echo $this->header['title']; ?></title>
        <?php
        foreach($this->header['dependancies']['css'] as $dependancy) :
        ?>
            <link rel="stylesheet" type="text/css" href="<?php echo $dependancy; ?>" />
        <?php endforeach; ?>
        <link rel="stylesheet" type="text/css" href="<?php echo \Core\Config::getInstance()->getBaseUrl(false) . "view/css/default.css"; ?>" />

        <?php
        foreach($this->header['dependancies']['js'] as $dependancy) :
            ?>
            <script src="<?php echo $dependancy; ?>"></script>
        <?php endforeach; ?>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>