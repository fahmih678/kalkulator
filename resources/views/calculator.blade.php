<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    @livewireStyles
    <title>calculator</title>
</head>

<body>
    <div class="calc">
        <div class="calc-title">
            <span class="calc-title-span">
                Kalkulator
            </span>
            <div class="calc-title-hr"></div>
        </div>
        @livewire('calculator')
    </div>
    <div class="history-table">
        <b>History </b>
        @livewire('history')
    </div>
    @livewireScripts
</body>

</html>
