@extends('backend.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-content wrapper">
        <div class="container-fluid">

        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
    <script>
        $(document).ready(function() {
            Echo.private('chat.' + 1).listen('MessageSent', (e) => {
                console.log(e);
            })
            Echo.private('chat.' + 2).listen('MessageSent', (e) => {
                console.log(e);
            })
        });
    </script>
@endpush
