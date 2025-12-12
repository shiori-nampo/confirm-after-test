<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Contact;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('contact',compact('categories'));
    }

    public function confirm(ContactRequest $request)
    {
        $contacts = $request->all();
        $category = Category::find($request->category_id);

        return view('confirm' ,compact('contacts','category'));
    }

    public function store(ContactRequest $request)
    {
        if($request->has('back')) {
            return redirect('/')->withInput();
        }

        $request['tell'] = $request->tel_1 . $request->tel_2 . $request->tel_3;
        Contact::create(
            $request->only([
                'category_id',
                'first_name',
                'last_name',
                'gender',
                'email',
                'tell',
                'address',
                'building',
                'detail'
            ])
        );
        return view('thanks');
    }

    public function admin()
    {
        $contacts = Contact::with('category')->paginate(7);
        $categories = Category::all();
        $csvData = Contact::all();

        return view('admin',compact('contacts','categories','csvData'));
    }

    public function search(Request $request)
    {
        if ($request->has('reset')) {
            return redirect('/admin')->withInput();
    }

        $query = Contact::query();

        $query = $this->getSearchQuery($request,$query);

        $contacts = $query->paginate(7);

        $csvData = $query->get();

        $categories = Category::all();

        return view('admin',compact('contacts','categories','csvData'));
    }

    private function getSearchQuery($request, $query)
    {
        $keyword = $request->keyword;

        if(!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name','like',"%{$keyword}%")
                ->orWhere('last_name','like', "%{$keyword}%")
                ->orWhere('email','like', "%{$keyword}%")
                ->orWhereRaw("CONCAT(last_name, first_name) LIKE ?", ["%{$keyword}%"])
                ->orWhereRaw("CONCAT(last_name,' ',first_name) LIKE ?", ["%{$keyword}%"]);
            });

        }

        if (!empty($request->gender)) {
            $query->where('gender', '=', $request->gender);
        }//gender入力時のみ実行。完全一致検索


        if (!empty($request->category_id)) {
            $query->where('category_id', '=', $request->category_id);
        }//カテゴリー選択のみ実行。完全一致検索

        if (!empty($request->date)) {
            $query->WhereDate('created_at', '=', $request->date);
        }//登録日が一致するデータのみ取得。WhereDateは時刻を無視して日付だけで比較するメソッド

        return $query;
    }//最後にクエリを返す

    public function export(Request $request)
    {
        $query = Contact::query();
        //query=命令するための処理
        //Contactテーブルに対して検索できるクエリの箱を作る
        $query = $this->getSearchQuery($request, $query);
        //検索条件（名前やメールで絞るなど）を追加。検索条件は後付け
        $csvData = $query->get()->toArray();
        //$queryを実行してデータを取得（get()で食ろ実行して結果を全部取ってくる）toArray()で配列に変換（CSV出力のため）
        $csvHeader = [
            'id','category_id','first_name','last_name','gender','email','address','building','detail','created_at','updated_at'
        ];//CSVの1行目（タイトル行）、配列形式で必要なカラム名をセットしてる

        //LaravelのStreamedResponseを使ってdbの内容をCSVファイルとしてダウンロードできる処理
        $response = new StreamedResponse(function () use ($csvHeader,$csvData) {
            //function内が実際に書き出す処理
            $createCsvFile = fopen('php://output','w');
            //CSVを書き込むための出力先を書く（ファイルは作ってない。ブラウザに流してるだけ）
            mb_convert_variables('SJIS-win','UTF-8',$csvHeader);
            //Excelで文字化けしないように（UTF-8=Windows用Shift-JISに変換）CSVはExcelで開くことが多いため必須
            fputcsv($createCsvFile, $csvHeader);
            //ヘッダーを書き込む（CSVの1行目）（カラム名（id、名前、メールなど）
            foreach ($csvData as $csv) {
                $csv['created_at'] = Data::make($csv['created_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d H:i:s');
                $csv['updated_at'] = Data::make($csv['update_at'])->setTimezone('Asia/Tokyo')->format('Y/m/d H:i:s');
                fputcsv($createCsvFile, $csv);
            }//データループして1行ずつ書く。DBの日時はUTCの場合があるので日本時間に変換。ファイrに1行ずつ追加。

            fclose($createCsvFile);//outputの終了処理
        },200, [   //200はHTTPステータス（正常）
            'Content-Type' => 'text/csv',//CSVだよってブラウザに伝える
            'Content-Disposition' => 'attachment; filename="contacts.csv"',//ファイルとしてダウンロードさせる。(名前＝contact.csv)
        ]);

        return $response;
    }

     //お問い合わせ削除
    public function destroy(Request $request)
    {
        Contact::find($request->id)->delete();

        return redirect('/admin');
    }
}
