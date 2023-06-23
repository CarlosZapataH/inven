<?php

class CommonRequest
{
    protected $data;
    protected $rules;
    protected $customMessages;
    protected $errors = [];
    protected $validData = [];

    public function __construct()
    {
    }
    
    public function validate($data, $rules, $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
        foreach ($this->rules as $field => $rules) {
            $nestedData = $this->getValueFromNestedField($field, $this->data);

            foreach ($rules as $rule) {
                $ruleName = $rule[0];
                $params = isset($rule[1]) ? $rule[1] : [];
                $fieldStructure = explode('.', $field);
                $this->validateRule($ruleName, $nestedData, $params, $field, $fieldStructure);
            }
        }

        return empty($this->errors);
    }

    protected function getValueFromNestedField($field, $data)
    {
        $keys = explode('.', $field);
        $fieldStructure = $keys;
        $value = $data;

        foreach ($keys as $key) {
            if (is_object($value) && property_exists($value, $key)) {
                $value = $value->$key;
            } 
            elseif (is_array($value) && isset($value[$key])) {
                $value = $value[$key];
            }
            else {
                return null;
            }
        }

        return [$value, $fieldStructure];
    }

    protected function validateRule($rule, $valueData, $params, $field, $fieldStructure)
    {
        $value = $valueData[0];
        $fieldStructure = $valueData[1];

        switch ($rule) {
            case 'required':
                if (!empty($value)) {
                    $this->addToValidData($field, $fieldStructure, $value);
                    return true;
                } else {
                    $errorMessage = $this->customMessages[$field]['required'] ?? "$field es requerido";
                    $this->addError($field, $errorMessage);
                    return false;
                }

            case 'nullable':
                $this->addToValidData($field, $fieldStructure, (empty($value)?null:$value));
                return true;

            case 'email':
                if (filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
                    $this->addToValidData($field, $fieldStructure, $value);
                    return true;
                } else {
                    $errorMessage = $this->customMessages[$field]['email'] ?? "El campo $field debe ser una dirección de correo electrónico válida";
                    $this->addError($field, $errorMessage);
                    return false;
                }

            case 'min_length':
                $minLength = $params[0] ?? 0;
                if (strlen($value) >= $minLength) {
                    $this->addToValidData($field, $fieldStructure, $value);
                    return true;
                } else {
                    $errorMessage = $this->customMessages[$field]['min_length'] ?? "El campo $field debe tener una longitud mínima de $minLength caracteres";
                    $this->addError($field, $errorMessage);
                    return false;
                }
            case 'array':
                if (is_array($value)) {
                    $this->addToValidData($field, $fieldStructure, $value);
                    return true;
                } else {
                    $errorMessage = $this->customMessages[$field]['array'] ?? "El campo $field debe ser un arreglo";
                    $this->addError($field, $errorMessage);
                    return false;
                }
            case 'min_items':
                $minItems = $params[0] ?? 1;
                if (is_array($value) && count($value) >= $minItems) {
                    $this->addToValidData($field, $fieldStructure, $value);
                    return true;
                } else {
                    $errorMessage = $this->customMessages[$field]['min_items'] ?? "El campo $field debe tener al menos $minItems elemento(s)";
                    $this->addError($field, $errorMessage);
                    return false;
                }

            // Agrega más casos según tus necesidades de validación

            default:
                // Regla de validación no reconocida
                return false;
        }
    }

    protected function addToValidData($field, $fieldStructure, $value)
    {
        $currentData = &$this->validData;

        if($fieldStructure){
            foreach ($fieldStructure as $fieldPart) {
                if (!isset($currentData[$fieldPart])) {
                    $currentData[$fieldPart] = [];
                }
    
                $currentData = &$currentData[$fieldPart];
            }
        }
        else{
            $currentData = &$currentData[$field];
        }

        $currentData = $value;
    }

    protected function addError($field, $errorMessage)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $errorMessage;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getValidData()
    {
        return $this->validData;
    }
}