<?php

namespace App\Http\Controllers\api\template;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Province;
use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProvinceController extends Controller
{
    //

    public function provinces()
    {

        $locale = App::getLocale();

        $tr = [];
        if ($locale === LanguageEnum::default->value)
            $tr =  Province::select("name", 'id')->get();
        else {
            $tr = $this->getTableTranslations(Province::class, $locale, 'desc');
        }
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function district(Request $request, $provinceId)
    {
        $locale = App::getLocale();
        $tr = [];
        if ($locale === LanguageEnum::default->value)
            $tr =  District::select("name", 'id')->where('province_id', $provinceId)->get();
        else {
            $tr = $this->getTableTranslations(District::class, $locale, 'desc');

            $tr = Translate::join('districts', 'translable_id', '=', 'districts.id')->where('translable_type', '=', District::class)->where('province_id', $provinceId)
                ->where('language_name', '=', $locale)
                ->select('translable_id as id', 'value as name')
                ->get();
        }
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
