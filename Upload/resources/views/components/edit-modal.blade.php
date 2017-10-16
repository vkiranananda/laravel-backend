@component('Site::components.modal', [ 'params' => ['id' => 'content-uploads-modal-edit' ] ])
    @slot('title')
      Свойства файла
    @endslot
    <form action="">
        <div class="form-group">
            <label for="content-uploads-modal-edit-desc">Описание файла</label>
            <input type="text" class="form-control" name='desc' id="content-uploads-modal-edit-desc">
        </div>
    </form>
    @slot('footer')
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" role="save">Сохранить</button>
    @endslot
@endcomponent
