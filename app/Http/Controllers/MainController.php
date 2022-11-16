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
        $date_torg_min = $request->input('date_torg_min');
        $date_torg_max = $request->input('date_torg_max');
        $price_min = $request->input('price_min');
        $price_max = $request->input('price_max');
        $numb_sales_min = $request->input('numb_sales_min');
        $numb_sales_max = $request->input('numb_sales_max');
        $query = DB::select("
        select MAX(quotation) AS MAX_quotation, MIN(quotation) AS MIN_quotation,MAX(num_contr) AS MAX_num_contr, MIN(num_contr) AS MIN_num_contr,MAX(torg_date) AS MAX_torg_date, MIN(torg_date) AS MIN_torg_date  FROM(
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
        where torg_date >= '$date_torg_min' and torg_date <= '$date_torg_max' and quotation >= '$price_min' and quotation <= '$price_max' and num_contr >= '$numb_sales_min' and num_contr <= '$numb_sales_max'
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
        $this->ExportFunction($data_array);
    }

    public function ExportFunction($export_array){
        $spreadSheet = new Spreadsheet();
        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
        $spreadSheet->getActiveSheet()->fromArray($export_array);

        $Excel_writer = new Xls($spreadSheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="ExportedData.xls"');
        header('Cache-Control: max-age=0');
        $Excel_writer->save('ExportedData.xls');
    }

    public function GetDownload()
    {
        //PDF file is stored under project/public/download/info.pdf
        $file= public_path(). "/ExportedData.xls";

        $headers = array(
            'Content-Type: application/xls',
        );
        return Response::download($file, 'ExportedData.xls', $headers);
    }
}

