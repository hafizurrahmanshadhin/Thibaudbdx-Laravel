<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Product Details PDF</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            background: #fff;
            color: #333;
        }

        h2 {
            text-align: center;
            background-color: #6C63FF;
            color: white;
            padding: 10px;
            border-radius: 8px;
        }

        .info {
            margin-top: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .info-table td {
            padding: 8px 10px;
            border: 1px solid #eee;
        }

        .info-table td:first-child {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 30%;
        }

        .image-container {
            text-align: center;
            margin: 20px 0;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <h2>Product Details</h2>

    {{-- @if ($product->image)
                <div class="image-container">
                    <img src="{{ asset($product->image) }}" 
                        alt="{{ $product->wine_name }}" 
                        class="product-image" />
                </div>
            @endif --}}
    @if ($product->image)
        @php
            $rawImagePath = $product->getRawOriginal('image');
            $absolutePath = public_path($rawImagePath);

            if ($rawImagePath && file_exists($absolutePath)) {
                // Convert to base64 for PDF
                $imageData = base64_encode(file_get_contents($absolutePath));
                $imageSrc = 'data:image/' . pathinfo($absolutePath, PATHINFO_EXTENSION) . ';base64,' . $imageData;
            }
        @endphp

        @if (isset($imageSrc))
            <div class="image-container">
                <img src="{{ $imageSrc }}" alt="{{ $product->wine_name }}" style="max-width: 300px;" />
            </div>
        @else
            <p>Image not found: {{ $rawImagePath }}</p>
        @endif
    @endif

    <div class="info">
        <table class="info-table">
            <tr>
                <td>wine_name</td>
                <td>{{ $product->wine_name ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Cuvee</td>
                <td>{{ $product->cuvee ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Type</td>
                <td>{{ $product->type ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Color</td>
                <td>{{ $product->color ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Soil Type</td>
                <td>{{ $product->soil_type ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Harvest & Ageing</td>
                <td>{{ $product->harvest_ageing ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Food</td>
                <td>{{ $product->food ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Tasting Notes</td>
                <td>{{ $product->tasting_notes ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Awards</td>
                <td>{{ $product->awards ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Company Name</td>
                <td>{{ $product->company_name ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Address/City</td>
                <td>{{ $product->address ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ $product->phone ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $product->email ?? ' ' }}</td>
            </tr>
            <tr>
                <td>Website Link</td>
                <td>{{ $product->website ?? ' ' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
