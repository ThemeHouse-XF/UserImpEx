<?php

class ThemeHouse_UserImpEx_ControllerHelper_Csv extends XenForo_ControllerHelper_Abstract
{

    /**
     * Returns an array from the provided file name (or XenForo_Upload object)
     * provided it is valid.
     * If it is not valid, an error is thrown.
     *
     * @param string|XenForo_Upload $file
     *
     * @return array
     */
    public function getCsvFromFile($file, $startRow = 0, $rowCount = 100)
    {
        if (!file_exists($file)) {
            throw $this->_controller->responseException(
                $this->_controller->responseError(
                    new XenForo_Phrase('please_enter_valid_file_name_requested_file_not_read')));
        }
        
        $handle = fopen($file, 'r');
        
        $headers = fgetcsv($handle, 0, ',');
        $rows = array();
        
        $i = -1;
        while ($row = fgetcsv($handle, 0, ',')) {
            $i++;
            if ($i < $startRow) {
                continue;
            }
            if ($rowCount && $i >= $rowCount + $startRow) {
                break;
            }
            
            $rows[$i + 2] = array_combine($headers, $row);
        }
        fclose($handle);
        
        foreach ($rows as &$row) {
            foreach ($row as $key => $value) {
                if (!is_array($value)) {
                    $value = utf8_encode($value);
                }
                if (preg_match('#^(.+)\[(.+)\]\[(.+)\]$#U', $key, $matches)) {
                    $row[$matches[1]][$matches[2]][$matches[3]] = $value;
                    unset($row[$key]);
                } elseif (preg_match('#^(.+)\[(.+)\]$#U', $key, $matches)) {
                    $row[$matches[1]][$matches[2]] = $value;
                    unset($row[$key]);
                } elseif (is_string($value) &&
                     preg_match('#^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$#', $value, $matches)) {
                    $row[$key] = strtotime($matches[0]);
                } else {
                    $row[$key] = $value;
                }
            }
        }
        
        return $rows;
    }
}