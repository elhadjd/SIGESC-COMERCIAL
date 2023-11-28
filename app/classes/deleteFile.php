<?php
namespace App\classes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
class DeleteFile
{

    public function delete($patch)
    {
        $path = public_path() . $patch;
        if (file_exists($path)){
            unlink($path);
        }
    }
    
}
?>
