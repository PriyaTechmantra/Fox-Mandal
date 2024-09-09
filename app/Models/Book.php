<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','bookshelves_id', 'office_id', 'uid', 'qrcode', 'category_id',
        'title', 'author', 'publisher', 'edition', 'page', 'quantity',
    ];


	
    public function bookshelve()
    {
        return $this->belongsTo(Bookshelve::class, 'bookshelves_id');
    }
     public function user()
     {
         return $this->belongsTo(User::class);
     }
     
     public function office()
    {
        return $this->belongsTo(Office::class);
    }
     
      public function bookshelves()
     {
         return $this->belongsTo(Bookshelve::class);
     }
     
      public function category()
     {
         return $this->belongsTo(BookCategory::class);
     }

     
     public static function insertData($data, $successCount) {
        $id='';
        $value = DB::table('books')->where('title', $data['title'])->where('uid',$data['uid'])->get();
        if($value->count() == 0) {
            $id = DB::table('books')->insertGetId($data);
           
           //DB::table('users')->insert($data);
            $successCount++;
        $resp = [
            "successCount" => $successCount,
            "id" => $id,
        ];
        
         return $resp;
        } else {
            $resp = [
            "successCount" => 0,
            "id" => $value[0]->id,
            ];
            
            return $resp;
        }

        // return $count;

       
     }   

    public function issuebook()
    {
         return $this->hasMany(IssueBook::class);

    }
}
