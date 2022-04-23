<div class="row">
	<div class="col-md-4">
		<div class="box">
			<div class="box-content" style="padding:10px;">
				<?php echo _form( array('field' => 'language_label', 'label' => 'Tên hiển thị', 'type' => 'text', 'note' => 'Tên là cách nó được hiển thị trên trang web của bạn (ví dụ: tiếng Anh).') );?>
				<?php echo _form( array('field' => 'language_locale', 'label' => 'Locale', 'type' => 'text', 'note' => 'Locale cho ngôn ngữ (ví dụ en:). Bạn sẽ cần tạo /language/en thư mục nếu ngôn ngữ này không tồn tại.') );?>
				<?php echo _form( array('field' => 'language_flag', 'label' => 'Cờ', 'type' => 'image') );?>
				<div class="col-md-12">
					<hr>
					<div class="group text-right">
						<button type="submit" class="btn-icon btn-green"><i class="fas fa-save"></i>Lưu</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-8">
		<div class="box">
			<div class="box-content">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tên ngôn ngữ</th>
                            <th>Locale</th>
                            <th>Cờ</th>
                            <th>Mặc định</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody id="js_mtlang_tbody">
                        <?php foreach ($language as $key => $lang): ?>
                        <tr data-id="<?php echo $key;?>" data-label="<?php echo $lang['label'];?>" data-flag="<?php echo $lang['flag'];?>">
                            <td class="language_label"><?php echo $lang['label'];?></td>
                            <td class="language_locale"><?php echo $key;?></td>
                            <td class="language_flag"><?php get_img($lang['flag'],'',array('style'=>'width:50px;'));?></td>
                            <td class="active"><?php echo ($key == Language::default()) ? '<span style="color:green"><i class="fas fa-shield-check"></i></span>' : '';?></td>
                            <td class="action">
                                <button type="button" class="btn-icon btn-blue btn-lang-edit">Edit</button>
                                <button type="button" class="btn-icon btn-red btn-lang-delete">Delete</button>
                                <?php if ($key != Language::default()) { ?>
                                <button type="button" class="btn-icon btn-green btn-lang-active">Kích hoạt mặc định</button>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(function() {

		let LanguageHandler = function() {
			$( document )
				.on( 'click', '.btn-lang-edit', this.Edit )
				.on( 'click', '.btn-lang-delete', this.Delete )
				.on( 'click', '.btn-lang-active', this.Active )
				.on( 'submit', '#admin_language_form', this.Save )
		};

		LanguageHandler.prototype.Edit = function(e) {
			var box = $(this).closest('tr');
			$('input[name="language_label"]').val( box.attr('data-label') );
			$('input[name="language_locale"]').val( box.attr('data-id') );
			$('input[name="language_flag"]').val( box.attr('data-flag') );
			return false;
		};

		/**
         * @return {boolean}
         */
        LanguageHandler.prototype.Delete = function(e) {

			$('#ajax_item_save_loader').show();

			let box = $(this).closest('tr');

			let data = {
				'action' : 'mtlang_ajax_language_delete',
				'key'	 : box.attr('data-id')
			};

			$jqxhr   = $.post(base+'/ajax', data, function() {}, 'json');

			$jqxhr.done(function(response) {

				$('#ajax_item_save_loader').hide();

	  			show_message(response.message, response.type);

	  			if(response.type === 'success' ) {
	  				box.remove();
	  			}
			});

			return false;
		};

		LanguageHandler.prototype.Active = function(e) {

			$('#ajax_item_save_loader').show();

			var box = $(this).closest('tr');

			var data = {

				'action' : 'mtlang_ajax_language_active',

				'key'	 : box.attr('data-id')
			};

			$jqxhr   = $.post(base+'/ajax', data, function() {}, 'json');

			$jqxhr.done(function( r ) {

				$('#ajax_item_save_loader').hide();

	  			show_message(r.message, r.type);

	  			if( r.type == 'success' ) {

	  				$('#js_mtlang_tbody tr').each( function() {

	  					$(this).find('td.active').html('');

	  					$(this).find('td.action').append('<button type="button" class="btn-icon btn-green btn-lang-active">Kích hoạt mặc định</button>');
	  				});

	  				box.find('td.active').html('<span style="color:green"><i class="fas fa-shield-check"></i></span>');

	  				box.find('td.action .btn-lang-active').remove();
	  			}
			});

			return false;
		};

		LanguageHandler.prototype.Save = function(e) {

			$('#ajax_item_save_loader').show();

			let data 		= $(this).serializeJSON();

			data.action     =  'mtlang_ajax_language_save';

			$jqxhr   = $.post(base+'/ajax', data, function() {}, 'json');

			$jqxhr.done(function(response) {

				$('#ajax_item_save_loader').hide();

	  			show_message(response.message, response.status);

	  			if( response.check === true ) {

	  				$('#js_mtlang_tbody').append('\
	  					<tr data-id="'+data.language_locale+'" data-label="'+data.language_label+'" data-flag="'+data.language_flag+'">\
								<td class="language_label">'+data.language_label+'</td>\
								<td class="language_locale">'+data.language_locale+'</td>\
								<td class="language_flag"><img src="'+data.language_flag+'" style="width:50px;" /></td>\
								<td class="active"></td>\
								<td class="action">\
									<button type="button" class="btn-icon btn-blue btn-lang-edit">Edit</button>\
									<button type="button" class="btn-icon btn-red btn-lang-delete">Delete</button>\
									<button type="button" class="btn-icon btn-green btn-lang-active">Kích hoạt mặc định</button>\
								</td>\
							</tr>');
	  			}
	  			else {
	  				$('tr[data-id="'+data.language_locale+'"]').attr('data-id', data.language_locale);
	  				$('tr[data-id="'+data.language_locale+'"] td.language_locale').html(data.language_locale);
	  				$('tr[data-id="'+data.language_locale+'"]').attr('data-label', data.language_label);
	  				$('tr[data-id="'+data.language_locale+'"] td.language_label').html(data.language_label);
	  				$('tr[data-id="'+data.language_locale+'"]').attr('data-flag', data.language_flag);
	  				$('tr[data-id="'+data.language_locale+'"] td.language_flag').html('<img src="'+data.language_flag+'" style="width:50px;">');
	  			}
			});

			return false;
		};

		new LanguageHandler();
	});
</script>

