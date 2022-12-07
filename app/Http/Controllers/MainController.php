<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Response;

class MainController extends Controller
{
    public function main() {/*функция возврата на главную*/
        return view('main');
    }

    public function ShowUnitedTable(){/*функция получения данных из 2х таблц*/
        $query = DB::select("
        select torg_date,kod,REPLACE(quotation, ',', '.') as quotation,num_contr,exec_data,
               REPLACE(ISNULL(LOG(quotation/EndDate),0), ',', '.') as Xk
        from(

        select torg_date,F_usd.kod,quotation,num_contr,exec_data, Lag(CAST(quotation AS float), 2)
        OVER(PARTITION BY F_usd.kod ORDER BY torg_date ASC) AS EndDate
        FROM F_usd
        inner join dataisp on dataisp.kod = F_usd.kod

        where CAST(quotation AS float) > 0
        ) as temp");
        return json_encode($query);
    }

    public function KodCheck(Request $request){/*функция проверки существования кода*/
        $request->input('kod');
        $count=DB::select("SELECT Count(*)
    FROM dataisp
    Where kod='".$request."'");
        if ($count==0)
        {
            $answer=0;
            return json_encode($answer);
        }
        else
        {
            $answer=1;
            return json_encode($answer);
        }

    }
    public function ChangeData(Request $request){/*Функция изменения данных*/
        $K=$request->input('kod');
        $TD=$request->input('torg_date');
        $Q=$request->input('quotation');
        $NC=$request->input('num_contr');
        DB::update('update F_usd set quotation = ? , num_contr = ?
             where kod = ? and torg_date=?',
            [$Q , $NC , $K,$TD]);
    }
    public function DeleteData(Request $request){/*Функция удаления данных*/
        $K=$request->input('kod');
        $TD=$request->input('torg_date');
        //Carbon::parse($TD)->format('Y M d');
        //$TD = strtotime('1995-08-15');
        //$K='FUSD_08_95';
        DB::delete("delete from [F_usd] where kod = '".$K."' and torg_date='".$TD."'");
    }
    public function AddData(Request $request){/*Функция добавления данных*/
        $K=$request->input('kod');
        $TD=$request->input('torg_date');
        $Q=$request->input('quotation');
        $NC=$request->input('num_contr');
        //$ED=$request->input('exec_data');//сделать только код
        DB::insert('insert into F_usd (kod,torg_date,quotation,num_contr)
        values(?,?,?,?)',
        [$K,$TD,$Q,$NC]);
    }
    public function AddDataF(Request $request){/*Функция добавления фьючерса*/
        $K=$request->input('kod');
        $ED=$request->input('exec_data');
        DB::insert('insert into dataisp (kod,exec_data)
        values(?,?)',
            [$K,$ED]);
    }
    public function ExportReport(Request $request) /*Функция добавления фьючерса*/
    {
        $kod = $request->input('kod');
        $date_torg_min = $request->input('date_torg_min');
        $date_torg_max = $request->input('date_torg_max');
        $price_min = $request->input('price_min');
        $price_max = $request->input('price_max');
        $numb_sales_min = $request->input('numb_sales_min');
        $numb_sales_max = $request->input('numb_sales_max');
        $filter_array [] = array("Код фьючерса", "Дата торгов от", "Дата торгов до", "Максимальная цена от", "Максимальная цена до", "Кол-во продаж от", "Кол-во продаж до");
        $filter_array [] = array(
            'Код фьючерса' => $kod,
            'Дата торгов от' => $date_torg_min,
            'Дата торгов до' => $date_torg_max,
            'Максимальная цена от' => $price_min,
            'Максимальная цена до' => $price_max,
            'Кол-во продаж от' => $numb_sales_min,
            'Кол-во продаж до' => $numb_sales_max);
        $query = DB::select("
        select MAX(kod) AS MAX_kod, MIN(kod) AS MIN_kod, MAX(quotation) AS MAX_quotation, MIN(quotation) AS MIN_quotation,MAX(num_contr) AS MAX_num_contr, MIN(num_contr) AS MIN_num_contr,MAX(torg_date) AS MAX_torg_date, MIN(torg_date) AS MIN_torg_date  FROM(
        select torg_date,kod,REPLACE(quotation, ',', '.') as quotation,num_contr,exec_data,
               REPLACE(LOG(quotation/EndDate), ',', '.') as Xk
        from(

        select torg_date,F_usd.kod,quotation,num_contr,exec_data, Lag(CAST(quotation AS float), 2)
        OVER(PARTITION BY F_usd.kod ORDER BY torg_date ASC) AS EndDate
        FROM F_usd
        inner join dataisp on dataisp.kod = F_usd.kod

        where CAST(quotation AS float) > 0
        ) as temp
        ) AS SSS
        ");
        if ($kod == ''){
            $kod1 = $query[0]->MIN_kod;
            $kod2 = $query[0]->MAX_kod;
        }
        else{
            $kod1 = $kod;
            $kod2 = $kod;
        }
        if ($date_torg_min == ''){
            $date_torg_min = $query[0]->MIN_torg_date;
        }
        if ($date_torg_max == ''){
            $date_torg_max = $query[0]->MAX_torg_date;
        }
        if ($price_min == ''){
            $price_min = $query[0]->MIN_quotation;
        }
        if ($price_max == ''){
            $price_max = $query[0]->MAX_quotation;
        }
        if ($numb_sales_min == ''){
            $numb_sales_min = $query[0]->MIN_num_contr;
        }
        if ($numb_sales_max == ''){
            $numb_sales_max = $query[0]->MAX_num_contr;
        }
        $data = DB::select("
        select torg_date,kod,REPLACE(quotation, ',', '.') as quotation,num_contr,exec_data,
               REPLACE(LOG(quotation/EndDate), ',', '.') as Xk
        from(

        select torg_date,F_usd.kod,quotation,num_contr,exec_data, Lag(CAST(quotation AS float), 2)
        OVER(PARTITION BY F_usd.kod ORDER BY torg_date ASC) AS EndDate
        FROM F_usd
        inner join dataisp on dataisp.kod = F_usd.kod

        where CAST(quotation AS float) > 0
        ) as temp
        where kod >= '$kod1' and kod <= '$kod2' and torg_date >= '$date_torg_min' and torg_date <= '$date_torg_max' and quotation >= '$price_min' and quotation <= '$price_max' and num_contr >= '$numb_sales_min' and num_contr <= '$numb_sales_max'
        ");
        $data_array [] = array("Код фьючерса", "Дата погашения", "Дата торгов", "Максимальная цена", "Кол-во продаж", "Xk");
        foreach ($data as $data_item) {
            $data_array[] = array(
                'Код фьючерса' => $data_item->kod,
                'Дата погашения' => $data_item->exec_data,
                'Дата торгов' => $data_item->torg_date,
                'Максимальная цена' => $data_item->quotation,
                'Кол-во продаж' => $data_item->num_contr,
                'Xk' => $data_item->Xk
            );
        }
        date_default_timezone_set('Europe/Moscow');
        $date_ = date('m/d/Y h:i:s a', time());
        $date_array [] = array("Дата формирования файла");
        $date_array [] = array(
            'Дата формирования файла' => $date_);
        $this->ExportFunction($data_array,$filter_array,$date_array);
    }

    public function ExportFunction($export_array,$export_filter,$date_array){
        $spreadSheet = new Spreadsheet();
        $spreadSheet->getActiveSheet()->mergeCells('A1:C1');
        $spreadSheet->getActiveSheet()->mergeCells('A2:C2');
        $spreadSheet->getActiveSheet()->mergeCells('A3:C3');
        $spreadSheet->getActiveSheet()->mergeCells('A4:C4');
        $spreadSheet->getActiveSheet()->mergeCells('A5:C5');
        $spreadSheet->getActiveSheet()->mergeCells('A6:C6');
        $spreadSheet->getActiveSheet()->mergeCells('A7:C7');
        $spreadSheet->getActiveSheet()->mergeCells('A8:C8');
        $spreadSheet->getActiveSheet()->mergeCells('D1:F1');
        $spreadSheet->getActiveSheet()->mergeCells('D2:F2');
        $spreadSheet->getActiveSheet()->mergeCells('D3:F3');
        $spreadSheet->getActiveSheet()->mergeCells('D4:F4');
        $spreadSheet->getActiveSheet()->mergeCells('D5:F5');
        $spreadSheet->getActiveSheet()->mergeCells('D6:F6');
        $spreadSheet->getActiveSheet()->mergeCells('D7:F7');
        $spreadSheet->getActiveSheet()->mergeCells('D8:F8');
        $spreadSheet->getActiveSheet()->getStyle('A1:F8')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A1:F8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $borderStyleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
                'horizontal' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
                'vertical' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );
        $spreadSheet->getActiveSheet()->getStyle('A1:F8')->applyFromArray($borderStyleArray);
        $spreadSheet->getActiveSheet()->getStyle('A1:A8')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF9000');
        $spreadSheet->getActiveSheet()->getStyle('A1:A8')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle('A1:A8')->getAlignment()->setWrapText(true);
        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
        $export_filter_headers= array_values($export_filter)[0];
        $i = 0;
        foreach ($export_filter_headers as $key => $value) {
            $export_filter_headers1[$i][0] = $value;
            $i++;
        }
        $export_filter_inputs = array_values($export_filter)[1];
        $i = 0;
        foreach ($export_filter_inputs as $key => $value) {
            $export_filter_inputs1[$i][0] = $value;
            $i++;
        }


        $date_array_headers = array_values($date_array)[0];
        $i = 0;
        foreach ($date_array_headers as $key => $value) {
            $date_array_headers1[$i][0] = $value;
            $i++;
        }
        $date_array_inputs = array_values($date_array)[1];
        $i = 0;
        foreach ($date_array_inputs as $key => $value) {
            $date_array_inputs1[$i][0] = $value;
            $i++;
        }
        $spreadSheet->getActiveSheet()->fromArray($date_array,NULL,'A1');
        $spreadSheet->getActiveSheet()->fromArray($date_array_headers1,NULL,'A1');
        $spreadSheet->getActiveSheet()->fromArray($date_array_inputs1,NULL,'D1');
        $spreadSheet->getActiveSheet()->fromArray($export_filter_headers1,NULL,'A2');
        $spreadSheet->getActiveSheet()->fromArray($export_filter_inputs1,NULL,'D2');
        $spreadSheet->getActiveSheet()->fromArray($export_array,NULL,'A9');
        $highestRow = $spreadSheet->getActiveSheet()->getHighestDataRow();
        $spreadSheet->getActiveSheet()->getStyle('A9:F'.$highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A1:F'.$highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A1:F'.$highestRow)->applyFromArray($borderStyleArray);
        $spreadSheet->getActiveSheet()->getStyle('A9:F9')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF9000');
        $spreadSheet->getActiveSheet()->getStyle('A9:F9')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle('A9:F9')->getAlignment()->setWrapText(true);
        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A1:F'.$highestRow)
            ->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));
        $Excel_writer = new Xls($spreadSheet);
        $Excel_writer = new Xls($spreadSheet);
        $Excel_writer->save('ExportedData.xls');
        //$spreadSheet->createSheet();
        //$spreadSheet->setActiveSheetIndex(0)->setTitle('Фильтры')->getDefaultColumnDimension()->setWidth(25);
        //$spreadSheet->setActiveSheetIndex(0)->fromArray($export_filter);
        //$spreadSheet->setActiveSheetIndex(1)->setTitle('Данные')->getDefaultColumnDimension()->setWidth(20);
        //$spreadSheet->setActiveSheetIndex(1)->fromArray($export_array);
        //$Excel_writer = new Xls($spreadSheet);
        //$Excel_writer->save('ExportedData.xls');
    }

    public function GetDownload()
    {
        $file= public_path(). "/ExportedData.xls";

        $headers = array(
            'Content-Type: application/xls',
        );
        return Response::download($file, 'ExportedData.xls', $headers);
    }

    ////Экспорт статистики
    public function ExportReportStats(Request $request)
    {
        $FUSD = $request->input('FUSD');
        $Mx = $request->input('Mx');
        $D = $request->input('D');
        $V = $request->input('V');
        $TrendMx = $request->input('TrendMx');
        $TrendD = $request->input('TrendD');
        $Date1 = $request->input('Date1');
        $Date2 = $request->input('Date2');
        $filter_array [] = array("Дата торгов от","Дата торгов до");
        $filter_array [] = array(
            'Дата торгов от' => $Date1,
            'Дата торгов до' => $Date2
        );
        $explode_FUSD = explode(',', $FUSD);
        $explode_Mx = explode(',', $Mx);
        $explode_D = explode(',', $D);
        $explode_V = explode(',', $V);
        $explode_TrendMx = explode(',', $TrendMx);
        $explode_TrendD = explode(',', $TrendD);
        $data_array [] = array("FUSD", "Mx", "D", "V", "TrendMx", "TrendD");
        for ($i = 0; $i < count($explode_FUSD); $i++) {
            $data_array[] = array(
                'FUSD' => $explode_FUSD[$i],
                'Mx' => $explode_Mx[$i],
                'D' => $explode_D[$i],
                'V' => $explode_V[$i],
                'TrendMx' => $explode_TrendMx[$i],
                'TrendD' => $explode_TrendD[$i]
            );
        }
        date_default_timezone_set('Europe/Moscow');
        $date_ = date('m/d/Y h:i:s a', time());
        $date_array [] = array("Дата формирования файла");
        $date_array [] = array(
            'Дата формирования файла' => $date_);
        $this->ExportFunctionStats($data_array,$filter_array,$date_array);
    }

    public function ExportFunctionStats($export_array,$export_filter,$date_array){
        $spreadSheet = new Spreadsheet();
        $spreadSheet->getActiveSheet()->mergeCells('A1:C1');
        $spreadSheet->getActiveSheet()->mergeCells('A2:C2');
        $spreadSheet->getActiveSheet()->mergeCells('A3:C3');
        $spreadSheet->getActiveSheet()->mergeCells('D1:F1');
        $spreadSheet->getActiveSheet()->mergeCells('D2:F2');
        $spreadSheet->getActiveSheet()->mergeCells('D3:F3');
        $spreadSheet->getActiveSheet()->getStyle('A1:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A1:F3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $borderStyleArray = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
                'horizontal' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
                'vertical' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );
        $spreadSheet->getActiveSheet()->getStyle('A1:F3')->applyFromArray($borderStyleArray);
        $spreadSheet->getActiveSheet()->getStyle('A1:A3')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF9000');
        $spreadSheet->getActiveSheet()->getStyle('A1:A3')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle('A1:A3')->getAlignment()->setWrapText(true);
        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);
        $export_filter_headers= array_values($export_filter)[0];
        $i = 0;
        foreach ($export_filter_headers as $key => $value) {
            $export_filter_headers1[$i][0] = $value;
            $i++;
        }
        $export_filter_inputs = array_values($export_filter)[1];
        $i = 0;
        foreach ($export_filter_inputs as $key => $value) {
            $export_filter_inputs1[$i][0] = $value;
            $i++;
        }


        $date_array_headers = array_values($date_array)[0];
        $i = 0;
        foreach ($date_array_headers as $key => $value) {
            $date_array_headers1[$i][0] = $value;
            $i++;
        }
        $date_array_inputs = array_values($date_array)[1];
        $i = 0;
        foreach ($date_array_inputs as $key => $value) {
            $date_array_inputs1[$i][0] = $value;
            $i++;
        }
        $spreadSheet->getActiveSheet()->fromArray($date_array,NULL,'A1');
        $spreadSheet->getActiveSheet()->fromArray($date_array_headers1,NULL,'A1');
        $spreadSheet->getActiveSheet()->fromArray($date_array_inputs1,NULL,'D1');
        $spreadSheet->getActiveSheet()->fromArray($export_filter_headers1,NULL,'A2');
        $spreadSheet->getActiveSheet()->fromArray($export_filter_inputs1,NULL,'D2');
        $spreadSheet->getActiveSheet()->fromArray($export_array,NULL,'A4');
        $highestRow = $spreadSheet->getActiveSheet()->getHighestDataRow();
        $spreadSheet->getActiveSheet()->getStyle('A4:F'.$highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A4:F'.$highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $spreadSheet->getActiveSheet()->getStyle('A4:F'.$highestRow)->applyFromArray($borderStyleArray);
        $spreadSheet->getActiveSheet()->getStyle('A4:F4')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFF9000');
        $spreadSheet->getActiveSheet()->getStyle('A4:F4')->getFont()->setBold(true);
        $spreadSheet->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setWrapText(true);
        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A1:F'.$highestRow)
            ->getBorders()
            ->getOutline()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00000000'));
        $Excel_writer = new Xls($spreadSheet);
        $Excel_writer->save('ExportedDataStats.xls');
    }

    public function GetDownloadStats()
    {
        $file= public_path(). "/ExportedDataStats.xls";

        $headers = array(
            'Content-Type: application/xls',
        );
        return Response::download($file, 'ExportedDataStats.xls', $headers);
    }
}

