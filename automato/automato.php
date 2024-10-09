<?php

class Automato {
    private $estados;
    private $estadoInicial;
    private $estadosFinais;
    private $transicoes;
    
    public function __construct($estadoInicial, $estadosFinais, $transicoes) {
        $this->estadoInicial = $estadoInicial;
        $this->estadosFinais = $estadosFinais;
        $this->transicoes = $transicoes;
        $this->estados = array_keys($transicoes);
    }

    public function executa($input) {
        $estadoAtual = $this->estadoInicial;
        $inputLength = strlen($input);
    
        for ($i = 0; $i < $inputLength; $i++) {
            $char = $input[$i];
    
            if (isset($this->transicoes[$estadoAtual][$char])) {
                $estadoAtual = $this->transicoes[$estadoAtual][$char];
            } else {
                return false; // Transição inválida encontrada
            }
        }
    
        // Verifica se o estado final foi atingido após processar toda a entrada
        return in_array($estadoAtual, $this->estadosFinais);
    }    
}