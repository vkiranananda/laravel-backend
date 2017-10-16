<div class="modal fade" <?=(isset($params['id'])) ? "id='".$params['id']."'" : '' ?>>
  <div class="modal-dialog {{ isset($params['large']) ? 'modal-lg' : ''}}" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $title }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ $slot }}
      </div>
      <div class="modal-footer">
        {{ $footer }}
      </div>
    </div>
  </div>
</div>