<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include("automato/automato.php");

    function criaAutomatos() {
        // Autômato para palavras reservadas
        $palavrasReservadas = new Automato(
            'q0',
            ['q2', 'q25', 'q27', 'q51'],
            [
                'q0' => [
                    's' => 'q1', 'f' => 'q3', 'e' => 'q6', 'v' => 'q7',
                    'i' => 'q8', 'l' => 'q11', 'S' => 'q26', 'F' => 'q28', 'E' => 'q31',
                    'V' => 'q38', 'I' => 'q40', 'L' => 'q46'
                ],
                'q1' => ['e' => 'q2'],
                'q2' => ['n' => 'q23'], 
                'q3' => ['a' => 'q4'], 
                'q4' => ['c' => 'q5'],
                'q5' => ['a' => 'q2'], 
                'q6' => ['n' => 'q9', 's' => 'q52'],
                'q7' => ['a' => 'q17'], 
                'q8' => ['m' => 'q13'],
                'q9' => ['q' => 'q10'],
                'q10' => ['u' => 'q19'], 
                'q11' => ['e' => 'q12'], 
                'q12' => ['i' => 'q2'], 
                'q13' => ['p' => 'q14'],
                'q14' => ['r' => 'q15'],
                'q15' => ['i' => 'q16'],
                'q16' => ['m' => 'q18'],
                'q17' => ['r' => 'q2'],
                'q18' => ['a' => 'q2'],
                'q19' => ['a' => 'q20'],
                'q20' => ['n' => 'q21'],
                'q21' => ['t' => 'q22'],
                'q22' => ['o' => 'q2'], 
                'q23' => ['a' => 'q24'],
                'q24' => ['o' => 'q25'],
                'q26' => ['E' => 'q27'],
                'q27' => ['N' => 'q49'],
                'q28' => ['A' => 'q29'],
                'q29' => ['C' => 'q30'],
                'q30' => ['A' => 'q27'],
                'q31' => ['N' => 'q32', 'S' => 'q57'],
                'q32' => ['Q' => 'q33'],
                'q33' => ['U' => 'q34'],
                'q34' => ['A' => 'q35'],
                'q35' => ['N' => 'q36'],
                'q36' => ['T' => 'q37'],
                'q37' => ['O' => 'q27'],
                'q38' => ['A' => 'q39'],
                'q39' => ['R' => 'q27'],
                'q40' => ['M' => 'q41'],
                'q41' => ['P' => 'q42'],
                'q42' => ['R' => 'q43'],
                'q43' => ['I' => 'q44'],
                'q44' => ['M' => 'q45'],
                'q45' => ['A' => 'q27'],
                'q46' => ['E' => 'q47'],
                'q47' => ['I' => 'q48'],
                'q48' => ['A' => 'q27'],
                'q49' => ['A' => 'q50'],
                'q50' => ['O' => 'q51'],
                'q52' => ['c' => 'q53'],
                'q53' => ['r' => 'q54'],
                'q54' => ['e' => 'q55'],
                'q55' => ['v' => 'q56'],
                'q56' => ['a' => 'q2'],
                'q57' => ['C' => 'q58'],
                'q58' => ['R' => 'q59'],
                'q59' => ['E' => 'q60'],
                'q60' => ['V' => 'q61'],
                'q61' => ['A' => 'q27'],
            ]
        );

        // Autômato para identificadores
        $identificador = new Automato(
            'q0',
            ['q1'],
            [
                'q0' => array_merge(array_fill_keys(range('a', 'z'), 'q1'), array_fill_keys(range('A', 'Z'), 'q1')),
                'q1' => array_merge(array_fill_keys(range('a', 'z'), 'q1'), array_fill_keys(range('A', 'Z'), 'q1'), array_fill_keys(range('0', '9'), 'q1')),
            ]
        );

        $constante = new Automato(
            'q0',
            ['q1'],
            [
                'q0' => array_fill_keys(range('0', '9'), 'q1'),
                'q1' => array_fill_keys(range('0', '9'), 'q1'),
            ]
        );

        $operadores = new Automato(
            'q0',
            ['q1', 'q3', 'q120'],
            [
                'q0' => [
                    '==' => 'q120', '=' => 'q120', '+' => 'q1', '-' => 'q1', '*' => 'q1', '/' => 'q1', '%' => 'q1',
                    '(' => 'q1', ')' => 'q1', '[' => 'q1', ']' => 'q1', '{' => 'q1', '}' => 'q1', 
                    '.' => 'q1', ',' => 'q1', ';' => 'q1', '!' => 'q120', '"' => 'q1', "'" => 'q1',
                    ':' => 'q1', '<' => 'q120', '>' => 'q120', '!=' => 'q120', '<=' => 'q120', '>=' => 'q120'
                ],
                'q120' => ['=' => 'q1']
            ]
        );

        return [
            'PALAVRARESERVADA' => $palavrasReservadas,
            'IDENTIFICADOR' => $identificador,
            'CONSTANTE' => $constante,
            'OPERADOR' => $operadores
        ];
    }

    function lexer($sourceCode) {
        $tokens = [];
        $automatos = criaAutomatos();
        $length = strlen($sourceCode);
        $i = 0;
        $erros = [];
        
        $linha = 1;
        $coluna = 1;
    
        // Definição das descrições dos operadores
        $operadorDescricao = [
            '(' => 'ABRE_PARENTESES',
            ')' => 'FECHA_PARENTESES',
            '{' => 'ABRE_CHAVES',
            '}' => 'FECHA_CHAVES',
            '[' => 'ABRE_COLCHETE',
            ']' => 'FECHA_COLCHETE',
            '+' => 'SOMA',
            '-' => 'SUBTRACAO',
            '*' => 'MULTIPLICACAO',
            '/' => 'DIVISAO',
            '%' => 'MODULO',
            '=' => 'ATRIBUICAO',
            '==' => 'IGUAL',
            '!=' => 'DIFERENTE',
            '<' => 'MENOR_QUE',
            '>' => 'MAIOR_QUE',
            '<=' => 'MENOR_OU_IGUAL',
            '>=' => 'MAIOR_OU_IGUAL',
            '!' => 'NEGACAO',
            '.' => 'PONTO',
            ',' => 'VIRGULA',
            ';' => 'PONTO_E_VIRGULA',
            ':' => 'DOIS_PONTOS',
            '"' => 'ASPAS_DUPLAS',
            "'" => 'ASPAS_SIMPLES',
        ];
    
        // Definição das descrições das palavras reservadas
        $palavraReservadaDescricao = [
            'var' => 'Variavel',
            'se' => 'Se',
            'senao' => 'Senao',
            'enquanto' => 'Enquanto',
            'para' => 'Para',
            'faca' => 'Faca',
            'imprima' => 'Imprima',
            'leia' => 'Leia',
            'escreva' => 'Escreva',
            'VAR' => 'Variavel',
            'SE' => 'Se',
            'SENAO' => 'Senao',
            'ENQUANTO' => 'Enquanto',
            'PARA' => 'Para',
            'FACA' => 'Faca',
            'IMPRIMA' => 'Imprima',
            'LEIA' => 'Leia',
            'ESCREVA' => 'Escreva',
        ];
    
        while ($i < $length) {
            // Ignorar espaços e quebras de linha
            if (ctype_space($sourceCode[$i])) {
                if ($sourceCode[$i] === "\n") {
                    $linha++;
                    $coluna = 1;
                } else {
                    $coluna++;
                }
                $i++;
                continue;
            }
    
            $word = '';
    
            // Identificar palavras (identificadores, palavras reservadas)
            if (ctype_alpha($sourceCode[$i])) {
                while ($i < $length && ctype_alnum($sourceCode[$i])) {
                    $word .= $sourceCode[$i];
                    $i++;
                    $coluna++;
                }
            }
            // Identificar constantes numéricas
            elseif (ctype_digit($sourceCode[$i])) {
                while ($i < $length && ctype_digit($sourceCode[$i])) {
                    $word .= $sourceCode[$i];
                    $i++;
                    $coluna++;
                }
            }
            // Identificar operadores e pontuações
            else {
                if ($sourceCode[$i] == '=' || $sourceCode[$i] == '!' || $sourceCode[$i] == '<' || $sourceCode[$i] == '>') {
                    while ($i < $length && ($sourceCode[$i] == '=' || $sourceCode[$i] == '!' || $sourceCode[$i] == '<' || $sourceCode[$i] == '>')) {
                        $word .= $sourceCode[$i];
                        $i++;
                        $coluna++;
                    }
                } else {
                    $word .= $sourceCode[$i];
                    $i++;
                    $coluna++;
                }
            }
    
            $found = false;
    
            // Verificar o token correspondente nos autômatos
            foreach ($automatos as $token => $automato) {
                if ($automato->executa($word)) {
                    $descricao = isset($operadorDescricao[$word]) ? $operadorDescricao[$word] : (isset($palavraReservadaDescricao[$word]) ? $palavraReservadaDescricao[$word] : '');
                    $tokens[] = [$token, $word, $descricao, $linha, $coluna - strlen($word)];
                    $found = true;
                    break;
                }
            }
    
            if (!$found) {
                $erros[] = "Erro léxico: token desconhecido '$word' na linha $linha, coluna $coluna";
            }
        }
    
        return ['tokens' => $tokens, 'erros' => $erros];
    }
    

    // Obter o código fonte do formulário
    $sourceCode = $_POST['sourceCode'] ?? '';

    try {
        $resultado = lexer($sourceCode);
        $tokens = $resultado['tokens'];
        $erros = $resultado['erros'];

        echo "<h2>Tokens Encontrados:</h2><pre>";
        foreach ($tokens as $token) {
            echo ($token[2] ? "{$token[2]}" : $token[0]) . ": {$token[1]}\n";
        }
        echo "</pre>";

        // Exibir erros encontrados
        if (!empty($erros)) {
            echo "<h2>Erros Encontrados:</h2><pre>";
            foreach ($erros as $erro) {
                echo "$erro\n";
            }
            echo "</pre>";
        }
    } catch (Exception $e) {
        echo "<h2>Erro:</h2><pre>{$e->getMessage()}</pre>";
    }
}
