<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidateId implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */

    public function __construct(private string $id_type){}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->id_type === 'NIF'){
            if (!preg_match('/^[0-9]{8}[A-Z]$/', $value)){   //preg_match verifica si el valor cumple con la expresión regular definida. 
                $fail('El DNI debe de contener 8 números y una letra mayúscula'); //si no cumple, se lanza un mensaje de error.
                return;
                }

            if (!$this->validateId($value)) {
                $fail('El DNI no es válido.');
                return;
                }
            }
        elseif ($this->id_type === 'NIE'){
            if (!preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $value)){
                $fail('El NIE debe de comenzar por X, Y o Z, seguido de 7 números y una letra mayúscula');
                return;
                }
            if (!$this->validateNie($value)) {
                $fail('El NIE no es válido.');
                return;
                }
            }
    }

    private function validateId(string $value): bool
    {
        $array_letters = 'TRWAGMYFPDXBNJZSQVHLCKE'; //Array de letras que se utilizará para validar el ID.
        $numbers = substr($value, 0, 8); //Extrae los primeros 8 caracteres del valor.
        $letter = substr($value, -1); //Extrae el último carácter del valor.
        $num = (int)implode('', str_split($numbers)); //Suma los dígitos de los primeros 8 caracteres y calcula el módulo 9.
        
        if ($array_letters[$num % 23]  !== $letter) { //Compara la letra obtenida con la letra del ID.
            return false; //Si no coinciden, devuelve false.
        }
        return true;
    }

    private function validateNie(string $value): bool
    {
        $array_letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $firstChar = $value[0];
        $numbers = substr($value, 1, 7);
        $letter = substr($value, -1);
        switch ($firstChar) {
            case 'X':
                $numbers = '0' . $numbers; //añade un 0 al principio de los números.
                break;
            case 'Y':
                $numbers = '1' . $numbers; //añade un 1 al principio de los números.
                break;
            case 'Z':
                $numbers = '2' . $numbers; //añade un 2 al principio de los números.
                break;
            default:
                return false; // Si el primer carácter no es X, Y o Z, devuelve false.
        }

        $num = (int)implode('', str_split($numbers)); //Convierte la cadena de números en un entero y realiza el sumatorio de los dígitos.

        if ($array_letters[$num % 23] !== $letter) {
            return false;
        }
        return true;
    }
}
