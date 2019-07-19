<?php

class Equacao
{
    const PATTENER_CON = '/[\W][0-9]+[a-z]|[\W][a-z]|[a-z]|[0-9]+[xyz]/u';    
    const PATTENER_NUM = '/[\W][0-9]+|[0-9]+[^a-z\W]/u';
    const PATTENER_SYM = '/[\W]/';

    private $parts;
    
    public function __construct(string $eq)
    {
        $eq = str_replace(' ', '', $eq);
        $this->parts['eq'] = $eq;
        
        $this->parts['divider'] = explode('=', $this->parts['eq']);

        $this->parts['beforeEqConstants'] = $this->getConstantes(0);
        $this->parts['afterEqConstants'] = $this->getConstantes(1);

        $this->parts['beforeEqNumbers'] = $this->getNumbers(0);
        $this->parts['afterEqNumbers'] = $this->getNumbers(1);

        $this->parts['inverseAfterEqConstants'] = $this->getInverseSym(
            $this->parts['afterEqConstants']
        );
        $this->parts['inverseBerforeEqNumbers'] = $this->getInverseSym(
            $this->is_calculable($this->parts['beforeEqNumbers'])
        );

        $this->parts['mergeBeforeEqConstants'] = $this->getMergerChunks('constants');
        $this->parts['mergeAfterEqNumbers'] = $this->getMergerChunks('numbers');

        $this->parts['mergeConstants'] = $this->reduceChunks($this->parts['mergeBeforeEqConstants']);
        $this->parts['mergeNumbers'] = $this->reduceChunks($this->parts['mergeAfterEqNumbers']);

        $this->parts['NameConstant'] = $this->getNameConstants(); 

        $this->parts['calcConstants'] = $this->calcConstants();
        $this->parts['calcNumbers'] = $this->calcNumbers();

        $this->parts['beforeResolved'] = $this->getBeforeResolved();
        $this->parts['solution'] = $this->getSolution();
    }

    /**
     * Encontra todas as constantes na equação (ex: 4x ...)
     * @param int $idx
     * @return array
     */
    public function getConstantes($idx)
    {
        preg_match_all(self::PATTENER_CON, $this->parts['divider'][$idx], $matches);
        $current = current($matches);
        if ($count = count($current) == 0) {
            return [''];
        } else if ($count == 1) {
            if (strlen($current) == 1) {
                return str_replace('x', '1x', $current);
            }
        }
        return $current;
    }

    /**
     * Encontra todas numeros, removendo as equação da string
     * @param int $idx
     * @return array
     */
    public function getNumbers($idx)
    {
        $numbers = ($idx == 0) 
            ? str_replace($this->parts['beforeEqConstants'], '', $this->parts['divider'][$idx]) 
            : str_replace($this->parts['afterEqConstants'], '', $this->parts['divider'][$idx]);
        return [$numbers];
    }

    /**
     * Inverte os sinais quando mover de um lado para o outro do '='
     * @param array $array
     * @return array
     */
    public function getInverseSym($array)
    {
        if ($count = count($array) == 1) {
            if (!current($array)) {
                return [''];
            }
        }
        return array_map(function ($value) {
            if (preg_match(self::PATTENER_SYM, $value, $symbol)) {
                if (current($symbol) == '+') {
                    return str_replace('+', '-', $value);
                } else if (current($symbol) == '-') {
                    return str_replace('-', '+', $value);
                } return $value;
            }
            return '-'.$value;
        }, $array);
    }

    /**
     * Junta os fragmentos das constantes ou dos numeros
     * @param string $consOrNumb
     * @return array
     */
    public function getMergerChunks($consOrNumb)
    {
        /**beforeEqConstants + inverseAfterEqConstants */
        if ($consOrNumb == 'constants') {
            $_constatants = $this->hasValidArray($this->parts['inverseAfterEqConstants']);
            return ($_constatants) 
                ? array_merge($this->parts['beforeEqConstants'], $this->parts['inverseAfterEqConstants'])
                : $this->parts['beforeEqConstants'];
        }
        $_numbers = $this->hasValidArray($this->parts['inverseBerforeEqNumbers']);
        return ($_numbers) ? array_merge($this->parts['afterEqNumbers'], $this->parts['inverseBerforeEqNumbers'])
                : $this->parts['afterEqNumbers'];
        /**afterEqNumbers+ inverseBerforeEqNumbers*/
    }

    /**
     * Verifica se é um array com valor valido para fazer o merge
     * @param array $array
     * @return array
     */
    public function hasValidArray($array) {
        if ($count = count($array) == 1) {
            if (!current($array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Junta os pedações das constantes ou numeros em uma unica string
     * @param array $array
     * @return string
     */
    public function reduceChunks($chunks)
    {
        return array_reduce($chunks, function($a, $b) {
            if ($a === null) {
                return $b;
            } return $a .= $b;
        });
    }

    /**
     * Encontra o nome da constant (x, y, z)
     * @return string
     */
    public function getNameConstants()
    {   
        preg_match_all('/[a-z]/', $this->parts['mergeConstants'], $matches);
        return array_unique(current($matches))[0];
    }
    
    /**
     * Calcula os valores das constantes
     * @return string
     */
    public function calcConstants()
    {
        $constatants = str_replace($this->parts['NameConstant'], '', $this->parts['mergeConstants']);
        $result = mb_convert_encoding($constatants, "EUC-JP", "auto");
        $result = str_replace('?', '-', $result);
        $result = eval('return ' . $result . ' ;');
        return $result.$this->parts['NameConstant'];
    }

    /**
     * Calcula os valores dos numeros
     * @return string | int
     */
    public function calcNumbers()
    {
        $numbers = $this->parts['mergeNumbers'];
        $result = mb_convert_encoding($numbers, "EUC-JP", "auto");
        $result = str_replace('?', '-', $result);
        $result = eval('return ' . $result . ' ;');
        return $result;
    }

    /**
     * Retorna o valor antes de calcular a solução
     * @return string
     */
    public function getBeforeResolved()
    {
        return implode('=', [$this->parts['calcConstants'], $this->parts['calcNumbers']]);
    }

    /**
     * Retorna a solução da equação
     * @return string
     */
    public function getSolution()
    {
        $constant = str_replace($this->parts['NameConstant'], '', $this->parts['calcConstants']);
        if ($constant) {
            $result = $this->parts['calcNumbers'] / $constant;
            return $this->parts['NameConstant'] . ' = ' . $result;
        }
        return $this->getBeforeResolved();
    }

    /**
     * veridica se é possivel calcular a string se for retorna o resultado se não retorna o valor passado
     * @param array $value
     * @return array
     */
    public function is_calculable($value)
    {
        $eval = 'return ' . current($value) . ' ;';
        $result = eval($eval);
        if (is_numeric($result)) {
            return [$result];
        }
        return $value;
    }
}

?>