<div class="form-group {{$errors->has($passing_variable) ? 'has-error' : null}}">
    <label for="{{$passing_variable}}" class="control-label">{{$passing_description}}</label>
    <input name='{{$passing_variable}}' class="form-control enable" type="text"
           placeholder="{{$passing_description}}" value="{{$survey[$passing_variable]}}" required {{$disabled}}>
    @if($errors->has($passing_variable))
        <label class="error" style="display: inline-block;">
            {{$errors->first($passing_variable)}}
        </label>
    @endif
</div>