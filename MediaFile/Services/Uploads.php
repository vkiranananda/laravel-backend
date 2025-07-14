<?php
namespace Backend\Root\MediaFile\Services;

use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Intervention\Image\Facades\Image as Image;
use Auth;
use Request;
use Storage;

class Uploads
{
  public static function saveFile($file, $conf)
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    $mediaFile = new MediaFile;
    $mediaFile->disk = $conf['disk'] ?? 'uploads';
    $mediaFile->parent_id = $conf['parentId'] ?? 0;
    $mediaFile->user_id = $conf['user-id'] ?? Auth::user()->id;
    $mediaFile->orig_name = $conf['orig_name'] ?? $file->getClientOriginalName();
    $mediaFile->path = isset($conf['path']) ? $conf['path'] : self::generatePath();
    $mediaFile->key = self::generateKey();

    // Если имя файла задано, то генерируем уникальное имя файла
    if (isset($conf['name'])) {
      $mediaFile->name = self::generateFileName(
        $mediaFile->path,
        self::fileNameNormalize($conf['name']),
        $mediaFile->disk
      );
    } else {
      // Если имя файла не задано, то генерируем уникальное случайное имя файла 
      $mediaFile->name = self::generateFileNameRandom(
        $mediaFile->path,
        $mediaFile->orig_name,
        $mediaFile->disk
      );
    }

    $mediaFile->extension = strtolower(self::parceFileName($mediaFile->name)['extension']);
    $mediaFile->size = $file->getSize();
    $mediaFile->array_data = [];

    // Определяем тип файла
    if (array_search($mediaFile->extension, ['jpeg', 'png', 'gif', 'jpg'], true) !== false) {
      $mediaFile->type = 'image';
    } else {
      $mediaFile->type = 'file';
    }

    // return $mediaFile;

    $mediaFile->save();
    // Сохраняем файл
    $file->storeAs($mediaFile->path, $mediaFile->name, $mediaFile->disk);

    return $mediaFile;
  }

  // Проверяем существование файла
  public static function fileExists($path, $name, $disk)
  {
    return MediaFile::where('disk', $disk)
      ->where('path', $path)
      ->where('name', $name)
      ->exists();
  }

  // Генерируем уникальный путь
  public static function generatePath()
  {
    // Генерируем уникальный путь из 16-ричных символов, например a1/c2
    $hex1 = dechex(random_int(0, 255));
    $hex2 = dechex(random_int(0, 255));
    // Убедимся, что всегда 2 символа
    $hex1 = str_pad($hex1, 2, '0', STR_PAD_LEFT);
    $hex2 = str_pad($hex2, 2, '0', STR_PAD_LEFT);
    return $hex1 . '/' . $hex2 . '/';
  }

  // Генерируем уникальный ключ
  public static function generateKey()
  {
    while (true) {
      $key = strtolower(\Illuminate\Support\Str::random(20));
      if (MediaFile::where('key', $key)->exists()) {
        continue;
      }
      return $key;
    }
  }

  // Генерируем уникальное имя файла с учетом имени файла
  public static function generateFileName($path, $name, $disk)
  {
    // Получаем расширение файла в родном регистре
    $file = self::parceFileName($name);
    $extension = ($file['extension'] !== '' ? '.' . $file['extension'] : '');

    // Генерируем уникальное имя файла
    $key = 0;
    while (true) {
      $newName = $file['filename'] . ($key > 0 ? '-' . $key : '') . $extension;
      if (self::fileExists($path, $newName, $disk)) {
        $key++;
        continue;
      }
      return $newName;
    }
  }

  // Генерируем уникальное имя файла случайное
  public static function generateFileNameRandom($path, $name, $disk)
  {
    // Получаем расширение файла в родном регистре
    $extension = self::parceFileName($name)['extension'];
    $extension = ($extension !== '' ? '.' . $extension : '');

    // Генерируем уникальное имя файла
    while (true) {
      $randomName = strtolower(\Illuminate\Support\Str::random(15)) . $extension;
      if (self::fileExists($path, $randomName, $disk)) {
        continue;
      }
      return $randomName;
    }
  }

  // Нормализуем имя файла. Удаляем ../, ..\\, /, \\ и т.д.
  public static function fileNameNormalize($name)
  {
    // Удаляем ../, ..\\, /, \\ и т.д.
    $name = str_replace(['/', '\\', '<', '>', ':', '*', '"', '|', '?', '\0'], '', $name);

    // Имя состоит только из точек
    if (preg_match('/^\.+$/', $name)) {
      abort(400, 'Имя файла не может состоять только из точек');
    }

    if ($name === '') {
      abort(400, 'Имя файла не может быть пустым');
    }

    $name = self::parceFileName($name);
    // Ограничиваем длину
    $name['filename'] = mb_substr($name['filename'], 0, 200);
    $name['extension'] = mb_substr($name['extension'], 0, 10);

    return $name['filename'] . ($name['extension'] !== '' ? '.' . $name['extension'] : '');
  }

  // Парсим имя файла и расширение.
  private static function parceFileName($name)
  {
    $dotPos = strrpos($name, '.');
    if ($dotPos === false || $dotPos === 0) {
      // Нет расширения или файл начинается с точки (скрытый файл без расширения)
      return ['filename' => $name, 'extension' => ''];
    }
    return [
      'filename' => substr($name, 0, $dotPos),
      'extension' => substr($name, $dotPos + 1)
    ];
  }

  // Преобразуем массив с размеров в строку...
  public static function sizesToStr($size)
  {
    if (count($size) < 2)
      return '';
    $res = $size[0] . 'x' . $size[1];
    $res .= (isset($size[2]) && $size[2] == 'fit') ? '-fit' : '';
    return $res;
  }

  // Генерируем различные размеры
  public static function genSizes(&$file, $sizes, $tmpFile = false)
  {
    $res = array();
    $disk = Storage::disk($file['disk']);
    $orig = false;

    $loadedFile = ($tmpFile) ? file_get_contents($tmpFile) : $disk->get($file['path'] . $file['file']);

    foreach ($sizes as $value) {
      $img = Image::make($loadedFile);

      // Оригинальные размеры, что бы потом можно было проверить был ли изменен файл
      if ($orig === false) {
        if (!isset($file['sizes']['orig'])) {
          $res['orig']['size'] = [$img->width(), $img->height()];
          $res['orig']['file'] = $file['file'];
          $res['orig']['path'] = '';
          $orig = $res['orig'];
        } else {
          $orig = $file['sizes']['orig'];
        }
      }

      $sizeStr = Uploads::sizesToStr($value);
      if (isset($value[2]) && $value[2] == 'fit') {
        $img->fit($value[0], $value[1], function ($constraint) {
          $constraint->upsize();
        });
      } else {
        if ($value[0] == 'auto')
          $value[0] = null;
        if ($value[1] == 'auto')
          $value[1] = null;
        $img->resize($value[0], $value[1], function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        });
      }

      // Если файл не был изменен не сохраяем его...
      if (($img->width() == $orig['size'][0]) && ($img->height() == $orig['size'][1])) {
        $res[$sizeStr] = $orig;
        continue;
      }

      $res[$sizeStr]['size'] = [$img->width(), $img->height()];

      // Сохраняем только в jpg формате
      $res[$sizeStr]['path'] = 'sizes/' . $sizeStr . '/';
      $res[$sizeStr]['file'] = pathinfo($file['file'])['filename'] . '.jpg';

      $disk->put(
        $file['path'] . $res[$sizeStr]['path'] . $res[$sizeStr]['file'],
        $img->encode('jpg', 100)
      );
    }

    return $res;
  }

  // получаем индивидуальное имя. Можно вообще брать ID записи и сохранять под ним, будет быстрее и проще и не надо ничего проверять :)
  private static function getIndividualName(&$newFile, $name)
  {
    $fInfo = pathinfo($name);

    $fInfo['extension'] = (isset($fInfo['extension'])) ? '.' . $fInfo['extension'] : '';

    // Получаем список файлов в каталоге
    $filesExists = [];
    foreach (MediaFile::where('disk', $newFile['disk'])->where('path', $newFile['path'])->get(['file']) as $file) {
      $filesExists[$file['file']] = '';
    }
    $index = 1;

    // Ищем подходящее имя файла
    while (isset($filesExists[$name])) {
      $name = $fInfo['filename'] . '-' . $index++ . $fInfo['extension'];
    }

    return $name;
  }

  // Удаляет массив файлов
  public static function deleteFiles($files)
  {
    foreach ($files as $file) {
      // Удаляем основной файл
      Storage::disk($file['disk'])->delete($file['path'] . $file['file']);

      if (!is_array($file['sizes']))
        continue;
      // Удаляем миниатюры
      foreach ($file['sizes'] as $fileSizes) {
        Storage::disk($file['disk'])->delete($file['path'] . $fileSizes['path'] . $fileSizes['file']);
      }
      // Удаляем из базы
      MediaFile::destroy($file['id']);
    }
  }
}

?>
