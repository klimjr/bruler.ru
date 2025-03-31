<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Технические работы</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f3f3;
            overflow: hidden;
        }

        .maintenance-image {
            width: 100vw;
            height: 100vh;
            object-fit: cover;
        }

        #desktop {
            display: flex;
        }

        #mobile {
            display: none;
        }

        @media only screen and (max-width: 800px) {
            #desktop {
                display: none;
            }

            #mobile {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <img id="desktop" src="/storage/maintenance_desktop.png" alt="Технические работы" class="maintenance-image">
    <img id="mobile" src="/storage/maintenance_mobile.png" alt="Технические работы" class="maintenance-image">
</body>

</html>
