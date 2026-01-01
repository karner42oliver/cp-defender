<div class="wrap">
    <p>
        Content: <?php echo count( \CP_Defender\Module\Scan\Component\Scan_Api::getContentFiles() ) ?>
    </p>
    <p>
        Core: <?php echo count( \CP_Defender\Module\Scan\Component\Scan_Api::getCoreFiles() ) ?>
    </p>
    <p>
        Progress: <?php echo \CP_Defender\Module\Scan\Component\Scan_Api::getScanProgress() ?>
    </p>
    <p>
        Time: <?php
		$model = \CP_Defender\Module\Scan\Component\Scan_Api::getLastScan();
		if ( is_object( $model ) ) {
			echo $model->dateFinished . '-' . $model->dateStart;
		}
		?>
    </p>
</div>