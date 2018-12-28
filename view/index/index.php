<h1>Welcome <?php echo $this->session->getUser()->getUsername(); ?> on page <?php echo $this->value; ?></h1>

<?php var_dump($this->header['dependancies']); ?>

<p><?php echo $this->url->buildLink("index", "view", ["label" => "ALLER SUR VIEW.HTML"]); ?></p>