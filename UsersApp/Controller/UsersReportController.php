<?php
require_once '../../Config/Database.php';
require_once '../../Framework/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UsersReportController extends Database {

    public function obtenerUsuarios($offset, $limit, $search = '') {
        if ($limit == -1) {
            $query = '
                SELECT usuarios.id, usuarios.username, usuarios.email, roles.nombre AS rol
                FROM usuarios
                INNER JOIN roles ON usuarios.role_id = roles.id
                WHERE usuarios.username LIKE :search OR usuarios.email LIKE :search
            ';
            $stmt = $this->connect()->prepare($query);
        } else {
            $query = '
                SELECT usuarios.id, usuarios.username, usuarios.email, roles.nombre AS rol
                FROM usuarios
                INNER JOIN roles ON usuarios.role_id = roles.id
                WHERE usuarios.username LIKE :search OR usuarios.email LIKE :search
                LIMIT :offset, :limit
            ';
            $stmt = $this->connect()->prepare($query);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarUsuarios($search = '') {
        $query = 'SELECT COUNT(*) as total FROM usuarios WHERE username LIKE :search OR email LIKE :search';
        $stmt = $this->connect()->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function obtenerUsuariosPorIds($ids) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $query = "
            SELECT usuarios.id, usuarios.username, usuarios.email, roles.nombre AS rol
            FROM usuarios
            INNER JOIN roles ON usuarios.role_id = roles.id
            WHERE usuarios.id IN ($placeholders)
        ";
        $stmt = $this->connect()->prepare($query);
        foreach ($ids as $k => $id) {
            $stmt->bindValue(($k + 1), $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exportarUsuarios($usuarios, $filename) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Cabecera
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Rol');

        // Estilos de la cabecera
        $headerStyle = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => '000000'],
            ],
            'font' => [
                'color' => ['argb' => 'FFFFFF'],
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFFFFF'],
                ],
            ],
        ];

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Estilos de las filas
        $rowStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Contenido
        $row = 2;
        foreach ($usuarios as $usuario) {
            $sheet->setCellValue('A' . $row, $usuario['id']);
            $sheet->setCellValue('B' . $row, $usuario['username']);
            $sheet->setCellValue('C' . $row, $usuario['email']);
            $sheet->setCellValue('D' . $row, $usuario['rol']);

            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($rowStyle);
            $sheet->getRowDimension($row)->setRowHeight(20);

            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
?>
