<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Equipos</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="antialiased">
    {{-- Aquí se montará tu aplicación de Vue para Equipos --}}
    <div id="app-equipos"></div>
</body>
</html>