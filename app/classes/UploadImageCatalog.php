<?php
namespace App\classes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
class UploadImageCatalog
{

    public function Upload($id, $image)
    {

        // Caminho para o diretório onde as imagens do produto serão armazenadas
        $path = public_path("produtos/image/{$id}/");

        // Verificar se o diretório do produto já existe
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $image = explode(",", $image);
        $ext = "";
        $extension = collect(['gif', 'png', 'jpg', 'jpeg']);

        $part1 = substr($image[0], strpos($image[0], '/') + 1);
        $ext = str_replace(";base64", "", $part1);

        if ($extension->contains($ext)) {
            $decode = base64_decode($image[1]);
            $img = Image::make($decode);
            $imagem = $img->encode($ext, 80);
            $filename = Str::random() . "." . $ext;
            // pegar o local para salvar a imagem
            $patch = public_path("produtos/image/{$id}/" . $filename);
            // Realizar o upload
            if (file_put_contents($patch, $imagem)) {
                return $filename;
            }
        }
    }
}
?>
