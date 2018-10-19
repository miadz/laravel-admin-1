<form {!! $attributes !!}>
    <div class="box-body fields-group">

        @foreach($fields as $field)
            {!! $field->render() !!}
        @endforeach

    </div>

<<<<<<< HEAD
    <!-- /.box-body -->
    <div class="box-footer">
        @if( ! $method == 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @endif
        <div class="form-group">
            <div class="col-md-{{ $labelWidth }}"></div>
            <div class="col-md-{{ $fieldWidth }}">
                <div class="btn-group pull-left">
                    <button type="reset" class="btn btn-warning pull-left">{{ trans('admin.reset') }}</button>
                </div>
                <div class="btn-group pull-right">
                    <button type="submit" class="btn btn-info pull-left">{{ trans('admin.submit') }}</button>
                </div>

            </div>
=======
    @if ($method != 'GET')
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @endif
    
    <!-- /.box-body -->
    @if(count($buttons) > 0)
    <div class="box-footer">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            @if(in_array('reset', $buttons))
            <div class="btn-group pull-left">
                <button type="reset" class="btn btn-warning pull-right">{{ trans('admin.reset') }}</button>
            </div>
            @endif

            @if(in_array('submit', $buttons))
            <div class="btn-group pull-right">
                <button type="submit" class="btn btn-info pull-right">{{ trans('admin.submit') }}</button>
            </div>
            @endif
>>>>>>> upstream/master
        </div>
    </div>
<<<<<<< HEAD
</form>
=======
    @endif
</form>
>>>>>>> upstream/master
