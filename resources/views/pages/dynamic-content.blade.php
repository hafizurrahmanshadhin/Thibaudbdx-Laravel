<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title ?? '' }}</title>
    <link href="{{ asset('backend/custom_downloaded_file/css2.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/custom_downloaded_file/all.min.css') }}" rel="stylesheet">

    {{-- FAVICON --}}
    <link rel="shortcut icon" type="image/x-icon"
        href="{{ isset($systemSetting->favicon) && !empty($systemSetting->favicon) ? asset($systemSetting->favicon) : asset('backend/images/logo-sm.png') }}" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: #2d2d2d;
            line-height: 1.6;
            background-color: #f9f9f9;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .container {
            max-width: 960px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Header Section */
        .page-header {
            background-color: #081112;
            color: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            transition: background-color 0.3s ease;
        }

        .page-header:hover {
            background-color: #2980b9;
        }

        .page-header h1 {
            margin: 0;
            font-size: 2.8rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Content Section */
        .page-content {
            background-color: white;
            padding: 40px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .page-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .page-content p {
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.8;
            color: #444;
        }

        .page-content a {
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            color: #2980b9;
        }

        .page-content pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Responsive Design */
        @media only screen and (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }

            .page-content {
                padding: 30px;
            }

            .container {
                margin: 20px auto;
                padding: 0 15px;
            }
        }

        @media only screen and (max-width: 576px) {
            .page-header {
                padding: 20px;
            }

            .page-header h1 {
                font-size: 1.8rem;
            }

            .page-content {
                padding: 20px;
            }

            .page-content p {
                font-size: 13px;
            }

            .container {
                padding: 0 10px;
            }
        }

        /* Accessibility Enhancements */
        .page-content,
        .page-header {
            outline: none;
            border: none;
        }

        .page-content:focus,
        .page-header:focus {
            box-shadow: 0 0 10px #3498db;
            transition: box-shadow 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="page-header">
            <h1 tabindex="0">{{ $content->title ?? '' }}</h1>
        </header>

        <main class="page-content" tabindex="0">
            {!! $content->content ?? '' !!}
        </main>
    </div>
</body>

</html>
