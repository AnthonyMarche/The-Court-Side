<?php

namespace App\Services;

use App\Repository\UserRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ExportUsers
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function createCsvFile(): void
    {
        $users = $this->userRepository->findUsersToExport();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('B1', 'Register since');

        $i = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $i, $user['email']);
            $sheet->setCellValue('B' . $i, $user['createdAt']->format('d-m-Y'));
            $i++;
        }

        $writer = new Csv($spreadsheet);
        $writer->setDelimiter(';');
        $writer->save("registered-users.csv");
    }
}
