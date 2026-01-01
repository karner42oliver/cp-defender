<div class="dev-box">
    <div class="box-title">
        <h3><?php _e( "Ignored", cp_defender()->domain ) ?></h3>
    </div>
    <div class="box-content">
		<?php $table = new \CP_Defender\Module\Scan\Component\Result_Table();
		$table->type = \CP_Defender\Module\Scan\Model\Result_Item::STATUS_IGNORED;
		$table->prepare_items();
		if ( $table->get_pagination_arg( 'total_items' ) ) {
			?>
            <p class="line"><?php _e( "Here is a list of the suspicious files you have chosen to ignore.", cp_defender()->domain ) ?></p>
			<?php
			$table->display();
		} else {
			?>
            <div class="well well-blue with-cap">
                <i class="def-icon icon-warning"  aria-hidden="true"></i>
				<?php _e( "You haven't ignored any suspicious files yet. Ignored files appear here and can be restored at any times.", cp_defender()->domain ) ?>
            </div>
			<?php
		}
		?>
    </div>
</div>