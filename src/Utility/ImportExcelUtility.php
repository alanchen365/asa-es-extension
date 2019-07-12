<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Exception\AppException;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Message\UploadFile;
use EasySwoole\Core\Swoole\ServerManager;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;

class ImportExcelUtility
{

    protected $originFile;  // 源文件

    protected $phpExcelObj;

    protected $excelType;

    public function __construct($file)
    {
        if(!$file instanceof UploadFile){
            throw new MsgException(1000,'请传递文件');
        }

        $this->originFile = $file;
        $this->excelType = PHPExcel_IOFactory::identify($this->originFile->getTempName());
        $readerObj = PHPExcel_IOFactory::createReader($this->excelType);
        $this->phpExcelObj = $readerObj->load($this->originFile->getTempName());
    }

    /**
     * 获取PHPExcel中sheet
     */
    public function getAllSheet():array
    {
        $sheetList = [];

        $highestSheet = $this->phpExcelObj->getSheetCount();
        for ($sheetNum = 0; $sheetNum < $highestSheet; ++$sheetNum) {
            $sheetList[] = $this->phpExcelObj->getSheet($sheetNum); //获取第一个工作表
        }

        return $sheetList;
    }

    /**
     * 根据某个sheet的数据
    $excelConfig = [
    'key' => [
    'A' => 'code',      // 表格中第一列 对应数组中的索引
    'B' => 'name',
    //  'D' => 'tmp_worktype_name',
    //  'E' => 'actual_num_weight',
    //  'F' => 'actual_salary',
    //  'G' => 'remark',
    ],
    'start_row' => 1    // 导出起始行 默认第一行
    'return_row_number' => false    // 是否返回行号
    ];
     * @param PHPExcel_Worksheet $sheetObj
     * @param array $config
     * @return array
     * @throws \PHPExcel_Exception
     */
    public function getSheetData(\PHPExcel_Worksheet $sheetObj,array $config): array
    {
        $sheetData = [];
        $totalRowCount = $sheetObj->getHighestRow();   //取得总行数

        if($totalRowCount == 0){
            throw new AppException(1000,'导入文件的内容不能为空');
        }

        for($row = $config['start_row'] ?? 1 ;$row <= $totalRowCount;$row++){
            $tmp = [];
            $highestColumnCount = $sheetObj->getHighestColumn(); // 获得总列数
            for ($column = 'A' ;$column != $highestColumnCount;$column++){
                $key = $config['key'][$column] ?? null;
                if(isset($key)){
                    $tmp[$key] = $sheetObj->getCell($column.$row)->getValue();
                }
            }

            if($config['start_row'] ?? false){
                $tmp['row_number'] = $row;
            }

            $sheetData[] = $tmp;
        }

        return $sheetData ?? [];
    }

}
