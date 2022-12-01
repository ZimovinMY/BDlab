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
    public function ExportReport(Request $request)
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
        $this->ExportFunction($data_array,$filter_array);
    }

    public function ExportFunction($export_array,$export_filter){
        $spreadSheet = new Spreadsheet();
        $spreadSheet->createSheet();
        $spreadSheet->setActiveSheetIndex(0)->setTitle('Фильтры')->getDefaultColumnDimension()->setWidth(25);
        $spreadSheet->setActiveSheetIndex(0)->fromArray($export_filter);
        $spreadSheet->setActiveSheetIndex(1)->setTitle('Данные')->getDefaultColumnDimension()->setWidth(20);
        $spreadSheet->setActiveSheetIndex(1)->fromArray($export_array);
        $Excel_writer = new Xls($spreadSheet);
        $Excel_writer->save('ExportedData.xls');
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
        $this->ExportFunctionStats($data_array,$filter_array);
    }

    public function ExportFunctionStats($export_array,$export_filter){
        $spreadSheet = new Spreadsheet();
        $spreadSheet->createSheet();
        $spreadSheet->setActiveSheetIndex(0)->setTitle('Фильтры')->getDefaultColumnDimension()->setWidth(15);
        $spreadSheet->setActiveSheetIndex(0)->fromArray($export_filter);
        $spreadSheet->setActiveSheetIndex(1)->setTitle('Данные')->getDefaultColumnDimension()->setWidth(11);
        $spreadSheet->setActiveSheetIndex(1)->fromArray($export_array);
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

