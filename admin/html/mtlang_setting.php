<form id="admin_language_form" method="post">
	<?php echo form_open();?>
	<div class="action-bar">
	    <div class="pull-right"><button type="submit" class="btn-icon btn-green"><i class="fas fa-save"></i>Lưu</button></div>
	</div>
	<div class="col-md-12">
		<div id="ajax_item_save_loader" class="ajax-load-qa">&nbsp;</div>
		<div role="tabpanel" class="ui-title-bar__group">
            <div class="ui-title-bar__group" style="padding-bottom:5px;">
                <div class="ui-title-bar__action">
                    <?php foreach ($tabs as $key => $tab): ?>
                        <a href="<?php echo URL_ADMIN;?>/plugins?page=mtlang&tab=<?php echo $key;?>" class="<?php echo ($key == $current_tab)?'active':'';?> btn btn-default"><?php echo $tab['label'];?></a>
                    <?php endforeach ?>
                </div>
            </div>
			<!-- Tab panes -->
			<div class="tab-content" style="padding-top: 10px;">
				<?php call_user_func( $tabs[$current_tab]['callback'], get_instance(), $current_tab ) ?>
			</div>
		</div>
	</div>
</form>