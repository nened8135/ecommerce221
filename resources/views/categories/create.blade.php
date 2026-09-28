<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une catégorie</title>
</head>
<body>

    <h1>Ajouter une catégorie</h1>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required>
        </div>

        <br>

        <div>
            <label for="slug">Slug :</label>
            <input type="text" id="slug" name="slug" required>
        </div>

        <br>

        <div>
            <label for="description">Description :</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <br>

        <button type="submit">Enregistrer</button>
    </form>

    <br>

    <a href="{{ route('categories.index') }}">Retour aux catégories</a>

</body>
</html>