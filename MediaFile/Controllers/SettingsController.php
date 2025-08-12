<?php

namespace Backend\Root\MediaFile\Controllers;

use Backend\Root\MediaFile\Models\MediaFile;
use App\Models\User;
use Backend\User\Models\UserRole;
use Auth;

class SettingsController extends \Backend\Root\Option\Controllers\OptionResourcesController
{
  protected $configPath = 'MediaFile::settings-config';
  protected $fieldsPath = 'MediaFile::settings-fields';
  protected $configRoot = true;

  // Разрешаем доступ только для админа
  public function __construct()
  {
    if (Auth::user()->user_role_id != 0) {
      abort(403, 'Доступ запрещен');
    }
    parent::__construct();
  }

  public function resourceCombine($type)
  {

    foreach (MediaFile::where('disk', 'filemanager')->where('parent_id', 0)->where('type', 'folder')->get(['id', 'name']) as $folder) {
      $this->fields['fields']['policy']['fields']['folder']['options'][] = [
        'value' => $folder->id,
        'label' => $folder->name,
      ];
    }

    foreach (User::all(['id', 'name']) as $user) {
      $this->fields['fields']['policy']['fields']['user']['options'][] = [
        'value' => $user->id,
        'label' => $user->name,
      ];
    }
    foreach (UserRole::all(['id', 'name']) as $role) {
      $this->fields['fields']['policy']['fields']['role']['options'][] = [
        'value' => $role->id,
        'label' => $role->name,
      ];
    }
  }
}
