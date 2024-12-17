<?php
if(!\request()->ajax())
{
    return;
}
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;

class AdminLanguageAjax {
    #[NoReturn]
    static function save(Request $request, $model): void
    {
        $validate = $request->validate([
            'label' => Rule::make(trans('system.form.label'))->notEmpty(),
            'locale' => Rule::make(trans('system.form.locale'))->notEmpty(),
            'flag' => Rule::make(trans('system.form.flag'))->notEmpty(),
        ]);

        if ($validate->fails()) {
            response()->error($validate->errors());
        }

        $dataInsert = [];

        $dataInsert['label'] = Str::clear($request->input('label'));

        $dataInsert['locale'] = Str::clear($request->input('locale'));

        $dataInsert['flag'] = $request->input('flag');

        $language = Option::get('language', LangHelper::default());

        //Thêm mới
        if(!empty($request->input('id'))) {

            $id = Str::clear($request->input('id'));

            if($id != $dataInsert['locale']) {
                response()->error(trans('ajax.lang.save.error.locale'));
            }

            if(!isset($language[$id])) {
                response()->error(trans('ajax.lang.save.error.notFound'));
            }
        }

        $language[$dataInsert['locale']] = $dataInsert;

        Option::update('language', $language);

        $dataInsert['flag'] = LangHelper::flagLink($dataInsert['flag']);

        response()->success(trans('ajax.save.success'), $dataInsert);
    }
    #[NoReturn]
    static function active(Request $request): void {

        if(empty($request->input('key')))
        {
            response()->error(trans('ajax.lang.active.error.default'));
        }

        $language = Option::get('language', LangHelper::default());

        $defaultNew = Str::clear($request->input('key'));

        if(!isset($language[$defaultNew]))
        {
            response()->error(trans('ajax.lang.active.error.notFound'));
        }

        Option::update('language_default', $defaultNew);

        \SkillDo\Model\Language::where('language', $defaultNew)->delete();

        \SkillDo\Cache::flush();

        response()->success(trans('ajax.save.success'));
    }
    #[NoReturn]
    static function delete(Request $request): void
    {
        $key = $request->input('data');

        $key = Str::clear($key);

        $language 			= Option::get('language', LangHelper::default());

        $language_default 	= Option::get('language_default');

        if($language_default == $key)
        {
            response()->error(trans('ajax.lang.delete.error.default'));
        }
        else if(isset($language[$key])) {

            unset($language[$key]);

            Option::update('language', $language);

            SkillDo\Model\Language::where('language', $key)->remove();

            response()->success(trans('ajax.delete.success'), [$key]);
        }

        response()->error(trans('ajax.delete.error'));

    }
    #[NoReturn]
    static function setting(Request $request): void
    {
        $validate = $request->validate([
            'language_display' => Rule::make('Hiển thị ngôn ngữ')->notEmpty(),
            'language_switcher_display' => Rule::make('Kiểu hiển thị')->notEmpty(),
        ]);

        if ($validate->fails()) {
            response()->error($validate->errors());
        }

        $dataInsert = LangHelper::setting();

        $dataInsert['display'] = Str::clear($request->input('language_display'));

        $dataInsert['switcher'] = Str::clear($request->input('language_switcher_display'));

        $dataInsert['bg'] = Str::clear($request->input('language_color_bg'));

        $dataInsert['bgHover'] = Str::clear($request->input('language_color_bg_hover'));

        $dataInsert['txt'] = Str::clear($request->input('language_color_txt'));

        $dataInsert['txtHover'] = Str::clear($request->input('language_color_txt_hover'));

        Option::update('language_settings', $dataInsert);

        LangHelper::buildCss();

        response()->success(trans('ajax.save.success'));
    }
    #[NoReturn]
    static function translateLoad(Request $request): void
    {
        if(empty($request->input('language')))
        {
            response()->error(trans('ajax.translate.load.error.empty'));
        }

        $languageKey = Str::clear($request->input('language'));

        $language = Language::list();

        if(empty($language[$languageKey]))
        {
            response()->error(trans('ajax.translate.load.error.notFound'));
        }

        $lang = LangHelper::loadCustom([], $languageKey);

        response()->success(trans('ajax.load.success'), $lang);
    }
    #[NoReturn]
    static function translateSave(Request $request): void
    {
        $type 			= Str::clear($request->input('name'));

        $key 			= Str::clear($request->input('key'));

        $value_new 		= Str::clear($request->input('value'));

        $value_old 		= Str::clear($request->input('pk'));

        $languageKey 	= Str::clear($request->input('language'));

        $langCustom = LangHelper::loadCustom([], $languageKey);

        if(empty($langCustom))
        {
            response()->error(trans('ajax.translate.save.error.empty'));
        }

        //edit key language
        if($type == 'key')
        {
            if(!isset($langCustom[$value_old]))
            {
                response()->error(trans('ajax.translate.save.error.notFoundOldKey'));
            }
            $langCustom[$value_new] = $langCustom[$value_old];
            unset($langCustom[$value_old]);
        }

        //edit trans language
        if($type == 'label')
        {
            if(!isset($langCustom[$key]))
            {
                response()->error(trans('ajax.translate.save.error.notFoundOldKey'));
            }
            $langCustom[$key] = $value_new;
        }

        LangHelper::saveTrans($langCustom, $languageKey);

        response()->success(trans('ajax.save.success'));
    }
    #[NoReturn]
    static function translateAdd(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $validate = $request->validate([
                'key'       => Rule::make('Key language')->notEmpty(),
                'label'     => Rule::make('Bản dịch')->notEmpty(),
                'language'  => Rule::make('Ngôn ngữ')->notEmpty(),
            ]);

            if ($validate->fails()) {
                response()->error($validate->errors());
            }

            $key 		    = Str::clear($request->input('key'));

            $label 		    = Str::clear($request->input('label'));

            $langKey 	    = Str::clear($request->input('language'));

            $translate		= LangHelper::loadCustom([], $langKey);

            if(isset($translate[$key])) {
                response()->success(trans('ajax.translate.add.error.existsKey'));
            }

            $translate[$key] = $label;

            LangHelper::saveTrans($translate, $langKey);

            response()->success(trans('ajax.save.success'));
        }

        response()->error(trans('ajax.save.error'));
    }
    #[NoReturn]
    static function buildJs(Request $request, $model): void {

        if($request->isMethod('post')) {

            Language::buildJs();

            response()->success(trans('ajax.update.success'));
        }

        response()->error(trans('ajax.update.error'));
    }
}
Ajax::admin('AdminLanguageAjax::save');
Ajax::admin('AdminLanguageAjax::active');
Ajax::admin('AdminLanguageAjax::delete');
Ajax::admin('AdminLanguageAjax::setting');
Ajax::admin('AdminLanguageAjax::translateLoad');
Ajax::admin('AdminLanguageAjax::translateSave');
Ajax::admin('AdminLanguageAjax::translateAdd');
Ajax::admin('AdminLanguageAjax::buildJs');