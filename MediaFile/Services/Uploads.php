<?php
namespace Backend\Root\MediaFile\Services;

use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Intervention\Image\Facades\Image as Image;
use Auth;
use GetConfig;
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
    $mediaFile->path = isset($conf['path']) ? self::pathNormalize($conf['path']) : self::generatePath();
    $mediaFile->key = self::generateKey();

    // Если имя файла задано, то генерируем уникальное имя файла
    if (isset($conf['name'])) {
      $mediaFile->orig_name = $mediaFile->name = self::generateFileName(
        $mediaFile->path,
        self::fileNameNormalize($conf['name']),
        $mediaFile->disk
      );
      // Получаем расширение файла из имени файла
      $mediaFile->extension = strtolower(self::parceFileName($mediaFile->name)['extension']);
    } else {
      if (isset($conf['orig_name'])) {
        $mediaFile->orig_name = self::fileNameNormalize($conf['orig_name']);
      } else {
        $mediaFile->orig_name = self::fileNameNormalize($file->getClientOriginalName());
      }
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

  // Перемещаем файл
  public static function move($file, $path, $name, $parentId)
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    // Нормализуем имя файла и путь
    $name = self::fileNameNormalize($name);
    $path = self::pathNormalize($path);

    if ($file->parent_id === $parentId && $file->name === $name) {
      abort(400, 'Нельзя перемещать в ту же папку');
    }

    $newFile = self::getFile($path, $name, $file->disk);
    // Если файл с таким именем уже существует, то выбрасываем ошибку
    if ($newFile) {
      abort(400, ($newFile->type === 'folder' ? 'Папка' : 'Файл') . ' с таким именем уже существует');
    }

    if ($file->type === 'folder') {
      $dirPath = $file->path . $file->name . '/';
      $toDirPath = $path . $name . '/';
      // Проверяем, что нельзя переместить папку внутрь самой себя
      if (strpos($path, $dirPath) === 0) {
        abort(400, 'Нельзя переместить папку внутрь самой себя');
      }
      // Обновляем все файлы и папки, находящиеся внутри перемещаемой папки, чтобы их path начинался с нового пути
      // Получаем все элементы, у которых path начинается с dirPath и диск совпадает
      $items = MediaFile::where('disk', $file->disk)
        ->where('path', 'like', $dirPath . '%')
        ->get();

      $dirPathLen = strlen($dirPath);
      foreach ($items as $item) {
        // Вычисляем новую часть пути: заменяем dirPath на $path . $name . '/'
        $relativePath = substr($item->path, $dirPathLen);
        $item->path = $toDirPath . $relativePath;
        $item->save();
      }
    }

    // Перемещаем файл в хранилище
    Storage::disk($file->disk)->move($file->path . $file->name, $path . $name);

    $file->path = $path;
    $file->parent_id = $parentId;
    $file->name = $file->orig_name = $name;

    $file->save();

    return $file;
  }

  // Копируем файл
  public static function copy($file, $path, $name, $parentId, $self = false)
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    // Нормализуем имя файла и путь
    $path = self::pathNormalize($path);

    // Генерируем уникальное имя файла
    if ($self === false) {
      $name = self::generateFileName(
        $path,
        self::fileNameNormalize($name),
        $file->disk
      );
      if ($file->type === 'folder') {
        // Проверяем, что нельзя копировать папку внутрь самой себя
        if (strpos($path, $file->path . $file->name . '/') === 0) {
          abort(400, 'Нельзя копировать папку внутрь самой себя');
        }
      }
    }

    $newFile = new MediaFile;
    $newFile->disk = $file->disk;
    $newFile->parent_id = $parentId;
    $newFile->user_id = Auth::user()->id;
    $newFile->path = $path;
    $newFile->name = $name;
    $newFile->size = $file->size;
    $newFile->orig_name = $name;
    $newFile->extension = $file->extension;
    $newFile->type = $file->type;
    $newFile->array_data = [];
    $newFile->key = self::generateKey();
    $newFile->save();

    if ($file->type === 'folder') {
      Storage::disk($file->disk)->makeDirectory($path . $name);
      foreach (MediaFile::where('parent_id', $file->id)->get() as $item) {
        self::copy($item, $path . $name . '/', $item->name, $newFile->id, true);
      }
    } else {
      Storage::disk($file->disk)->copy($file->path . $file->name, $path . $name);
    }

    return $newFile;
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

  // Получаем миниатюру картинки
  public static function getThumbnail(&$file, $size)
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
      return array_merge($data['sizes'][$textSize], ['extension' => 'jpg']);
    }
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

    return array_merge($data['sizes'][$textSize], ['extension' => 'jpg']);
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

  // Переносим файл в uploads, например при удалении файла
  // Если файл уже в uploads, то ничего не делаем
  private static function moveToUploads($file)
  {
    setlocale(LC_ALL, 'ru_RU.utf8');

    if ($file->disk === 'uploads') {
      return false;
    }

    $oldDisk = $file->disk;
    $oldFilePath = $file->path . $file->name;

    $file->disk = 'uploads';
    $file->path = self::generatePath();
    $file->name = self::generateFileNameRandom($file->path, $file->extension, $file->disk);

    // Копируем файл потоково, не загружая в память
    $sourceStream = Storage::disk($oldDisk)->readStream($oldFilePath);
    Storage::disk($file->disk)->writeStream($file->path . $file->name, $sourceStream);
    Storage::disk($oldDisk)->delete($oldFilePath);

    $file->save();

    return $file;
  }

  // Удаляем файл. Возвращает массив с файлами которые не удалось удалить или true
  public static function deleteFile($file)
  {
    // Если папка, то удаляем все файлы в папке
    if ($file->type === 'folder') {
      // Удаляем все файлы в папке
      foreach (MediaFile::where('parent_id', $file->id)->get() as $fileNext) {
        // Рекурсивно удаляем все файлы в папке
        self::deleteFile($fileNext);
      }
    } else {
      // Проверяем, используется ли файл в таблице связей
      if (MediaFileRelation::where('file_id', $file->id)->count() > 0) {
        // Если файл используется в таблице связей, то перемещаем его в uploads
        self::moveToUploads($file);
        return;
      }
      // Удаляем все файлы привязанные к этому файлу. Например, миниатюры.
      // К файлу могут быть привязаны только файлы без вложенных файлов.
      foreach (MediaFile::where('parent_id', $file->id)->get() as $cacheFile) {
        // Удаляем "миниатюры" из хранилища
        Storage::disk($cacheFile->disk)->delete($cacheFile->path . $cacheFile->name);
        $cacheFile->delete();
      }
    }

    // Иначе удаляем файл из хранилища
    if ($file->type !== 'folder') {
      Storage::disk($file->disk)->delete($file->path . $file->name);
    } else {
      Storage::disk($file->disk)->deleteDirectory($file->path . $file->name);
    }
    $file->delete();
  }

  // Получаем массив данных о файле для отображения в списке
  public static function getFileToList(&$file)
  {
    $dateConfig = GetConfig::backend('backend');
    $res = [
      'id' => $file->key,
      'name' => $file->name,
      'orig_name' => $file->orig_name,
      'type' => $file->type,
      'size' => $file->size,
      'timestamp' => $file->created_at->timestamp,
      'dateFormatted' => (new \Carbon\Carbon($file->created_at))
        ->setTimezone($dateConfig['time-zone'])
        ->format($dateConfig['datetime-format']),
    ];

    if ($file->type !== 'folder') {
      $res['url'] = self::getUrl($file);
    }

    $thumbnail = self::getThumbnail($file, ['80', '80', 'fit']);

    if ($thumbnail) {
      $res['thumb'] = self::getUrl($thumbnail);
    }

    return $res;
  }

  // Получаем url файла
  public static function getUrl(&$file)
  {
    return self::getBaseUrl($file['key'] . self::getFileExt($file['extension']));
  }

  // Получаем url файла
  public static function getBaseUrl($fileName = '')
  {
    return route('uploads.get-file', $fileName);
  }

  // Нормализуем путь. Добавляем / в конец если нет
  public static function pathNormalize($path)
  {
    if ($path != '' && substr($path, -1) !== '/') {
      $path .= '/';
    }
    return $path;
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

  // Получаем данные о файле
  public static function getFileData($file)
  {
    return [
      'path' => $file->path . $file->name,
      'key' => $file->key,
    ];
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
  public static function parceFileName($name)
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
  public static function getFileExt($extension)
  {
    return ($extension !== '' ? '.' . $extension : '');
  }
}

?>
