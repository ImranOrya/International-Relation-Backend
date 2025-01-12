<?php

namespace App\Http\Controllers\api\app\news;

use App\Enums\LanguageEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\app\news\NewsStoreRequest;
use App\Models\News;
use App\Models\NewsDocument;
use App\Models\NewsTran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    //

    public function news(Request $request){

       $locale = App::getLocale();

       $query = News::with([
        'newsTran' =>function ($query) use ($locale){
            $query->where('language_name',$locale)->select('id','news_id','')
        }
       ]);
       
       
    
       

    }
    public function showNews(Request $request){

       $locale =App::getlocale();

      $query =  News::with([
           'newsTran' => function ($query) use ($locale){
            $query->where('language_name',$locale)->select('id','news_id','contents');
           },
           'priority:id,name',
           'newsType:id,name'
           
       ] )->where('visible',1)->where('submited',1);
    

       return $query->get();

    }


public function store(NewsStoreRequest $request)
{
    $validatedData = $request->validated();
    $user_id = Auth::id(); // Get authenticated user's ID
    
    DB::beginTransaction();
    
    try {
        $news ='';
        if($request->news_id != ''){
            $news = News::findOrFail($request->news_id);
            $news->update([
             'news_type_id' => $validatedData['news_type_id'],
            'priority_id' => $validatedData['priority_id'],
            'user_id' => $user_id,
            'visible' => 1,
            'expiry_date' => $validatedData['expiry_date'],
            'submited' => 1
            ]);
        }else{
        // Create News
        $news = News::create([
            
            'news_type_id' => $validatedData['news_type_id'],
            'priority_id' => $validatedData['priority_id'],
            'user_id' => $user_id,
            'visible' => 1,
            'expiry_date' => $validatedData['expiry_date'],
            'submited' => 1

        ]);
    }
        // Create NewsTrans (translations)
        $languages = [
            ['name' => LanguageEnum::default->value, 'content' => $validatedData['contents_en']],
            ['name' => LanguageEnum::pashto->value, 'content' => $validatedData['contents_ps']],
            ['name' => LanguageEnum::farsi->value, 'content' => $validatedData['contents_fa']],
        ];
        foreach ($languages as $language) {
            NewsTran::create([
                'news_id' => $news->id,
                'language_name' => $language['name'],
                'contents' => $language['content'],
            ]);
        }

        // Create NewsDocuments
    

        // Commit transaction
        DB::commit();

        // Return a success response
           return response()->json(
            [
                'message' => __('app_translation.success'),
            
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );

    } catch (\Exception $e) {
        // Rollback transaction on error
        DB::rollBack();
        return response()->json([
            'message' => __('app_translation.server_error')
        ],500);
    }
}


}
