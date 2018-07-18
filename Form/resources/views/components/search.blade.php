
@foreach ( $params['search'] as $key => $field)
<form action="{!!action($params['controllerName'].'@index')!!}" method="GET">
<div class="card mb-3">
  <div class="card-body pb-2">
    <h6 class="card-subtitle mb-2">Поиск</h6>
   	<div class="form-row">
<?php
	
    if( !isset($field['type']) ) continue;

    if($field['type'] == 'html') {
        echo $field['html'];
        continue;
    } 
	if( !isset($field['name']) ) continue;
	 
    if(!isset($field['attr']['class']))$field['attr']['class'] = '';
    
    if(in_array($field['type'], ['date', 'email', 'text', 'tel', 'textarea', 'mce', 'password', 'select']))
    {
        $field['attr']['class'] .= " form-control";
    }elseif(in_array($field['type'], ['radio', 'checkbox']))
    {
        $field['attr']['class'] .= " form-check-input";
    }
 
?>
	    <div class="form-group {{ (isset($field['conteiner-class'])) ? $field['conteiner-class'] : 'col-auto'}}" id="{{$field['attr']['id']}}-block">
	    	@if(isset($field['label']))
	    		<label for="{{$field['attr']['id']}}">{{$field['label']}}</label>
	    	@endif
	      	{!!Forms::fieldHTML($field, $data)!!}
	    </div>
	    @if ($loop->last)
        	<div class="col-auto">
      			<button type="submit" class="btn btn-primary">Поиск</button>
    		</div>
   		@endif
@endforeach
			</div>
		</div>
	</div>
	@foreach($params['conf']['url-params'] as $param)
		<input type="hidden" name="{{$param}}" value="{{Request::input($param, '')}}">
	@endforeach
</form>
