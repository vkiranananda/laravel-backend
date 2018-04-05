@if (count($data) == 0)
  <p class="pt-1">Список пуст</p>
@else
  {{ csrf_field() }}

  <table class="table table-hover list" id="table-list">
      <thead>
          <tr class="table-success">
            @foreach ($params['list'] as $key => $field)
              <?php
                if( isset($params['fields'][$field['name']]) )
                  $params['list'][$key] = array_replace($params['fields'][$field['name']], $field);
              ?>
              <th class="align-middle" scope="col" {!! isset($field['attr']) ? Forms::attributes($field['attr']) : '' !!} >{{ isset($params['list'][$key]['label']) ? $params['list'][$key]['label'] : '' }}</th>
            @endforeach

            @if(!isset($params['conf']['list-no-actions']))
              <th scope="col" class="menu-td"></th>
            @endif
          </tr>
      </thead>
      <tbody>
      @foreach ($data as $el)
        <tr >
        @foreach ($params['list'] as $field)
          <td {!! isset($field['conf-list-td-attr']) ? $field['conf-list-td-attr'] : '' !!} >
            @if(isset($field['icon']))
              <span class="icons-{{$field['icon']}}"></span>&nbsp;
            @endif
            @if(isset($field['html']))
              {!! isset($el[$field['name']]) ? $el[$field['name']] : '' !!}
            @elseif( isset($field['link']) ) 
              @if ( $field['link'] == 'edit')
                <a href="{{ action($params['controllerName'].'@edit', $el['id']) }}" >
                  {{ Content::getFieldValue($el,$field) }}
                </a>
              @endif
            @else
              {{ Content::getFieldValue($el,$field) }}
            @endif
          </td>
        @endforeach


        @if(!isset($params['conf']['list-no-actions']))
          <td class="menu-td">
            <div class="dropdown">
              <button class="btn btn-secondary button-grabber" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="{{ action($params['controllerName'].'@edit', $el['id']) }}"><span class="icons-pencil"></span>&nbsp;Править</a>
                <a class="dropdown-item" href="{{ action($params['controllerName'].'@destroy', $el['id']) }}" data-role="delete-record"><span class="icons-trash"></span>&nbsp;Удалить</a>
              </div>
            </div>
          </td>
        @endif
        </tr>
      @endforeach
      </tbody>
  </table>
  {!! $data->appends(Request::All())->links('Form::components.pagination') !!}
@endif
@if(isset($params['sortable']))
	<the-list-sortable></the-list-sortable >
@endif