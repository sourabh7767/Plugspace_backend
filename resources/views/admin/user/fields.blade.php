<!-- Cat Name Field -->
<div class="form-customer col-sm-6">
    {!! Form::label('cat_name', 'Sub Cat Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

Submit Field
<div class="form-customer col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customer.index') !!}" class="btn btn-default">Cancel</a>
</div>