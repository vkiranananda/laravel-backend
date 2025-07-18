<?php
namespace Backend\Root\MediaFile\Services;

use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Intervention\Image\Facades\Image as Image;
use Auth;
use Log;
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
    $mediaFile->path = isset($conf['path']) ? self::pathNormalize($conf['path']) : self::generatePath();
    $mediaFile->key = self::generateKey();

    // Если имя файла задано, то генерируем уникальное имя файла
    if (isset($conf['name'])) {
      $mediaFile->name = self::generateFileName(
        $mediaFile->path,
        self::fileNameNormalize($conf['name']),
        $mediaFile->disk
      );
      // Получаем расширение файла из имени файла
      $mediaFile->extension = strtolower(self::parceFileName($mediaFile->name)['extension']);
    } else {
      // Получаем расширение файла из оригинального имени файла
      $mediaFile->extension = strtolower(self::parceFileName($mediaFile->orig_name)['extension']);
      // Если имя файла не задано, то генерируем уникальное случайное имя файла
      $mediaFile->name = self::generateFileNameRandom(
        $mediaFile->path,
        $mediaFile->extension,
        $mediaFile->disk
      );
    }

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

  // Создаем папку
  public static function createFolder($conf)
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    $mediaFile = new MediaFile;
    $mediaFile->disk = $conf['disk'];
    $mediaFile->parent_id = $conf['parentId'] ?? 0;
    $mediaFile->user_id = $conf['user-id'] ?? Auth::user()->id;
    $mediaFile->path = self::pathNormalize($conf['path']);
    $mediaFile->type = 'folder';
    $mediaFile->key = self::generateKey();

    $mediaFile->orig_name = $mediaFile->name = self::generateFileName(
      $mediaFile->path,
      self::fileNameNormalize($conf['name']),
      $mediaFile->disk
    );

    // Создаем папку в хранилище
    Storage::disk($mediaFile->disk)->makeDirectory($mediaFile->path . $mediaFile->name);

    $mediaFile->save();
    return $mediaFile;
  }

  // Нормализуем путь. Добавляем / в конец если нет
  public static function pathNormalize($path)
  {
    if ($path != '' && substr($path, -1) !== '/') {
      $path .= '/';
    }
    return $path;
  }

  // Проверяем существование файла
  public static function fileExists($path, $name, $disk)
  {
    return MediaFile::where('disk', $disk)
      ->where('path', $path)
      ->where('name', $name)
      ->exists();
  }

  // Получаем файл
  public static function getFile($path, $name, $disk)
  {
    return MediaFile::where('disk', $disk)
      ->where('path', $path)
      ->where('name', $name)
      ->first();
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
  public static function generateFileNameRandom($path, $extension, $disk)
  {
    // Генерируем уникальное имя файла
    while (true) {
      $randomName = strtolower(\Illuminate\Support\Str::random(15)) . self::getFileExt($extension);
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

  // Получаем расширение файла, если есть вернет расширение с точкой
  // если нет пустую строку.
  private static function getFileExt($extension)
  {
    return ($extension !== '' ? '.' . $extension : '');
  }

  // Получаем миниатюру картинки
  public static function getThumbnail($file, $size)
  {
    // Если файл не изображение, то возвращаем null
    if ($file['type'] !== 'image') {
      return false;
    }

    // Преобразуем массив с размером в строку
    $textSize = self::sizeToStr($size);

    // Получаем данные файла
    $data = $file['array_data'];

    // Если размер уже есть, то возвращаем его
    if (isset($data['sizes'][$textSize])) {
      return $data['sizes'][$textSize];
    }
    Log::info('gen');
    $loadedFile = Storage::disk($file['disk'])->get($file['path'] . $file['name']);
    // Если файл не найден, то возвращаем false
    if (!$loadedFile) {
      return false;
    }

    $img = Image::make($loadedFile);

    // Если размер указан как fit, то используем метод fit
    if (isset($size[2]) && $size[2] == 'fit') {
      $img->fit($size[0], $size[1], function ($constraint) {
        $constraint->upsize();
      });
    } else {
      // Если размер указан как auto, то используем метод resize и сохраняем в jpg формате
      if ($size[0] == 'auto')
        $size[0] = null;
      if ($size[1] == 'auto')
        $size[1] = null;
      $img->resize($size[0], $size[1], function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
      });
    }

    // Сохраняем миниатюру в кеш
    $thumbnail = self::createCacheFileFrom($file, $img->encode('jpg', 100), 'jpg');
    $data['sizes'][$textSize] = self::getFileData($thumbnail);

    $file['array_data'] = $data;
    $file->save();

    return $data['sizes'][$textSize];
  }

  // Получаем данные о файле
  public static function getFileData($file)
  {
    return [
      'path' => $file->path . $file->name,
      'key' => $file->key . self::getFileExt($file->extension),
    ];
  }

  // Создаем кеш файла из другого файла, например для миниатюры
  public static function createCacheFileFrom($fromFile, $file, $extension = '')
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    $mediaFile = new MediaFile;
    $mediaFile->disk = 'uploads';
    $mediaFile->parent_id = $fromFile->id;
    $mediaFile->user_id = $fromFile->user_id;
    $mediaFile->orig_name = $fromFile->orig_name;
    $mediaFile->path = self::generatePath();
    $mediaFile->key = self::generateKey();
    $mediaFile->name = self::generateFileNameRandom($mediaFile->path, $extension, $mediaFile->disk);
    $mediaFile->extension = strtolower($extension);
    $mediaFile->array_data = [];
    $mediaFile->size = strlen($file);
    $mediaFile->type = 'cache';

    $mediaFile->save();

    Storage::disk($mediaFile->disk)->put($mediaFile->path . $mediaFile->name, $file);

    return $mediaFile;
  }

  // Преобразуем массив с размером в строку...
  public static function sizeToStr($size)
  {
    if (count($size) < 2)
      return '';
    $res = $size[0] . 'x' . $size[1];
    $res .= (isset($size[2]) && $size[2] == 'fit') ? '-fit' : '';
    return $res;
  }

  // Удаляем файл. Возвращает массив с файлами которые не удалось удалить или true
  public static function deleteFile($file)
  {
    $res = [];
    // Если папка, то удаляем все файлы в папке
    if ($file->type === 'folder') {
      // Удаляем все файлы в папке
      foreach (MediaFile::where('parent_id', $file->id)->get() as $file) {
        // Рекурсивно удаляем все файлы в папке
        // if (count($res) > 0)
        $res = array_merge($res, self::deleteFile($file));
      }
    } else {
      // Проверяем, используется ли файл в таблице связей
      $relations = MediaFileRelation::where('file_id', $file->id)->get();

      // Если файл используется в таблице связей, то не удаляем его
      if ($relations->count() > 0) {
        $relRes = [];
        foreach ($relations as $relation) {
          // Получаем модель того кто использует файл и id
          $relRes[] = ['type' => $relation->post_type, 'id' => $relation->post_id];
        }
        return ['file' => $file, 'relations' => $relRes];
      }
      // Удаляем все файлы привязанные к этому файлу. Например, миниатюры.
      // К файлу могут быть привязаны только файлы без вложенных файлов.
      foreach (MediaFile::where('parent_id', $file->id)->get() as $cacheFile) {
        // Удаляем "миниатюры" из хранилища
        Storage::disk($file->disk)->delete($cacheFile->path . $cacheFile->name);
        $cacheFile->delete();
      }
    }

    // Если есть ошибки, то возвращаем их
    if (count($res) > 0) {
      return $res;
    }

    // Иначе удаляем файл из хранилища
    if ($file->type !== 'folder') {
      Storage::disk($file->disk)->delete($file->path . $file->name);
    } else {
      Storage::disk($file->disk)->deleteDirectory($file->path . $file->name);
    }
    $file->delete();

    return [];
  }
}

?>
