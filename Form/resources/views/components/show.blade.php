<div class="card form-edit">
    <div class="card-header">
        {{ $params['lang']['title'] }}
    </div>
    <div class="card-body">

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
                            @component('Form::components.show-fields', [ 'fields' => &$tab['fields'], 'data' => $data, 'params' => &$params ] ) @endcomponent
                        @endif
                    </div>
                    <?php $active = ''; ?>
                @endforeach
                </div>
            @else
                <?php $tab = array_shift($params['edit']); ?>
                @if(is_array($tab['fields']) && count($tab['fields']) > 0)
                    @component('Form::components.show-fields', [ 'fields' => &$tab['fields'], 'data' => $data, 'params' => &$params ] ) @endcomponent
                @endif
            @endif
        @endif
        
        <div class="row text-right">
            <div class="text-right col-12 mr-4">
                <a class="btn btn-primary mr-3" href="{{$params['url']}}" role="button">Изменить</a>
            </div>
        </div>
    </div>
</div>


