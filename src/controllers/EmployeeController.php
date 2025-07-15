<?php
require_once BASE_PATH . '/src/models/Employee.php';

class EmployeeController
{
    public static function validateEmployee($cb_codigo, $region, $pdo)
    {
        $employee = Employee::findByCodigo($cb_codigo, $region, $pdo);

        if ($employee) {
            return [
                'success' => true,
                'data' => $employee
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Empleado no encontrado o inactivo'
            ];
        }
    }

    public static function submitEmployeeData($input, $pdo)
    {
        $employee = Employee::saveDemographics($input, $pdo);

        if ($employee) {
            return [
                'success' => true,
                'data' => $employee
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Datos de empleados no validados o inexistentes.',
            ];
        }
    }
}
