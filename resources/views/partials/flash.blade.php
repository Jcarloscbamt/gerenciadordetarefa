@if(session('ok'))
    <div class="flash flash-ok">&#10003; {{ session('ok') }}</div>
@endif

@if($errors->any())
    <div class="flash flash-erro">
        &#9888;
        <div>
            @foreach($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    </div>
@endif
