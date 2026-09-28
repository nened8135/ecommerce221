<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catégories</title>
</head>
<body>

    <h1>Liste des catégories</h1>

    <a href="{{ route('categories.create') }}">
        Ajouter une catégorie
    </a>

    <hr>

    @if($categories->count() > 0)

        <ul>
            @foreach($categories as $category)
                <li>
                    <strong>{{ $category->name }}</strong>
                    — {{ $category->description }}

                    <a href="{{ route('categories.edit', $category->id) }}">
                        Modifier
                    </a>
                </li>
            @endforeach
        </ul>

    @else

        <p>Aucune catégorie disponible.</p>

    @endif

</body>
</html>