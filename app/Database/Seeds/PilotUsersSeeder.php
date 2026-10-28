<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

final class PilotUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Gestor CenEntEnc_01', 'matricula' => '0001', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_01', 'area_id' => 'area-norte'],
            ['name' => 'Gestor CenEntEnc_02', 'matricula' => '0002', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_02', 'area_id' => 'area-norte'],
            ['name' => 'Gestor CenEntEnc_03', 'matricula' => '0003', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_03', 'area_id' => 'area-norte'],
            ['name' => 'Gestor CenEntEnc_04', 'matricula' => '0004', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_04', 'area_id' => 'area-sul'],
            ['name' => 'Gestor CenEntEnc_05', 'matricula' => '0005', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_05', 'area_id' => 'area-sul'],
            ['name' => 'Gestor CenEntEnc_06', 'matricula' => '0006', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_06', 'area_id' => 'area-sul'],
            ['name' => 'Gestor CenEntEnc_07', 'matricula' => '0007', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_07', 'area_id' => 'area-sul'],
            ['name' => 'Gestor CenEntEnc_08', 'matricula' => '0008', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_08', 'area_id' => 'area-sul'],
            ['name' => 'Gestor CenEntEnc_09', 'matricula' => '0009', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_09', 'area_id' => 'area-norte'],
            ['name' => 'Gestor CenEntEnc_10', 'matricula' => '0010', 'role' => 'gestor_unidade', 'unit_id' => 'CenEntEnc_10', 'area_id' => 'area-norte'],
            ['name' => 'Gestor Área Norte', 'matricula' => '0101', 'role' => 'gestor_area', 'unit_id' => null, 'area_id' => 'area-norte'],
            ['name' => 'Gestor Área Sul', 'matricula' => '0102', 'role' => 'gestor_area', 'unit_id' => null, 'area_id' => 'area-sul'],
            ['name' => 'Gerente Território', 'matricula' => '1001', 'role' => 'gerente', 'unit_id' => null, 'area_id' => null],
        ];

        $unitAreas = [
            'CenEntEnc_01' => 'area-norte',
            'CenEntEnc_02' => 'area-norte',
            'CenEntEnc_03' => 'area-norte',
            'CenEntEnc_04' => 'area-sul',
            'CenEntEnc_05' => 'area-sul',
            'CenEntEnc_06' => 'area-sul',
            'CenEntEnc_07' => 'area-sul',
            'CenEntEnc_08' => 'area-sul',
            'CenEntEnc_09' => 'area-norte',
            'CenEntEnc_10' => 'area-norte',
        ];
        $courierNumber = 1;
        foreach ($unitAreas as $unitId => $areaId) {
            for ($slot = 0; $slot < 5; $slot++) {
                $code = str_pad((string) $courierNumber, 4, '0', STR_PAD_LEFT);
                $login = 'entregador_' . $code;
                $users[] = ['name' => 'Entregador ' . $code, 'matricula' => $login, 'role' => 'entregador', 'unit_id' => $unitId, 'area_id' => $areaId];
                $courierNumber++;
            }
        }

        $userModel = model(UserModel::class);
        $employeeTable = $this->db->table('employees');

        foreach ($users as $employee) {
            $user = $userModel->where('username', $employee['matricula'])->first();

            if ($user === null) {
                $user = new User([
                    'username' => $employee['matricula'],
                    'active'   => true,
                ]);
                $user->email = $employee['matricula'] . '@shipfromstore.test';
                $user->setPassword('123');

                $userId = $userModel->insert($user);
                $user->id = (int) $userId;
            } else {
                $userId = (int) $user->id;
            }

            if (! $user->inGroup($employee['role'])) {
                $user->addGroup($employee['role']);
            }

            if ($employeeTable->where('matricula', $employee['matricula'])->countAllResults() === 0) {
                $employeeTable->insert([
                    'user_id'   => $userId,
                    'name'      => $employee['name'],
                    'matricula' => $employee['matricula'],
                    'role'      => $employee['role'],
                    'unit_id'   => $employee['unit_id'],
                    'area_id'   => $employee['area_id'],
                ]);
            }
        }
    }
}
