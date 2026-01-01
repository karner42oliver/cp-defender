<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Ignored", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
		<?php $table = new \CP_Defender\Module\Scan\Component\Result_Table();
		$table->type = \CP_Defender\Module\Scan\Model\Result_Item::STATUS_FIXED;
		$table->prepare_items();
		if ( $table->get_pagination_arg( 'total_items' ) ) {
			$table->display();
		} else {
			?>
            <div class="well well-blue with-cap">
                <i class="def-icon icon-warning"  aria-hidden="true"></i>
				<?php _e( "You haven't cleaned any suspicious files yet. When this action is available, any cleaned files will appear here.", cp_defender()->domain ) ?>
            </div>
			<?php
		}
		?>
    </div>
</div>