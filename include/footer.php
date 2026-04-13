<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
            .footer {
        background-color: #0a1f43dc ;
        color: white;
        padding: 30px 20px 10px;
    }

    .footer-container {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .footer-section {
        width: 30%;
        margin-bottom: 20px;
    }

    .footer-section h3 {
        margin-bottom: 10px;
    }

    .footer-section ul {
        list-style: none;
        padding: 0;
    }

    .footer-section ul li {
        margin: 5px 0;
    }

    .footer-section a {
        color: white;
        text-decoration: none;
    }

    .footer-section a:hover {
        color: #1abc9c;
    }

    .footer-bottom {
        text-align: center;
        border-top: 1px solid #444;
        padding-top: 10px;
        margin-top: 10px;
    }
    </style>
</head>
<body>
<footer class="footer">
    <div class="footer-container">

        <div class="footer-section">
            <h3> Gestion des commandes</h3>
            <p> gestion des commandes des produit</p>
        </div>

        <div class="footer-section">
            <h3>Liens utiles</h3>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="#">commandes</a></li>
                
                
            </ul>
        </div>

        <div class="footer-section">
            <h3>Contact</h3>
            <p>Email : ramatoubatogouma@gmail.com</p>
            <p>Tél : +228 90 27 93 27</p>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 Gestion des commandes </p>
    </div>
</footer>
</body>
</html>
