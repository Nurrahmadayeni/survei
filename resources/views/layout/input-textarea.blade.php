<div class="form-group {{$errors->has($passing_variable) ? 'has-error' : null}}">
    <label for="{{$passing_variable}}" class="control-label">{{$passing_description}}</label>
        <textarea name="{{$passing_variable}}" class="form-control"
                  placeholder="{{$passing_description}}" {{$disabled}} required>{{$survey[$passing_variable]}}</textarea>
    @if($errors->has($passing_variable))
        <label class="error" style="display: inline-block;">
            {{$errors->first($passing_variable)}}
        </label>
    @endif
</div>