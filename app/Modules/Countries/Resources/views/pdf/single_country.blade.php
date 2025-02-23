<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Country Details</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 50%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h2>Country Details</h2>
<table>
    <tr>
        <th>Name</th>
        <td>{{ $data->name }}</td>
    </tr>
    <tr>
        <th>Code</th>
        <td>{{ $data->code }}</td>
    </tr>
</table>
</body>
</html>
