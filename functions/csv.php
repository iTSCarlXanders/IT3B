<?php

function exportCsv(string $filename, array $headers,array $rows):void
{
    if(ob_get_level()) {
        ob_end_clean();
    }

    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    $safeName = $safeName . '_' . date('Y-m-d_H-i-s');

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$safeName.'"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out =fopen('php://output', 'w');   

    fwrite($out, $headers);

    foreach ($rows as $row) {
        fputcsv($out, array_values($row));
    }

    fclose($out);

    exit:
    
}
     
?>