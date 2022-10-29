<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function main() {/*функция возврата на главную*/
        return view('main');
    }

    public function ShowUnitedTable(){/*функция получения данных из 2х таблц*/
        $query = DB::select("SELECT
        torg_date, F_usd.kod, REPLACE(quotation, ',', '.') as quotation, num_contr, exec_data
        FROM F_usd
        INNER JOIN dataisp ON dataisp.kod = F_usd.kod");
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
}

