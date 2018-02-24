@extends('Backend::layouts.admin')

@section('title', 'Корзина')

@section('content')

<div class="card">
  <div class="card-header">Корзина</div>

  <div class="card-body">
      <button type="button" class="btn btn-primary mb-3" data-role='href' href=''>
      	Очистить корзину
      </button>

       @component('Form::components.list', ['params' => $params, 'data' => $dataCats]) @endcomponent

  </div>
</div>

<?($dataCats) ?>
@endsection
