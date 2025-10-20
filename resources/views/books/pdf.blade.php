<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Books PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
</head>

<body>
    <h3>Books List</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>{{ $book->titre }}</td>
                <td>{{ $book->auteur }}</td>
                <td>{{ $book->category->nom ?? '-' }}</td>
                <td>{{ $book->type ?? '-' }}</td>
                <td>{{ $book->is_valid ? 'Valid' : 'Pending' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>