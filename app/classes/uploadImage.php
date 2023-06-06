<?php
namespace App\classes;
use Illuminate\Support\Str;
class uploadImage
{

    public function Upload($patch, $image, $item = null)
    {
        if ($item != null && $item->image != null) {
            if ($item->image != $image) {
                $path = public_path() . $patch . $item->image;
                if (file_exists($path) && $item->image != 'produto-sem-imagem.png') {
                    unlink($path);
                }
            }
        }

        $image = explode(",", $image);
        $ext = "";
        $extension = collect(['gif', 'png', 'jpg', 'jpeg']);

        $part1 = substr($image[0], strpos($image[0], '/') + 1);
        $ext = str_replace(";base64", "", $part1);

        if ($extension->contains($ext)) {
            $decode = base64_decode($image[1]);
            $filename = Str::random() . "." . $ext;
            // pegar o local para salvar a imagem
            $patch = public_path() . $patch . $filename;
            // Realizar o upload
            if (file_put_contents($patch, $decode)) {
                return $filename;
            }
        }
    }
}
?>
