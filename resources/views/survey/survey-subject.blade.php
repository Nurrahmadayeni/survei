
    {{--{{dd($subjects)}}--}}
    <div id="unit" class="form-group">
        <label for="unit" class="control-label">Pilih Matakuliah</label>
        <select class="form-control mb-15 select2" name='subject' id="subject" data-placeholder="-- Pilih Matakuliah --" required>
            <option value="" disabled="">-- Pilih Matakuliah --</option>
            @foreach($subjects as $subject)
                @if(!empty($subject->kodemk))
                    <option value="{{$subject->kodemk}}">{{$subject->namamk}}</option>
                @endif
            @endforeach
        </select>
    </div>