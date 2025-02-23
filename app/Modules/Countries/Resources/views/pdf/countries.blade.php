<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Countries</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h2>List of Countries</h2>
<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Code</th>
    </tr>
    </thead>
    <tbody>
    @foreach($countries as $country)
        <tr>
            <td>{{ $country->name }}</td>
            <td>{{ $country->code }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
