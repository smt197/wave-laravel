<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badge de fidélité</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            background-color: #f2f2f2;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .badge-card {
            width: 320px;
            height: 500px; /* Augmenter légèrement la hauteur */
            background: linear-gradient(135deg, #fefefe 30%, #e6f7ff 100%);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); /* Ombre renforcée */
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Espacement vertical pour centrer les éléments */
            border: 2px solid #007BFF; /* Bordure plus visible */
            overflow: hidden; /* Empêcher les débordements */
        }

        .title-bar {
            background-color: #007BFF;
            color: #fff;
            padding: 12px 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
            margin: -25px -25px 20px -25px;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 12px;
            color: #333;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .client-photo img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #007BFF;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .client-info {
            font-size: 14px;
            text-align: left;
            background-color: rgba(253, 249, 249, 0.9);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }

        .client-info p {
            margin: 8px 0;
            color: #c4c0c0;
        }

        .client-info strong {
            font-weight: 600;
            color: #202020;
        }

        .qr-code {
            width: 110px;
            height: 110px;
            background-color: rgba(253, 249, 249, 0.9);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            margin: 0 auto; /* Centrer le QR code */
            margin-top: 15px; /* Ajouter un espace au-dessus */
        }

        .qr-code img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .footer {
            font-size: 13px;
            color: #2c3e50;
            margin-top: 10px;
            font-weight: 400;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="badge-card">
        <div class="title-bar">Carte Wave</div>
        <h1>WAVE-Agence</h1>

        <!-- Photo du client -->
        {{-- <div class="client-photo">
            @if ($photoData)
                <img src="{{ $photoData }}" alt="Photo du client">
            @else
                <img src="{{ asset('path/to/default/image.png') }}" alt="Photo par défaut">
            @endif
        </div> --}}

        <!-- Informations du client -->
        {{-- <div class="client-info">
            <p><strong>Nom :</strong> {{ $client->user->nom }}</p>
            <p><strong>Prénom :</strong> {{ $client->user->prenom }}</p>
            <p><strong>Adresse :</strong> {{ $client->adresse }}</p>
            <p><strong>Email :</strong> {{ $client->email }}</p>
        </div> --}}

        <!-- QR Code plus visible au centre -->
        <div class="qr-code">
            <img src="{{ $qrCodeBase64 }}" alt="QR Code">
        </div>

        <!-- Footer visible et espacé correctement -->
        <div class="footer">Merci de votre fidélité !</div>
    </div>
</body>

</html>



{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .qr-code { text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <h1>Voici votre QR Code</h1>
    <div class="qr-code">
        <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width: 200px; height: 200px;" />
    </div>
</body>
</html> --}}
