<?php
function mtlang_ajax_language_save( $ci, $model ) {

	$result['status'] = 'error';

	$result['message'] = 'Lưu dữ liệu thất bại';

	if( InputBuilder::Post() ) {

		$check = true;

		$data = InputBuilder::Post();

		$language = Option::get('language', MultiLanguage::default() );

		$new = array();

		$key = Str::clear($data['language_locale']);

		$new['label'] = Str::clear($data['language_label']);

		$new['flag'] = FileHandler::handlingUrl( $data['language_flag'] );

		if( isset($language[$key]) ) $check = false;;

		$language[$key] = $new;

		Option::update('language', $language );

		$result['check'] 	= $check;

		$result['status'] 	= 'success';

		$result['message'] 	= 'Lưu dữ liệu thành công.';
	}

	echo json_encode( $result );

}

register_ajax_admin('mtlang_ajax_language_save');

function mtlang_ajax_language_active( $ci, $model ) {

	$result['type'] = 'error';

	$result['message'] = 'Lưu dữ liệu thất bại';

	if( InputBuilder::Post() ) {

		$data = InputBuilder::Post();

		$language = get_option('language', MultiLanguage::default() );

		$key = Str::clear($data['key']);

		if( isset($language[$key]) ) {

			update_option('language_default', $key );

			$result['type'] 	= 'success';

			$result['message'] 	= 'Lưu dữ liệu thành công.';
		}
	}

	echo json_encode( $result );

}

register_ajax_admin('mtlang_ajax_language_active');

function mtlang_ajax_language_delete( $ci, $model ) {

	$result['type'] = 'error';

	$result['message'] = 'Xóa dữ liệu thất bại';

	if( InputBuilder::Post() ) {

		$data = InputBuilder::Post();

		$language 			= get_option('language', MultiLanguage::default() );

		$language_default 	= get_option('language_default');

		$key = Str::clear($data['key']);

		if( $language_default == $key ) {

			$result['message'] 	= 'Không thể xóa ngôn ngữ mặc định.';
		}
		else if( isset($language[$key]) ) {

			unset($language[$key]);

			update_option('language', $language );

			$result['type'] 	= 'success';

			$result['message'] 	= 'Xóa dữ liệu thành công.';
		}
	}

	echo json_encode( $result );

}

register_ajax_admin('mtlang_ajax_language_delete');


function mtlang_ajax_translate_save( $ci, $model ) {

	$result['type'] = 'error';

	$result['message'] = 'Lưu dữ liệu thất bại';

	if( InputBuilder::Post() ) {

		$type 			= Str::clear(InputBuilder::Post('name'));

		$key 			= Str::clear(InputBuilder::Post('key'));

		$value_new 		= Str::clear(InputBuilder::Post('value'));

		$value_old 		= Str::clear(InputBuilder::Post('pk'));

		$language_key 	= Str::clear(InputBuilder::Post('language'));

		$translate		= MultiLanguage::translate( $language_key );

		if( $type == 'key' ) {

			if( isset($translate[$value_old]) ) {

				$translate[$value_new] = $translate[$value_old];

				unset($translate[$value_old]);
			}
		}

		if( $type == 'label' ) {

			if( isset($translate[$key]) ) {

				$translate[$key] = $value_new;
			}
		}

		if( MultiLanguage::saveTranslate( $translate, $language_key ) ) {

			$result['type'] 	= 'success';

			$result['message'] 	= 'Lưu dữ liệu thành công.';

		}
	}

	echo json_encode( $result );

}

register_ajax_admin('mtlang_ajax_translate_save');

function mtlang_ajax_translate_add( $ci, $model ) {

	$result['type'] = 'error';

	$result['message'] = 'Lưu dữ liệu thất bại';

	if( InputBuilder::Post() ) {

		$key 		= Str::clear(InputBuilder::Post('key'));

		$label 		= Str::clear(InputBuilder::Post('label'));

		$language_key 	= Str::clear(InputBuilder::Post('language'));

		$translate		= MultiLanguage::translate( $language_key );

		$translate[$key] = $label;

		if( MultiLanguage::saveTranslate( $translate, $language_key ) ) {

			$result['type'] 	= 'success';

			$result['message'] 	= 'Lưu dữ liệu thành công.';

		}
	}

	echo json_encode( $result );

}

register_ajax_admin('mtlang_ajax_translate_add');