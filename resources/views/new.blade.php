@extends('layouts.app')

@section('content')
    <div class="clearfix">
        <div class="content clearfix">
            <h3 class="section-heading">
                Create new League
            </h3>
            <p>Note: Clubs will be chosen randomly and all tours and tour fixtures will be created beforehand.</p>
            <div class="clearfix">
                <span class="btn btn-primary" id="create">Create</span>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        $("#create").click(function() {
            fetch("{{ route('new') }}", {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                })
                .then(response => response.json())
                .then(function(result) {
                    if (result.success == true) {
                        window.location.href = "{{ route('table') }}";
                    }
                });
        });

    </script>
@endsection
