<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
    <style>
        body {
            font-family: 'sans';
            background-color: transparent;
        }

        figure {
            display: block;
            margin-left: auto;
            max-width: 100%;
        }

        img {
            display: block;
            max-width: 300px;
            width: 100%;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .link {
            display: block;
            background-color: #6ecc84;
            padding-top: .5rem;
            padding-bottom: .5rem;
            text-align: center;
        }

        .link p {
            text-align: justify;
        }

        a {
            color: white;
        }

        a:hover {
            color: blue;
        }
    </style>
</head>

<body>
    <div class="container">
        <figure class="logo">
            <img src="https://www.dellasantapsicologo.it/images/Logo.png" alt="logo">
        </figure>
        <p>
            Salve, cliccando su questo link potrà iniziare la compilazione dei questionari necessari per portare avanti
            la Sua valutazione psicologica. Per qualsiasi dubbio inerente le singole domande può lasciare un commento
            toccando/cliccando sull'apposito pulsante, riguarderemo le domande assieme in seduta. Buon lavoro.
        </p>

        <a class="link" href="{{ $data['link'] }}">
            <div>LINK</div>
        </a>


</body>
</div>

</html>
