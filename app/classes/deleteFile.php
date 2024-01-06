<?php
namespace App\classes;
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
