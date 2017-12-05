<upload-edit-file>
	@component('Form::components.fields-list', [ 'fields' => Forms::prepAllFields ([], GetConfig::backend('Upload::edit')), 'data' => [], 'params' => [] ] ) @endcomponent
</upload-edit-file>