<form role="{{ isset($params['formRole']) ? $params['formRole'] : 'formAjax' }}" method="POST" action="{{ $params['url'] }}">
    <div class="card form-edit">
        <div class="card-header">
            {{ $params['lang']['title'] }}
            <br><small id="post-link-area">
            @if(isset($params['viewUrl']) && $params['viewUrl'] != '')
                <a href="{{$params['viewUrl']}}">{{$params['viewUrl']}}</a>
            @endif
            </small>
        </div>
        <div class="card-body">
            <input type="hidden" name="id" value="{{ $data['id'] }}">
            @foreach($params['fields'] as $name => $field)
                @if(isset($field['type']) && $field['type'] == 'hidden')
                    {!! Forms::fieldHTML($field, $data) !!}
                @endif
            @endforeach

            {{ $slot }}

            @if(is_array($params['edit']) )
                @if(count($params['edit']) > 1)
                <!--  Tabs titles -->
                    <ul class="nav nav-tabs" role="tablist">
                    <?php $active = 'active'; ?>
                    @foreach($params['edit'] as $tab)
                      <li class="nav-item">
                        <a class="nav-link {{ $active }}" data-toggle="tab" href="#tab-{{ $tab['id'] }}" role="tab">{{ $tab['tab_name'] }}</a>
                      </li>
                      <?php $active = ''; ?>
                    @endforeach
                    </ul>

                    <!-- Tabs content -->
                    <div class="tab-content pt-3 pb-3">
                    <?php $active = 'active'; ?>
                    @foreach($params['edit'] as $tab)
                        <div class="tab-pane {{ $active }}" id="tab-{{ $tab['id'] }}" role="tabpanel">
                            @if(is_array($tab['fields']) && count($tab['fields']) > 0)
                                @component('Form::components.fields-list', [ 'fields' => &$tab['fields'], 'data' => $data, 'params' => &$params ] ) @endcomponent
                            @endif
                        </div>
                        <?php $active = ''; ?>
                    @endforeach
                    </div>
                @else
                    <?php $tab = array_shift($params['edit']); ?>
                    @if(is_array($tab['fields']) && count($tab['fields']) > 0)
                        @component('Form::components.fields-list', [ 'fields' => &$tab['fields'], 'data' => $data, 'params' => &$params ] ) @endcomponent
                    @endif
                @endif
            @endif
            
            <div class="row text-right">
                <div class="col result-area">
                    <span class="error error-any">Произошла непредвиденная ошибка, попробуйте обновить страницу, если не помогает свяжитесь с администратором сайта.</span>
                    <span class="error error-422">Проверьте правильность заполнения данных</span>
                    <span class="success">Сохранено</span>
                </div>
                <div class="mr-4">
                    @if( isset($params['previousUrl']) )
                         <a class="btn btn-secondary mr-3" href="{{$params['previousUrl']}}" role="button">Назад</a>
                    @else
                         <a class="btn btn-secondary mr-3" href="javascript:history.back()" role="button">Назад</a>
                    @endif
                    <button type="button" class="btn btn-primary" role="submit" data-send-text="Сохраняю...">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
@if( isset($params['conf']['media-files']) )
<upload-file-modal></upload-file-modal>
@endif
</form>
@if( isset($params['conf']['media-files']) )
	<upload-edit-file url="{!!GetConfig::backend('Upload::edit')['conf']['get-info-url']!!}"></upload-edit-file>

@endif


