@extends('Backend::layouts.admin')

@section('title', 'Иконки')

@section('content')
<div class="container">
    <h1 class="mb-4">Витрина рабочих иконок</h1>

    @php
    $icons = [
        'upload-cloud',
        'file',
        'close',
        'spinner',
        'home',
        'settings',
        'user',
        'menu',
        'edit',
        'delete',
        'plus',
        'search',
        'check',
        'error',
        'warning',
        'info',
        'arrow-up',
        'arrow-down',
        'arrow-left',
        'arrow-right',
        'back',
        'checklist',
        'pencil',
        'globe',
        'list-unordered',
        'key',
        'terminal',
        'folder',
        'folder-fill',
        'chevron-down',
        'chevron-up',
        'copy',
        'book',
        'people',
    ];
    $sizes = [
        1 => '16px',
        2 => '20px',
        3 => '24px',
        4 => '32px',
        5 => '48px',
    ];
    $colors = [
        'icon-yellow' => 'Жёлтый',
        'icon-orange' => 'Оранжевый',
        'icon-blue'   => 'Синий',
        'icon-green'  => 'Зелёный',
        'icon-red'    => 'Красный',
        'icon-violet' => 'Фиолетовый',
        'icon-brown'  => 'Коричневый',
        'icon-grey'   => 'Серый',
    ];
    @endphp

    <div class="row mt-5">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Имя иконки</th>
                            <th>размер 1</th>
                            <th>размер 2, icon-blue</th>
                            <th>размер 3, icon-green</th>
                            <th>размер 4, icon-yellow</th>
                            <th>размер 5, icon-red</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($icons as $icon)
                        <tr>
                            <td><code>{{ $icon }}</code></td>
                            <td><v-icon name="{{ $icon }}" size="1" /></td>
                            <td><v-icon name="{{ $icon }}" size="2" class="icon-blue" /></td>
                            <td><v-icon name="{{ $icon }}" size="3" class="icon-green" /></td>
                            <td><v-icon name="{{ $icon }}" size="4" class="icon-yellow" /></td>
                            <td><v-icon name="{{ $icon }}" size="5" class="icon-red" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h2 class="h4 mb-3">Цвета иконки</h2>
            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Класс цвета</th>
                            <th>Пример</th>
                            <th>Описание</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colors as $class => $desc)
                        <tr>
                            <td><code>{{ $class }}</code></td>
                            <td><v-icon name="check" size="3" class="{{ $class }}" /></td>
                            <td class="text-muted">{{ $desc }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
