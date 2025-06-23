<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin:0; padding:0; }
        img { max-width: 100%; margin-bottom: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($images as $image)
    <img src="{{ $image }}" alt="Image" />
    <div class="page-break"></div>
@endforeach
</body>
</html>
