<?php

namespace App\Http\Controllers;

use App\Models\PaymentImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class ImageController extends Controller
{
    protected string $folder;

    public function storeImage(UploadedFile $image, Model $modelClass): int
    {
        if ($image) {
            $fileName = strtoupper(Str::random(6).'_'.now()->format('YmdHis'));
            $fileName = basename($fileName).'.png';

            $image->storeAs($this->folder, $fileName, 'public_uploads');

            $data = [
                'path' => $this->folder.'/'.$fileName,
            ];

            if ($modelClass instanceof PaymentImage) {
                $data['user_id'] = auth()->id();
            }

            $savedImage = $modelClass::create($data);

            return $savedImage->id;
        }

        return 0;
    }

    public function deleteImage(string $id, Model $modelClass): void
    {
        $image = $modelClass::findOrFail($id);

        if (Storage::disk('public_uploads')->exists($image->path)) {
            Storage::disk('public_uploads')->delete($image->path);
        }

        $image->delete();
    }
}
