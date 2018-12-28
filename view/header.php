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
        <nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="<?php echo \Core\Config::getInstance()->getBaseUrl(); ?>">{Mon site}</a>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <?php if(!$this->session->sessionIsActive()): ?>
                <form class="form-inline mt-2 mt-md-0" method="post" action="<?php echo $this->url->buildUrl("index", "index"); ?>">
                    <input class="form-control mr-sm-2" type="text" name="connexionID" placeholder="Identifiant" aria-label="identifiant" />
                    <input class="form-control mr-sm-2" type="password" name="connexionPassword" placeholder="Mot de passe" aria-label="Mot de passe" />
                    <button class="btn btn-success" type="submit">Se connecter</button>
                </form>
                <?php else : ?>
                <div class="col-sm-10 col-md-6 col-lg-6 col-xl-6">
                    <span class="">Bienvenue <?php echo $this->session->getUser()->getUsername(); ?></span>
                    <a class=btn btn-error" href="<?php echo $this->url->buildUrl("index", "logout"); ?>">Se déconnecter</a>
                </div>
                <?php endif; ?>
            </div>
        </nav>