<?php

namespace AsaEs\Utility;

use AsaEs\AsaEsConst;
use AsaEs\Config;
use AsaEs\Sdk\Baseservice\OssService;
use EasySwoole\Core\Component\Di;
use EasySwoole\Core\Http\Message\UploadFile;
use EasySwoole\Core\Swoole\ServerManager;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use EasySwoole\Core\Utility\File;

class ExcelUtility
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
    ];
     * @param PHPExcel_Worksheet $sheetObj
     * @param array $config
     * @return array
     * @throws \PHPExcel_Exception
     */
    public static function getSheetData(\PHPExcel_Worksheet $sheetObj,array $config): array
    {
        $sheetData = [];
        $highestRowCount = $sheetObj->getHighestRow();   //取得总行数

        for($row = $config['start_row'] ?? 1 ;$row <= $highestRowCount;$row++){
            $tmp = [];
            $highestColumnCount = $sheetObj->getHighestColumn(); // 获得总列数
            for ($column = 'A' ;$column<= $highestColumnCount;$column++){
                $key = $config['key'][$column] ?? null;
                if(isset($key)){
                    $tmp[$key] = $sheetObj->getCell($column.$row)->getValue();
                }
            }
            $sheetData[] = $tmp;
        }

        return $sheetData ?? [];
    }

    /**
     * [putXls description]
     * @param  string  $csvFileName [description] 文件名
     * @param  array   $data     [description] 数组，每组数据都是使用，分割的字符串
     * @param  string  $haderText   [description] 标题（默认第一行）
     * @param  integer $line        [description] 从第几行开始写
     * @param  integer $offset      [description] 共计写几行
     * @param  bool $isProtection      [description] 是否保护
     * @return [type]               [description]
     */
    public static function putXls(string $csvFileName, array $resultArray ,array $haderText = [], $line = 1,bool $isProtection = false){

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);

        if(!empty($haderText)){
            // 合并标题
            array_unshift($resultArray,$haderText);
        }

        $indextoaz = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        , 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ'
        );

        $tmpDir = Config::getInstance()->getConf('TEMP_DIR') . '/Exports';
        $row = $line;
        foreach ($resultArray as $data) {
            $col = 0;
            foreach ($data as $field) {
                $objPHPExcel->getActiveSheet()->setCellValueExplicit($indextoaz[$col] . $row, $field, \PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyle($indextoaz[$col] . $row)->getNumberFormat()->setFormatCode("@");
                //            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $field);
                $col++;
            }
            $row++;
//            if($row % 1000 == 0){
//                $objPHPExcel->createSheet();
//                $objPHPExcel->setactivesheetindex($row / 1000);
//            }
        }

        if($isProtection){
            $objPHPExcel->getSheet(0)->getProtection()->setSheet(true);
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $filePath = "{$tmpDir}/{$csvFileName}.xls";

        File::createDir($tmpDir);
        $objWriter->save($filePath);

        // 保存至oss
        $res = OssService::saveTmpFile($filePath,'tmp',$csvFileName.'_'.Time::getNowDataTime());
        return $res ?? [];
    }
}












