<div><?php echo $this->value; ?></div>

<div><?php echo $this->url->buildLink("index", "index", ["class" => "whitelink", "label" => "ALLER SUR INDEX.HTML"]); ?></div>

<?php var_dump($this->users); ?>

<?php echo $this->config->getLibraryUrl(); ?>
