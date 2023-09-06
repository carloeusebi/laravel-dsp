<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <p>
    <div>Da: {{ $data['name'] }}</div>
    @isset($data['email'])
        <div>Email: {{ $data['email'] }}</div>
    @endisset
    <div>Ha contattato il supporto per il seguente motivo:</div>
    <p>{{ $data['issue'] }}</p>
</body>

</html>
