<!DOCTYPE html>
<html>
<head>
    <title>Routes List</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f8;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #333;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .method {
            font-weight: bold;
            color: #2196f3;
        }
    </style>
</head>
<body>

<h2>📍 Laravel Routes</h2>

<table>
    <thead>
        <tr>
            <th>Method</th>
            <th>URI</th>
            <th>Name</th>
            <th>Action</th>
            <th>Middleware</th>
        </tr>
    </thead>
    <tbody>
        @foreach($routes as $route)
            <tr>
                <td class="method">{{ $route['method'] }}</td>
                <td>{{ $route['uri'] }}</td>
                <td>{{ $route['name'] }}</td>
                <td>{{ $route['action'] }}</td>
                <td>{{ $route['middleware'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>