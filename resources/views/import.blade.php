<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import subskrypcji</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; background: #f9fafb; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 400px; }
        button { background: #3b82f6; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-top: 15px;}
        button:hover { background: #2563eb; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Wgraj historię z banku</h2>
        <p>Obsługiwany format: CSV</p>

        <form action="/import" method="POST" enctype="multipart/form-data">
            @csrf 
            
            <div>
                <input type="file" name="csv_file" accept=".csv" required>
            </div>
            
            <button type="submit">Analizuj plik</button>
        </form>
    </div>

</body>
</html>