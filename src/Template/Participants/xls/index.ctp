<?php
        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        
        $date = new DateTime();
        $r = $date->format('Y-m-d');
        $filename = "$r-partecipanti-$event_id.xls";
        header("Content-Type: application/force-download");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");;
        header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding: binary ");
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $row = '1';
        $col = 'A';
        foreach ($columns as $c) {                
                $sheet->setCellValue("$col$row", $c);                 
                $col++;
        }
        foreach ($participants as $p) {
                $col = 'A';        
                $row++;                
                foreach ($columns as $c) {
                        $value = $p->$c;
                        $sheet->setCellValue("$col$row", $value);                 
                        $col++;
                }        
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');   

        // Return response object to prevent controller from trying to render
        // a view.
        return;

