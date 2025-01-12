<?php

namespace App\Http\Controllers\api\template;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function changeLocale($locale)
    {
        try {
            // Set the language in a cookie
            if ($locale === "en" || $locale === "fa" || $locale === "ps") {
                // 1. Set app language
                App::setLocale($locale);
                return response()->json([
                    'message' => __('app_translation.lang_change_success'),
                ], 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                // 3. Passed language not exist in system
                response()->json([
                    'message' => __('app_translation.lang_change_failed'),
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            response()->json([
                'message' => __('app_translation.server_error'),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
    public function font($direction)
    {
        try {
            $path = storage_path("app/public/fonts/");
            if ($direction === "rtl") {
                $path = $path . "nazanin.ttf";

                if (!file_exists($path)) {
                    return response()->json("Not found");
                }
                return response()->file($path);
            }
            return response()->json(["message" => "Not allowed", 405]);
        } catch (Exception $err) {
            Log::info('User login error =>' . $err->getMessage());
            return response()->json(['message' => "Something went wrong please try again later!"], 500);
        }
    }
    public function contries()
    {
        $locale = App::getLocale();
        $tr = [];
        if ($locale === LanguageEnum::default->value) {
            $tr = Country::select('id', 'name')->get();
        } else {
            $tr = $this->getTableTranslations(Country::class, $locale, 'asc');
        }
        return response()->json($tr);
    }
    public function provinces(Request $request)
    {
        $request->validate([
            'countryId' => 'required',
        ]);
        $countryId = $request->input('countryId');

        $locale = App::getLocale();
        $tr = [];
        if ($locale === LanguageEnum::default->value) {
            $tr = Province::where('country_id', '=', $countryId)->select('id', 'name', 'country_id')->get();
        } else {
            $tr = $this->getTableTranslations(Country::class, $locale, 'asc');
        }
        return response()->json($tr);
    }
    public function districts(Request $request)
    {
        $request->validate([
            'provinceId' => 'required',
        ]);
        $provinceId = $request->input('provinceId');

        $locale = App::getLocale();
        $tr = [];
        if ($locale === LanguageEnum::default->value) {
            $tr = District::where('province_id', '=', $provinceId)->select('id', 'name', 'province_id')->get();
        } else {
            $tr = $this->getTableTranslations(Country::class, $locale, 'desc');
        }
        return response()->json($tr);
    }
}
