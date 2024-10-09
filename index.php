<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisador Léxico</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        textarea { width: 100%; height: 200px; }
        button { margin-top: 10px; }
        pre { background-color: #f4f4f4; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>

<h1>Analisador Léxico</h1>
<form method="post">
    <label for="sourceCode">Digite o código fonte:</label>
    <textarea name="sourceCode" id="sourceCode"></textarea>
    <button type="submit">Analisar</button>
</form>

<?php include("analisadorLexico/analisador.php"); ?>

</body>
</html>