<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ( $data AS $structureNode ) {
		$this->renderPartial('_view', array('data' => $structureNode));
	}?>
</urlset>