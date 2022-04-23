<div role="tabpanel">
	<!-- Nav tabs -->
	<?php $current = Language::list(Language::default());?>
	<ul class="nav nav-tabs" role="tablist" id="mtlang_translate_nav">
		<?php $i = 0; foreach ($language['list'] as $key => $lang) { if($key == Language::default()) continue; ?>
		<li role="presentation" class="<?php echo ($i == 0) ? 'active' : '' ; $i++;?>" data-lang="<?php echo $key;?>" style="padding: 0px;">
			<a href="#<?php echo $key;?>" aria-controls="<?php echo $key;?>" role="tab" data-toggle="tab" style="padding: 10px;">
                <?php echo $current['label'] .' <i class="fad fa-angle-double-right"></i> '. $lang['label'];?>
            </a>
		</li>
		<?php } ?>
	</ul>
	<div class="box">
		<div class="box-content" style="padding: 10px;">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<input type="text" name="key" id="input" class="form-control" required>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<input type="text" name="label" id="input" class="form-control" required>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<button type="submit" class="btn-icon btn-green btn-block">Thêm</button>
			</div>
		</div>
	</div>

	<!-- Tab panes -->
	<div class="tab-content" id="mtlang_translate_content">
		<?php $i = 0; foreach ($language['list'] as $key => $lang) { if($key == Language::default()) continue; ?>
		<div role="tabpanel" class="tab-pane <?php echo ($i == 0) ? 'active' : '' ; $i++;?>" id="<?php echo $key;?>">
            <div class="box">
                <div class="box-content" style="padding: 10px;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Dịch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($translate[$key] as $key_lang => $tran): ?>
                            <tr>
                                <td><a href="#" data-key="<?php echo $key;?>" data-key-lang="<?php echo $key_lang;?>" data-name="key" data-pk="<?php echo $key_lang;?>" class="edittable-lang-text" ><?php echo $key_lang;?></a></td>
                                <td><a href="#" data-key="<?php echo $key;?>" data-key-lang="<?php echo $key_lang;?>" data-name="label" data-pk="<?php echo $tran;?>" class="edittable-lang-text" ><?php echo $tran;?></a></td>
                            </tr>
                            <?php endforeach ?>

                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<?php } ?>
	</div>
</div>

<style type="text/css">
	.popover.top { margin-left: 80px;  }
    .nav-tabs > li {
        border-radius: 10px; overflow: hidden; margin-bottom: 10px;
    }
</style>

<script type="text/javascript">
	$(function(){

		$('.edittable-lang-text').editable({

			type: 'text',

			params: function(params) {

		        params.action = 'mtlang_ajax_translate_save';

		        params.language = $(this).editable().attr('data-key');

		        params.key 		= $(this).editable().attr('data-key-lang');

		        return params;
		    },
			url: base +'/ajax',
		});

		$('#admin_language_form').submit(function(){

			$('#ajax_item_save_loader').show();

			var data 		= $(this).serializeJSON();

			data.action     =  'mtlang_ajax_translate_add';

			data.language   = $('#mtlang_translate_nav li.active').attr('data-lang');

			$jqxhr   = $.post(base+'/ajax', data, function() {}, 'json');

			$jqxhr.done(function( r ) {

				$('#ajax_item_save_loader').hide();

	  			show_message(data.message, r.type);

	  			if( r.type == 'success' ) {

	  				$('#mtlang_translate_content .tab-pane.active tbody').prepend('\
	  					<tr>\
							<td><a href="#" data-key="'+data.language+'" data-key-lang="'+data.key+'" data-name="key" data-pk="'+data.label+'" class="edittable-lang-text" >'+data.key+'</a></td>\
							<td><a href="#" data-key="'+data.language+'" data-key-lang="'+data.key+'" data-name="label" data-pk="'+data.label+'" class="edittable-lang-text" >'+data.label+'</a></td>\
						</tr>');
	  			}
			});

			return false;
		})

	})
</script>