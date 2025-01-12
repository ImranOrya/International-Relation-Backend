<?php

namespace App\Http\Controllers\api\app\file;

use App\Http\Controllers\Controller;
use App\Models\CheckList;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class FileController extends Controller
{
    //

// documentation from this site https://shouts.dev/articles/laravel-upload-large-file-with-resumablejs-and-laravel-chunk-upload



    public function fileUpload(Request $request)
        {

         $request->validate([
        'check_list_id' => 'required|integer',
        'file' => [
            'required',
            'file',
            function ($attribute, $value, $fail) use ($request) {
                $checkListId = $request->input('check_list_id');
                
                // Fetch allowed extensions based on `check_list_id`.
                $allowedExtensions = CheckList::find($checkListId)?->file_extensions; 
                // Assume file_extensions is an array, e.g., ['pdf', 'docx']

                if (!$allowedExtensions || !in_array($value->getClientOriginalExtension(), $allowedExtensions)) {
                    $fail("The $attribute must be a file of type: " . implode(', ', $allowedExtensions) . '.');
                }
            },
        ],
    ]);

        // create the file receiver

      $checklist =  CheckList::find($request->check_list_id);

      $checklist_name =   $checklist->name;
      
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile(),$checklist_name);
        }

        // we are in chunk mode, lets send the current progress
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }



    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return JsonResponse
     */
    protected function saveFile(UploadedFile $file,$checklist_name)
    {
      

         $extension = $file->getClientOriginalExtension();
        $filename = str_replace("." . $extension, "", $file->getClientOriginalName()); // Filename without extension


        $fileName = $filename . "_" . md5(time()) . "." . $extension;

    
        // Build the file path
       
        
        $userId= Auth::user()->id;

        $finalPath = storage_path("app/private/temp/" . $userId.'/'.$checklist_name);

        // move the file name
        $file->move($finalPath, $fileName);

        return response()->json([
            'path' => asset('storage/' . $finalPath),
            'name' => $fileName,
            'orginal_name' =>$filename
            
        ]);
    }

 
  
 


}




