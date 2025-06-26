<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin:20; padding:20; }
        img { max-width: 100%; margin-bottom: 10px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
@foreach($images as $image)
    <img src="{{ $image }}" alt="Image" style="width:500px; height:600px;   border-radius: 10px;" />
    <div class="page-break"></div>
@endforeach
</body>
</html>
