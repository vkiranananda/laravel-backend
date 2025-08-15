<?php

namespace Backend\Option\Controllers;

class OptionController extends \Backend\Root\Option\Controllers\OptionController
{
  use \Backend\Root\User\Services\UserAccessTrait;
  protected string $userAccessKey = 'Option';
}
